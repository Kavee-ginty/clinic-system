import { NextResponse } from "next/server";
import { supabaseAdmin, supabaseServer } from "@/lib/supabase/server";
import { ageFromDob } from "@/lib/format";
import type { JsonRecord, Role } from "@/lib/types";

type Actor = { email: string; roles: Role[] };
type Ctx = { params: Promise<{ action: string }> };

const today = () => new Date().toISOString().slice(0, 10);

function json(data: unknown, status = 200) {
  return NextResponse.json(data, { status });
}

function asText(value: unknown) {
  return String(value ?? "").trim();
}

async function body(request: Request) {
  return (await request.json().catch(() => ({}))) as JsonRecord;
}

async function actor(): Promise<Actor | NextResponse> {
  const supabase = await supabaseServer();
  const { data } = await supabase.auth.getUser();
  const email = data.user?.email?.toLowerCase();
  if (!email) return json({ success: false, error: "Not signed in" }, 401);

  const admin = supabaseAdmin();
  const { data: rows, error } = await admin.from("user_roles").select("role").eq("email", email);
  if (error) return json({ success: false, error: error.message }, 500);

  return { email, roles: (rows ?? []).map((row) => row.role as Role) };
}

function allowed(current: Actor, roles: Role[]) {
  return current.roles.some((role) => roles.includes(role));
}

function forbid() {
  return json({ success: false, error: "Not allowed for this role" }, 403);
}

async function getSettings(admin = supabaseAdmin()) {
  const { data, error } = await admin.from("settings").select("setting_key, setting_value");
  if (error) throw error;
  return Object.fromEntries((data ?? []).map((row) => [row.setting_key, row.setting_value]));
}

export async function GET(request: Request, ctx: Ctx) {
  const current = await actor();
  if (current instanceof NextResponse) return current;
  const { action } = await ctx.params;
  const url = new URL(request.url);
  const admin = supabaseAdmin();

  try {
    if (action === "me") return json({ email: current.email, roles: current.roles });
    if (action === "settings") return json(await getSettings(admin));

    if (action === "stats") {
      if (!allowed(current, ["admin", "doctor", "receptionist"])) return forbid();
      const day = today();
      const [patients, patientsToday, visitsToday, waiting] = await Promise.all([
        admin.from("patients").select("patient_id", { count: "exact", head: true }),
        admin.from("patients").select("patient_id", { count: "exact", head: true }).eq("registered_date", day),
        admin.from("visits").select("visit_id", { count: "exact", head: true }).gte("visit_date_time", `${day}T00:00:00`),
        admin.from("queue").select("queue_id", { count: "exact", head: true }).eq("queue_date", day).eq("status", "waiting")
      ]);
      return json({
        total_patients: patients.count ?? 0,
        patients_today: patientsToday.count ?? 0,
        visits_today: visitsToday.count ?? 0,
        waiting: waiting.count ?? 0
      });
    }

    if (action === "queue") {
      if (!allowed(current, ["admin", "doctor", "receptionist"])) return forbid();
      const { data, error } = await admin.rpc("get_today_queue");
      if (error) throw error;
      return json(data ?? []);
    }

    if (action === "patients") {
      if (!allowed(current, ["admin"])) return forbid();
      const { data, error } = await admin.from("patients").select("*").order("patient_id", { ascending: false }).limit(300);
      if (error) throw error;
      return json(data ?? []);
    }

    if (action === "search") {
      if (!allowed(current, ["admin", "doctor", "receptionist"])) return forbid();
      const q = asText(url.searchParams.get("q"));
      if (!q) return json([]);
      const { data, error } = await admin
        .from("patients")
        .select("*")
        .or(`first_name.ilike.%${q}%,last_name.ilike.%${q}%,phone.ilike.%${q}%,nic.ilike.%${q}%,patient_number.ilike.%${q}%`)
        .order("patient_id", { ascending: false })
        .limit(20);
      if (error) throw error;
      return json(data ?? []);
    }

    if (action === "patient") {
      if (!allowed(current, ["admin", "doctor", "receptionist"])) return forbid();
      const id = Number(url.searchParams.get("id"));
      const { data, error } = await admin.from("patients").select("*").eq("patient_id", id).single();
      if (error) throw error;
      return json(data);
    }

    if (action === "history") {
      if (!allowed(current, ["admin", "doctor", "receptionist"])) return forbid();
      const patientId = Number(url.searchParams.get("patient_id"));
      const { data: visits, error } = await admin
        .from("visits")
        .select("*")
        .eq("patient_id", patientId)
        .order("visit_date_time", { ascending: false });
      if (error) throw error;
      const ids = (visits ?? []).map((visit) => visit.visit_id);
      const { data: drugs, error: drugError } = ids.length
        ? await admin.from("visit_drugs").select("*").in("visit_id", ids)
        : { data: [], error: null };
      if (drugError) throw drugError;
      return json((visits ?? []).map((visit) => ({ ...visit, drugs: (drugs ?? []).filter((drug) => drug.visit_id === visit.visit_id) })));
    }

    if (action === "field-history") {
      if (!allowed(current, ["doctor"])) return forbid();
      const field = asText(url.searchParams.get("field"));
      if (!["complaint", "examination", "investigation", "diagnosis", "treatment", "referals", "notes"].includes(field)) return json([]);
      const { data, error } = await admin.from("visits").select(field).not(field, "is", null).limit(200);
      if (error) throw error;
      return json([...new Set((data ?? []).map((row) => asText((row as unknown as JsonRecord)[field])).filter(Boolean))].slice(0, 30));
    }

    if (action === "inventory") {
      if (!allowed(current, ["doctor", "receptionist"])) return forbid();
      const { data, error } = await admin.from("drugs").select("*").order("drug_name");
      if (error) throw error;
      return json(data ?? []);
    }

    if (action === "visit-drugs") {
      if (!allowed(current, ["doctor"])) return forbid();
      const patientId = Number(url.searchParams.get("patient_id"));
      const { data, error } = await admin
        .from("visit_drugs")
        .select("*, visits!inner(patient_id)")
        .eq("visits.patient_id", patientId)
        .order("visit_drug_id", { ascending: false })
        .limit(50);
      if (error) throw error;
      return json(data ?? []);
    }

    if (action === "visit-details") {
      if (!allowed(current, ["admin", "doctor", "receptionist"])) return forbid();
      const visitId = Number(url.searchParams.get("visit_id"));
      const { data: visit, error } = await admin.from("visits").select("*, patients(*)").eq("visit_id", visitId).single();
      if (error) throw error;
      const { data: drugs, error: drugError } = await admin.from("visit_drugs").select("*").eq("visit_id", visitId);
      if (drugError) throw drugError;
      return json({ success: true, visit, drugs: drugs ?? [], settings: await getSettings(admin) });
    }

    return json({ success: false, error: "Unknown action" }, 404);
  } catch (error) {
    return json({ success: false, error: error instanceof Error ? error.message : "Request failed" }, 500);
  }
}

export async function POST(request: Request, ctx: Ctx) {
  const current = await actor();
  if (current instanceof NextResponse) return current;
  const { action } = await ctx.params;
  const data = await body(request);
  const admin = supabaseAdmin();

  try {
    if (action === "add-patient") {
      if (!allowed(current, ["admin", "receptionist"])) return forbid();
      const dob = asText(data.dob).replaceAll("/", "-");
      const payload = {
        first_name: asText(data.first_name),
        last_name: asText(data.last_name),
        dob,
        gender: asText(data.gender),
        phone: asText(data.phone),
        address: asText(data.address),
        nic: asText(data.nic),
        age: ageFromDob(dob),
        registered_date: today()
      };
      if (!payload.first_name || !payload.last_name || !payload.dob) return json({ success: false, error: "Required fields missing" }, 400);

      let duplicateQuery = admin
        .from("patients")
        .select("patient_id, patient_number")
        .ilike("first_name", payload.first_name)
        .ilike("last_name", payload.last_name)
        .eq("dob", payload.dob)
        .limit(1);
      duplicateQuery = payload.phone ? duplicateQuery.eq("phone", payload.phone) : duplicateQuery.eq("gender", payload.gender);
      if (payload.nic) duplicateQuery = duplicateQuery.eq("nic", payload.nic);
      const { data: duplicate, error: duplicateError } = await duplicateQuery.maybeSingle();
      if (duplicateError) throw duplicateError;
      if (duplicate) {
        return json({ success: false, error: `Patient already exists as ${duplicate.patient_number}. Search and add that patient to the queue.` }, 409);
      }

      const { data: patient, error } = await admin.from("patients").insert(payload).select("patient_id").single();
      if (error) throw error;
      const patient_number = `PT-${String(patient.patient_id).padStart(5, "0")}`;
      await admin.from("patients").update({ patient_number }).eq("patient_id", patient.patient_id);
      return json({ success: true, patient_id: patient.patient_id, patient_number });
    }

    if (action === "update-patient") {
      if (!allowed(current, ["admin", "receptionist"])) return forbid();
      const patientId = Number(data.patient_id);
      const dob = asText(data.dob).replaceAll("/", "-");
      const { error } = await admin
        .from("patients")
        .update({
          first_name: asText(data.first_name),
          last_name: asText(data.last_name),
          dob,
          gender: asText(data.gender),
          phone: asText(data.phone),
          address: asText(data.address),
          nic: asText(data.nic),
          age: ageFromDob(dob)
        })
        .eq("patient_id", patientId);
      if (error) throw error;
      return json({ success: true });
    }

    if (action === "delete-patient") {
      if (!allowed(current, ["admin"])) return forbid();
      const { error } = await admin.from("patients").delete().eq("patient_id", Number(data.patient_id));
      if (error) throw error;
      return json({ success: true });
    }

    if (action === "add-queue") {
      if (!allowed(current, ["admin", "receptionist"])) return forbid();
      const patientId = Number(data.patient_id);
      const day = today();
      const { data: existing } = await admin
        .from("queue")
        .select("queue_number")
        .eq("patient_id", patientId)
        .eq("queue_date", day)
        .in("status", ["waiting", "with_doctor"])
        .maybeSingle();
      if (existing) return json({ success: false, error: `Patient is already in today's active queue as token ${existing.queue_number}` }, 400);

      const { data: rows, error: maxError } = await admin.from("queue").select("queue_number").eq("queue_date", day).order("queue_number", { ascending: false }).limit(1);
      if (maxError) throw maxError;
      const queueNumber = ((rows?.[0]?.queue_number as number | undefined) ?? 0) + 1;
      const { error } = await admin.from("queue").insert({ patient_id: patientId, queue_number: queueNumber, queue_date: day, status: "waiting" });
      if (error) throw error;
      return json({ success: true, queue_number: queueNumber });
    }

    if (action === "update-queue") {
      if (!allowed(current, ["admin", "doctor"])) return forbid();
      const status = asText(data.status);
      if (!["waiting", "with_doctor", "completed"].includes(status)) return json({ success: false, error: "Invalid status" }, 400);
      const { error } = await admin.from("queue").update({ status }).eq("queue_id", Number(data.queue_id));
      if (error) throw error;
      return json({ success: true });
    }

    if (action === "remove-queue") {
      if (!allowed(current, ["admin", "receptionist"])) return forbid();
      const queueId = Number(data.queue_id);
      const { data: visit } = await admin.from("visits").select("visit_id").eq("queue_id", queueId).maybeSingle();
      if (visit) return json({ success: false, error: "Cannot remove after a visit is created" }, 400);
      const { error } = await admin.from("queue").delete().eq("queue_id", queueId);
      if (error) throw error;
      return json({ success: true });
    }

    if (action === "reset-queue") {
      if (!allowed(current, ["admin"])) return forbid();
      const { error } = await admin.from("queue").delete().eq("queue_date", today());
      if (error) throw error;
      return json({ success: true });
    }

    if (action === "mark-paid") {
      if (!allowed(current, ["admin", "receptionist"])) return forbid();
      const { error } = await admin.from("visits").update({ is_paid: true }).eq("visit_id", Number(data.visit_id));
      if (error) throw error;
      return json({ success: true });
    }

    if (action === "add-visit") {
      if (!allowed(current, ["doctor"])) return forbid();
      const queueId = Number(data.queue_id);
      const patientId = Number(data.patient_id);
      const { data: existing } = await admin.from("visits").select("visit_id").eq("queue_id", queueId).maybeSingle();
      if (existing) return json({ success: true, visit_id: existing.visit_id });

      const settings = await getSettings(admin);
      const visitFee = Number(settings.visit_fee ?? 500);
      const drugs = Array.isArray(data.drugs) ? (data.drugs as JsonRecord[]) : [];
      const drugTotal = drugs.reduce((sum, drug) => sum + Number(drug.cost ?? 0), 0);
      const { data: visit, error } = await admin
        .from("visits")
        .insert({
          patient_id: patientId,
          queue_id: queueId,
          complaint: asText(data.complaint),
          examination: asText(data.examination),
          investigation: asText(data.investigation),
          diagnosis: asText(data.diagnosis),
          treatment: asText(data.treatment),
          referals: asText(data.referals),
          notes: asText(data.notes),
          visit_fee: visitFee,
          total_bill: visitFee + drugTotal
        })
        .select("visit_id")
        .single();
      if (error) throw error;
      await saveVisitDrugs(admin, visit.visit_id, drugs);
      await admin.from("queue").update({ status: "completed" }).eq("queue_id", queueId);
      return json({ success: true, visit_id: visit.visit_id });
    }

    if (action === "save-billing") {
      if (!allowed(current, ["doctor"])) return forbid();
      const visitId = Number(data.visit_id);
      const drugs = Array.isArray(data.drugs) ? (data.drugs as JsonRecord[]) : [];
      const { error } = await admin.from("visits").update({ visit_fee: Number(data.visit_fee ?? 0), total_bill: Number(data.total_bill ?? 0) }).eq("visit_id", visitId);
      if (error) throw error;
      await saveVisitDrugs(admin, visitId, drugs);
      return json({ success: true });
    }

    if (action === "inventory") {
      if (!allowed(current, ["doctor", "receptionist"])) return forbid();
      const mode = asText(data.mode);
      const drugId = Number(data.drug_id);
      if (mode === "add") {
        const { error } = await admin.from("drugs").insert({
          drug_name: asText(data.drug_name),
          dose: asText(data.dose),
          batch_number: asText(data.batch_number),
          quantity: Number(data.quantity ?? 0),
          unit_price: Number(data.unit_price ?? 0)
        });
        if (error) throw error;
      } else if (mode === "stock") {
        const { data: drug, error: readError } = await admin.from("drugs").select("quantity").eq("drug_id", drugId).single();
        if (readError) throw readError;
        const { error } = await admin.from("drugs").update({ quantity: Number(drug.quantity ?? 0) + Number(data.add_quantity ?? 0) }).eq("drug_id", drugId);
        if (error) throw error;
      } else if (mode === "price") {
        const { error } = await admin.from("drugs").update({ unit_price: Number(data.price ?? 0) }).eq("drug_id", drugId);
        if (error) throw error;
      } else if (mode === "edit") {
        const { error } = await admin.from("drugs").update({ drug_name: asText(data.drug_name), dose: asText(data.dose), batch_number: asText(data.batch_number) }).eq("drug_id", drugId);
        if (error) throw error;
      } else if (mode === "delete") {
        const { error } = await admin.from("drugs").delete().eq("drug_id", drugId);
        if (error) throw error;
      }
      return json({ success: true });
    }

    if (action === "settings") {
      if (!allowed(current, ["admin", "doctor"])) return forbid();
      const keys = ["clinic_name", "doctor_name", "doctor_qualifications", "doctor_slmc", "clinic_address", "clinic_phone", "clinic_email", "print_page_size", "print_text_size", "logo_width", "visit_fee"];
      await Promise.all(
        keys
          .filter((key) => data[key] !== undefined)
          .map((key) => admin.from("settings").upsert({ setting_key: key, setting_value: asText(data[key]) }))
      );
      return json({ success: true });
    }

    return json({ success: false, error: "Unknown action" }, 404);
  } catch (error) {
    return json({ success: false, error: error instanceof Error ? error.message : "Request failed" }, 500);
  }
}

async function saveVisitDrugs(admin: ReturnType<typeof supabaseAdmin>, visitId: number, drugs: JsonRecord[]) {
  for (const drug of drugs) {
    const qty = Number(drug.qty ?? drug.quantity ?? 0);
    const drugId = drug.id || drug.drug_id ? Number(drug.id ?? drug.drug_id) : null;
    const row = {
      visit_id: visitId,
      drug_id: drugId,
      drug_name: asText(drug.name ?? drug.drug_name),
      quantity: qty,
      total_cost: Number(drug.cost ?? drug.total_cost ?? 0),
      frequency: asText(drug.frequency),
      dose: asText(drug.dose),
      duration: asText(drug.duration)
    };
    const { error } = await admin.from("visit_drugs").insert(row);
    if (error) throw error;

    if (drugId) {
      const { data: stock, error: readError } = await admin.from("drugs").select("quantity").eq("drug_id", drugId).single();
      if (readError) throw readError;
      const { error: updateError } = await admin.from("drugs").update({ quantity: Number(stock.quantity ?? 0) - qty }).eq("drug_id", drugId);
      if (updateError) throw updateError;
    }
  }
}
