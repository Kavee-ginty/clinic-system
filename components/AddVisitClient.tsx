"use client";

import { FormEvent, useEffect, useMemo, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { money } from "@/lib/format";
import type { Drug, Patient } from "@/lib/types";

type DrugLine = { id: number | ""; name: string; qty: number; cost: number; frequency: string; dose: string; duration: string };
type HistoryDrug = { drug_id?: number | null; drug_name?: string; quantity?: number; total_cost?: number; frequency?: string; dose?: string; duration?: string };

export function AddVisitClient({ patientId, queueId }: { patientId: number; queueId: number }) {
  const [patient, setPatient] = useState<Patient | null>(null);
  const [inventory, setInventory] = useState<Drug[]>([]);
  const [historyDrugs, setHistoryDrugs] = useState<HistoryDrug[]>([]);
  const [lines, setLines] = useState<DrugLine[]>([]);
  const [message, setMessage] = useState("");
  const [visitId, setVisitId] = useState<number | null>(null);
  const [prescriptionOpen, setPrescriptionOpen] = useState(false);
  const [treatmentText, setTreatmentText] = useState("");

  useEffect(() => {
    Promise.all([apiGet<Patient>("patient", { id: patientId }), apiGet<Drug[]>("inventory"), apiGet<HistoryDrug[]>("visit-drugs", { patient_id: patientId })])
      .then(([nextPatient, nextInventory, nextHistoryDrugs]) => {
        setPatient(nextPatient);
        setInventory(nextInventory);
        setHistoryDrugs(nextHistoryDrugs);
      })
      .catch((error) => setMessage(error.message));
  }, [patientId]);

  const drugTotal = useMemo(() => lines.reduce((sum, line) => sum + Number(line.cost || 0), 0), [lines]);

  function addDrugLine() {
    setLines([...lines, { id: "", name: "", qty: 1, cost: 0, frequency: "", dose: "", duration: "" }]);
  }

  function openPrescriptionTable() {
    if (lines.length === 0) addDrugLine();
    setPrescriptionOpen(true);
  }

  function setLine(index: number, patch: Partial<DrugLine>) {
    setLines((old) => old.map((line, i) => i === index ? { ...line, ...patch } : line));
  }

  function chooseDrug(index: number, id: string) {
    const drug = inventory.find((item) => item.drug_id === Number(id));
    if (!drug) return;
    setLine(index, { id: drug.drug_id, name: drug.drug_name, dose: drug.dose ?? "", qty: 1, cost: Number(drug.unit_price ?? 0) });
  }

  function setQty(index: number, qty: number) {
    const line = lines[index];
    const drug = inventory.find((item) => item.drug_id === Number(line.id));
    setLine(index, { qty, cost: drug ? Number(drug.unit_price ?? 0) * qty : line.cost });
  }

  function addHistoryDrug(drug: HistoryDrug) {
    setLines([
      ...lines,
      {
        id: drug.drug_id ?? "",
        name: drug.drug_name ?? "",
        qty: Number(drug.quantity ?? 1),
        cost: Number(drug.total_cost ?? 0),
        frequency: drug.frequency ?? "",
        dose: drug.dose ?? "",
        duration: drug.duration ?? ""
      }
    ]);
  }

  function appendPrescriptionToTreatment() {
    const summary = lines
      .filter((line) => line.name)
      .map((line) => `${line.name}${line.dose ? ` ${line.dose}` : ""} - ${line.frequency || "-"} - ${line.duration || "-"} x ${line.qty}`)
      .join("\n");
    if (summary) setTreatmentText((old) => [old.trim(), summary].filter(Boolean).join("\n"));
    setPrescriptionOpen(false);
  }

  async function save(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = Object.fromEntries(new FormData(event.currentTarget));
    const saved = await apiPost<{ visit_id: number }>("add-visit", {
      ...form,
      patient_id: patientId,
      queue_id: queueId,
      drugs: lines.filter((line) => line.name && line.qty > 0)
    });
    setVisitId(saved.visit_id);
    setMessage("Visit saved and queue completed");
    window.open(`/doctor/print-report?visit_id=${saved.visit_id}`, "_blank");
  }

  return (
    <div className="mx-auto max-w-4xl px-4 py-10">
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}

      <form onSubmit={save} className="rounded-xl border-t-4 border-teal-400 bg-white p-6 shadow-lg md:p-8">
        <section className="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-lg bg-gray-100 p-4">
          <div>
            <h2 className="text-xl font-black uppercase text-gray-800">
              {patient ? `${patient.first_name} ${patient.last_name}` : "Loading Patient"}
              {patient?.patient_number && <span className="ml-1 text-sm font-black normal-case text-gray-600">({patient.patient_number})</span>}
            </h2>
            {patient && (
              <p className="mt-1 text-sm font-bold text-gray-600">
                Age: {patient.age ?? "N/A"} | NIC: {patient.nic || "N/A"} | Gender: {patient.gender} | DOB: {patient.dob.replaceAll("-", "/")}
                <br />
                <span className="font-semibold">Phone: {patient.phone || "N/A"}</span>
              </p>
            )}
          </div>
          {patient && (
            <a href={`/doctor/history?patient_id=${patient.patient_id}`} target="_blank" className="rounded bg-blue-100 px-5 py-3 font-black text-blue-700 hover:bg-blue-200">
              View History
            </a>
          )}
        </section>

        <div className="grid gap-6 md:grid-cols-2">
          <Field name="complaint" label="Presenting Complaint *" required />
          <Field name="examination" label="Examination Findings" />
          <Field name="investigation" label="Investigations" />
          <Field name="diagnosis" label="Diagnosis *" required />
        </div>

        <section className="mt-6">
          <div className="mb-3 flex flex-wrap items-center justify-between gap-2">
            <label className="text-sm font-black text-gray-700">
              Treatment / Prescription <span className="text-red-500">*</span>
            </label>
            <div className="flex flex-wrap gap-2">
              <button type="button" className="rounded border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-black text-blue-700">Save Template</button>
              <button type="button" className="rounded border border-gray-200 bg-white px-4 py-2 text-sm font-black text-gray-600">Templates</button>
              <button type="button" onClick={openPrescriptionTable} className="rounded border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-black text-teal-700">+ Open Prescription Table</button>
            </div>
          </div>
          <textarea name="treatment" required rows={4} value={treatmentText} onChange={(event) => setTreatmentText(event.target.value)} className="w-full rounded-lg border border-gray-200 p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" />
        </section>

        <div className="mt-6 grid gap-6 md:grid-cols-2">
          <Field name="referals" label="Referrals" />
          <Field name="notes" label="Doctor's Notes" />
        </div>

        <div className="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 pt-5">
          <p className="font-black text-gray-500">
            Total Table Drugs: <span className="rounded border border-teal-300 bg-teal-50 px-2 py-1 text-teal-700">{lines.length}</span>
          </p>
          <div className="flex flex-wrap gap-3">
            {visitId && <a href={`/doctor/print-report?visit_id=${visitId}`} target="_blank" className="rounded-lg bg-gray-700 px-6 py-3 font-black text-white shadow hover:bg-gray-800">Complete & Preview</a>}
            <button className="rounded-lg bg-teal-600 px-6 py-3 font-black text-white shadow hover:bg-teal-700">Save & Direct Print</button>
          </div>
        </div>
      </form>

      {prescriptionOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 backdrop-blur-sm">
          <section className="flex max-h-[88vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
            <header className="flex items-center justify-between border-b border-gray-100 px-6 py-5">
              <h3 className="text-2xl font-black text-gray-900">Add Drugs to Prescription</h3>
              <button type="button" onClick={() => setPrescriptionOpen(false)} className="text-3xl font-bold text-gray-400 hover:text-gray-700">&times;</button>
            </header>

            <div className="min-h-[420px] overflow-auto p-6">
              <div className="grid grid-cols-[minmax(220px,1.6fr)_170px_360px_90px_90px_70px] gap-4 bg-gray-100 px-3 py-3 text-sm font-black text-gray-800">
                <span>Drug Name</span>
                <span>Dose</span>
                <span>Frequency</span>
                <span>Duration</span>
                <span>Total Qty</span>
                <span>Action</span>
              </div>

              <div className="space-y-2 border-b border-blue-100 pb-4 pt-3">
                {lines.map((line, index) => (
                  <div key={index} className="grid grid-cols-[minmax(220px,1.6fr)_170px_360px_90px_90px_70px] gap-4">
                    <select value={line.id} onChange={(event) => chooseDrug(index, event.target.value)} className="rounded border border-gray-300 p-3 text-sm font-bold">
                      <option value="">Type drug name...</option>
                      {inventory.map((drug) => <option key={drug.drug_id} value={drug.drug_id}>{drug.drug_name} {drug.dose} ({drug.quantity})</option>)}
                    </select>
                    <input value={line.dose} onChange={(event) => setLine(index, { dose: event.target.value })} placeholder="Optional" className="rounded border border-gray-300 p-3 text-sm font-bold" />
                    <input value={line.frequency} onChange={(event) => setLine(index, { frequency: event.target.value })} placeholder="bd / tds / mane / nocte / custom" className="rounded border border-gray-300 p-3 text-sm font-bold" />
                    <input value={line.duration} onChange={(event) => setLine(index, { duration: event.target.value })} placeholder="Days" className="rounded border border-gray-300 p-3 text-sm font-bold" />
                    <input value={line.qty} type="number" min="1" onChange={(event) => setQty(index, Number(event.target.value))} placeholder="Qty" className="rounded border-2 border-teal-400 p-3 text-sm font-bold" />
                    <button type="button" onClick={() => setLines(lines.filter((_, i) => i !== index))} className="rounded bg-red-50 px-3 py-2 text-sm font-black text-red-700">Remove</button>
                  </div>
                ))}
                <button type="button" onClick={addDrugLine} className="rounded bg-teal-600 px-6 py-3 font-black text-white hover:bg-teal-700">+</button>
              </div>

              {historyDrugs.length > 0 && (
                <div className="mt-1 w-72 rounded-b bg-teal-50 shadow-xl">
                  <div className="border-b border-teal-100 px-3 py-2 text-xs font-black uppercase tracking-wide text-teal-900">From Patient History</div>
                  <div className="max-h-56 overflow-y-auto">
                    {historyDrugs.slice(0, 8).map((drug, index) => (
                      <button key={index} type="button" onClick={() => addHistoryDrug(drug)} className="block w-full border-b border-teal-100 px-3 py-3 text-left hover:bg-teal-100">
                        <span className="font-black text-gray-900">{drug.drug_name}</span>
                        <span className="ml-2 text-xs text-gray-700">[{drug.dose || "Dose"}] ({drug.frequency || "-"} - {drug.duration || "-"})</span>
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </div>

            <footer className="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-6 py-5">
              <p className="font-black text-gray-600">Est. Drug Cost: <span className="text-lg text-teal-700">{money(drugTotal)}</span></p>
              <button type="button" onClick={appendPrescriptionToTreatment} className="rounded-lg bg-gray-900 px-8 py-4 text-lg font-black text-white shadow-lg hover:bg-gray-800">
                Confirm & Append to Notes
              </button>
            </footer>
          </section>
        </div>
      )}
    </div>
  );
}

function Field({ name, label, required }: { name: string; label: string; required?: boolean }) {
  return (
    <label className="block">
      <span className="mb-2 block text-sm font-black text-gray-700">{label}</span>
      <textarea name={name} required={required} rows={4} className="w-full rounded-lg border border-gray-200 p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" />
    </label>
  );
}
