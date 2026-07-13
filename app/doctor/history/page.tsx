import { ClinicShell } from "@/components/ClinicShell";
import { requireRole } from "@/lib/auth";
import { supabaseAdmin } from "@/lib/supabase/server";
import { displayDate, money } from "@/lib/format";

export default async function History({ searchParams }: { searchParams: Promise<{ patient_id?: string }> }) {
  const { email, roles } = await requireRole(["admin", "doctor", "receptionist"]);
  const role = roles.includes("doctor") ? "doctor" : roles.includes("admin") ? "admin" : "receptionist";
  const patientId = Number((await searchParams).patient_id);
  const admin = supabaseAdmin();
  const { data: patient } = await admin.from("patients").select("*").eq("patient_id", patientId).single();
  const { data: visits } = await admin.from("visits").select("*").eq("patient_id", patientId).order("visit_date_time", { ascending: false });

  return (
    <ClinicShell role={role} email={email}>
      <h2 className="text-3xl font-black text-gray-800">Patient History</h2>
      <p className="mb-6 font-semibold text-gray-500">{patient ? `${patient.first_name} ${patient.last_name} • ${patient.patient_number}` : "Patient not found"}</p>
      <div className="space-y-4">
        {(visits ?? []).map((visit) => (
          <article key={visit.visit_id} className="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div className="mb-3 flex flex-wrap justify-between gap-2">
              <b>{displayDate(String(visit.visit_date_time).slice(0, 10))}</b>
              <span className="text-sm font-bold text-gray-500">{money(visit.total_bill)}</span>
            </div>
            <p><b>Complaint:</b> {visit.complaint}</p>
            <p><b>Diagnosis:</b> {visit.diagnosis}</p>
            <p><b>Treatment:</b> {visit.treatment}</p>
            <a href={`/doctor/print-report?visit_id=${visit.visit_id}`} target="_blank" className="mt-3 inline-block rounded bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700">Print Record</a>
          </article>
        ))}
      </div>
    </ClinicShell>
  );
}
