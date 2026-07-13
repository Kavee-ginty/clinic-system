"use client";

import { FormEvent, useEffect, useMemo, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { money } from "@/lib/format";
import type { Drug, Patient } from "@/lib/types";

type DrugLine = { id: number | ""; name: string; qty: number; cost: number; frequency: string; dose: string; duration: string };

export function AddVisitClient({ patientId, queueId }: { patientId: number; queueId: number }) {
  const [patient, setPatient] = useState<Patient | null>(null);
  const [inventory, setInventory] = useState<Drug[]>([]);
  const [lines, setLines] = useState<DrugLine[]>([]);
  const [message, setMessage] = useState("");
  const [visitId, setVisitId] = useState<number | null>(null);

  useEffect(() => {
    Promise.all([apiGet<Patient>("patient", { id: patientId }), apiGet<Drug[]>("inventory")])
      .then(([nextPatient, nextInventory]) => {
        setPatient(nextPatient);
        setInventory(nextInventory);
      })
      .catch((error) => setMessage(error.message));
  }, [patientId]);

  const drugTotal = useMemo(() => lines.reduce((sum, line) => sum + Number(line.cost || 0), 0), [lines]);

  function addDrugLine() {
    setLines([...lines, { id: "", name: "", qty: 1, cost: 0, frequency: "", dose: "", duration: "" }]);
  }

  function setLine(index: number, patch: Partial<DrugLine>) {
    setLines((old) => old.map((line, i) => i === index ? { ...line, ...patch } : line));
  }

  function chooseDrug(index: number, id: string) {
    const drug = inventory.find((item) => item.drug_id === Number(id));
    if (!drug) return;
    setLine(index, { id: drug.drug_id, name: drug.drug_name, dose: drug.dose ?? "", qty: 1, cost: Number(drug.unit_price ?? 0) });
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
              <button type="button" onClick={addDrugLine} className="rounded border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-black text-teal-700">+ Open Prescription Table</button>
            </div>
          </div>
          <textarea name="treatment" required rows={4} className="w-full rounded-lg border border-gray-200 p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" />
        </section>

        {lines.length > 0 && (
          <section className="mt-5 rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div className="space-y-3">
              {lines.map((line, index) => (
                <div key={index} className="grid gap-2 rounded-lg border border-gray-100 bg-white p-3 md:grid-cols-[2fr_80px_100px_1fr_1fr_1fr_80px]">
                  <select value={line.id} onChange={(event) => chooseDrug(index, event.target.value)} className="rounded border p-2 text-sm font-semibold">
                    <option value="">Select drug</option>
                    {inventory.map((drug) => <option key={drug.drug_id} value={drug.drug_id}>{drug.drug_name} {drug.dose} ({drug.quantity})</option>)}
                  </select>
                  <input value={line.qty} type="number" min="1" onChange={(event) => setLine(index, { qty: Number(event.target.value) })} className="rounded border p-2 text-sm" />
                  <input value={line.cost} type="number" step="0.01" onChange={(event) => setLine(index, { cost: Number(event.target.value) })} className="rounded border p-2 text-sm" />
                  <input value={line.frequency} onChange={(event) => setLine(index, { frequency: event.target.value })} placeholder="Frequency" className="rounded border p-2 text-sm" />
                  <input value={line.dose} onChange={(event) => setLine(index, { dose: event.target.value })} placeholder="Dose" className="rounded border p-2 text-sm" />
                  <input value={line.duration} onChange={(event) => setLine(index, { duration: event.target.value })} placeholder="Duration" className="rounded border p-2 text-sm" />
                  <button type="button" onClick={() => setLines(lines.filter((_, i) => i !== index))} className="rounded bg-red-50 px-3 py-2 text-sm font-bold text-red-700">Remove</button>
                </div>
              ))}
            </div>
            <p className="mt-4 text-right text-lg font-black">Drug subtotal: {money(drugTotal)}</p>
          </section>
        )}

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
