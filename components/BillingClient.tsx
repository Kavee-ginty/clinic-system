"use client";

import { FormEvent, useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { money } from "@/lib/format";

type VisitDetails = {
  visit: { visit_id: number; visit_fee: number; total_bill: number; patients: { first_name: string; last_name: string } };
  drugs: Array<{ visit_drug_id: number; drug_name: string; quantity: number; total_cost: number }>;
};

export function BillingClient({ visitId }: { visitId: number }) {
  const [details, setDetails] = useState<VisitDetails | null>(null);
  const [fee, setFee] = useState(0);
  const [message, setMessage] = useState("");

  useEffect(() => {
    apiGet<VisitDetails>("visit-details", { visit_id: visitId }).then((data) => {
      setDetails(data);
      setFee(Number(data.visit.visit_fee ?? 0));
    }).catch((error) => setMessage(error.message));
  }, [visitId]);

  async function save(event: FormEvent) {
    event.preventDefault();
    const drugTotal = details?.drugs.reduce((sum, drug) => sum + Number(drug.total_cost ?? 0), 0) ?? 0;
    await apiPost("save-billing", { visit_id: visitId, visit_fee: fee, total_bill: fee + drugTotal, drugs: [] });
    setMessage("Billing saved");
  }

  const drugTotal = details?.drugs.reduce((sum, drug) => sum + Number(drug.total_cost ?? 0), 0) ?? 0;

  return (
    <form onSubmit={save} className="max-w-3xl">
      <h2 className="text-3xl font-black text-gray-800">Billing</h2>
      <p className="mb-6 font-semibold text-gray-500">{details?.visit.patients.first_name} {details?.visit.patients.last_name}</p>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <section className="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <label className="block text-sm font-black uppercase text-gray-500">Visit Fee</label>
        <input value={fee} onChange={(event) => setFee(Number(event.target.value))} type="number" step="0.01" className="mt-2 w-full rounded-lg border p-3 font-bold" />
        <div className="mt-6 space-y-2">
          {(details?.drugs ?? []).map((drug) => (
            <div key={drug.visit_drug_id} className="flex justify-between border-b py-2 text-sm">
              <span>{drug.drug_name} x {drug.quantity}</span>
              <b>{money(drug.total_cost)}</b>
            </div>
          ))}
        </div>
        <p className="mt-6 text-right text-2xl font-black">Total: {money(fee + drugTotal)}</p>
        <button className="mt-4 rounded-lg bg-teal-700 px-6 py-3 font-black text-white">Save Billing</button>
      </section>
    </form>
  );
}
