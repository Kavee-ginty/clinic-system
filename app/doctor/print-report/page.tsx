import { requireRole } from "@/lib/auth";
import { supabaseAdmin } from "@/lib/supabase/server";
import { displayDate, money } from "@/lib/format";

export default async function PrintReport({ searchParams }: { searchParams: Promise<{ visit_id?: string }> }) {
  await requireRole(["admin", "doctor", "receptionist"]);
  const visitId = Number((await searchParams).visit_id);
  const admin = supabaseAdmin();
  const { data: visit } = await admin.from("visits").select("*, patients(*)").eq("visit_id", visitId).single();
  const { data: drugs } = await admin.from("visit_drugs").select("*").eq("visit_id", visitId);
  const { data: settingsRows } = await admin.from("settings").select("setting_key, setting_value");
  const settings = Object.fromEntries((settingsRows ?? []).map((row) => [row.setting_key, row.setting_value]));

  return (
    <main className="mx-auto max-w-3xl bg-white p-8 text-gray-900 print:p-0">
      <button id="print-button" className="no-print mb-4 rounded bg-gray-800 px-4 py-2 font-bold text-white">Print</button>
      <header className="mb-6 border-b pb-4 text-center">
        <h1 className="text-3xl font-black">{settings.clinic_name ?? "Clinic System"}</h1>
        <p>{settings.clinic_address}</p>
        <p>{settings.clinic_phone}</p>
        <h2 className="mt-4 text-xl font-bold">{settings.doctor_name}</h2>
        <p>{settings.doctor_qualifications}</p>
      </header>
      {visit && (
        <>
          <section className="mb-6 grid grid-cols-2 gap-2 text-sm">
            <p><b>Patient:</b> {visit.patients.first_name} {visit.patients.last_name}</p>
            <p><b>Patient No:</b> {visit.patients.patient_number}</p>
            <p><b>Date:</b> {displayDate(String(visit.visit_date_time).slice(0, 10))}</p>
            <p><b>Total:</b> {money(visit.total_bill)}</p>
          </section>
          <section className="space-y-2">
            <p><b>Complaint:</b> {visit.complaint}</p>
            <p><b>Examination:</b> {visit.examination}</p>
            <p><b>Investigation:</b> {visit.investigation}</p>
            <p><b>Diagnosis:</b> {visit.diagnosis}</p>
            <p><b>Treatment:</b> {visit.treatment}</p>
            <p><b>Notes:</b> {visit.notes}</p>
          </section>
          <section className="mt-6">
            <h3 className="mb-2 text-lg font-black">Prescription</h3>
            <table className="w-full border-collapse text-sm">
              <tbody>
                {(drugs ?? []).map((drug) => (
                  <tr key={drug.visit_drug_id} className="border-b">
                    <td className="py-2">{drug.drug_name}</td>
                    <td className="py-2">{drug.dose}</td>
                    <td className="py-2">{drug.frequency}</td>
                    <td className="py-2">{drug.duration}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
        </>
      )}
      <script dangerouslySetInnerHTML={{ __html: "document.getElementById('print-button')?.addEventListener('click',()=>window.print())" }} />
    </main>
  );
}
