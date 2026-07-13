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
    <main className="min-h-screen bg-white p-3 text-gray-700 sm:p-8 print:p-0">
      <div className="mx-auto flex max-w-4xl flex-col items-center justify-center gap-6 lg:flex-row lg:items-start lg:gap-8">
        <article className="min-h-[920px] w-full max-w-[560px] bg-white p-4 text-sm shadow-2xl sm:p-7 print:min-h-0 print:w-full print:shadow-none">
      <header className="mb-4 border-b-2 border-gray-500 pb-3">
        <div className="grid gap-4 text-center sm:grid-cols-[80px_1fr_120px] sm:items-center lg:grid-cols-[110px_1fr_150px]">
          <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-green-500 text-3xl font-black text-white lg:h-20 lg:w-20 lg:text-4xl">+</div>
          <div className="text-center">
            <h1 className="text-xl font-black">{settings.clinic_name ?? "Clinic System"}</h1>
            <p className="text-xs">{settings.clinic_address}</p>
            <p className="text-xs">{settings.clinic_phone}</p>
            <p className="text-xs">{settings.clinic_email}</p>
          </div>
          <div className="text-center text-xs font-bold sm:text-right">
            <p>{settings.doctor_name}</p>
            <p>{settings.doctor_qualifications}</p>
            <p>SLMC - {settings.doctor_slmc}</p>
          </div>
        </div>
      </header>
      {visit && (
        <>
          <section className="mb-4 grid gap-2 border-b pb-3 text-xs font-bold sm:grid-cols-2">
            <p>ID: {visit.visit_id} - {visit.patients.first_name} {visit.patients.last_name} ({visit.patients.gender?.[0]}) / {visit.patients.age} Y</p>
            <p className="sm:text-right">Date of Visit:<br />{displayDate(String(visit.visit_date_time).slice(0, 10))}</p>
          </section>
          <section className="grid gap-4 text-sm sm:grid-cols-2">
            <p><b>Complaints:</b><br />* {visit.complaint}</p>
            <p><b>Investigations:</b><br />* {visit.investigation}</p>
            <p><b>Examination Findings:</b><br />* {visit.examination}</p>
            <p><b>Diagnosis:</b><br />* {visit.diagnosis}</p>
          </section>
          <section className="mt-4">
            <table className="w-full border-collapse text-xs">
              <thead><tr className="border-y border-gray-500"><th className="py-2 text-left">Drug Name</th><th>Frequency</th><th>Duration</th><th className="text-right">Total Qty</th></tr></thead>
              <tbody>
                {(drugs ?? []).map((drug) => (
                  <tr key={drug.visit_drug_id} className="border-b">
                    <td className="py-2 font-bold">{drug.drug_name} {drug.dose}</td>
                    <td className="py-2 text-center">{drug.frequency}</td>
                    <td className="py-2 text-center">{drug.duration}</td>
                    <td className="py-2 text-right font-bold">{drug.quantity}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
          <section className="mt-6 space-y-5">
            <p><b>Referrals:</b><br />{visit.referals}</p>
            <p><b>Notes / Advice:</b><br />{visit.notes || visit.treatment}</p>
            <p className="text-right text-xs font-bold">Total: {money(visit.total_bill)}</p>
          </section>
        </>
      )}
        </article>
        <aside className="no-print grid w-full max-w-[560px] gap-3 sm:grid-cols-3 lg:sticky lg:top-8 lg:block lg:w-36 lg:space-y-5">
          <button id="print-button" className="block w-full rounded bg-gray-900 px-4 py-4 font-black text-white shadow">Preview Print</button>
          <button id="direct-print-button" className="block w-full rounded bg-teal-600 px-4 py-4 font-black text-white shadow">Direct Print</button>
          <button id="close-button" className="block w-full rounded bg-red-600 px-4 py-4 font-black text-white shadow">Close Tab</button>
        </aside>
      </div>
      <script dangerouslySetInnerHTML={{ __html: "document.getElementById('print-button')?.addEventListener('click',()=>window.print())" }} />
      <script dangerouslySetInnerHTML={{ __html: "document.getElementById('direct-print-button')?.addEventListener('click',()=>window.print());document.getElementById('close-button')?.addEventListener('click',()=>window.close())" }} />
    </main>
  );
}
