"use client";

import { FormEvent, useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { money } from "@/lib/format";
import type { Drug, Role } from "@/lib/types";

export function InventoryClient({ role = "doctor" }: { role?: Role }) {
  const [drugs, setDrugs] = useState<Drug[]>([]);
  const [message, setMessage] = useState("");
  const accent = role === "receptionist" ? "blue" : "teal";
  const primary = accent === "blue" ? "bg-blue-600 hover:bg-blue-700" : "bg-teal-600 hover:bg-teal-700";

  async function refresh() {
    setDrugs(await apiGet<Drug[]>("inventory"));
  }

  useEffect(() => {
    refresh().catch((error) => setMessage(error.message));
  }, []);

  async function add(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const target = event.currentTarget;
    await apiPost("inventory", { mode: "add", ...Object.fromEntries(new FormData(target)) });
    target.reset();
    setMessage("Drug added");
    await refresh();
  }

  async function update(drug_id: number, mode: string, payload: object) {
    await apiPost("inventory", { mode, drug_id, ...payload });
    await refresh();
  }

  return (
    <div>
      <div className="mb-6">
        <h2 className="text-2xl font-black text-gray-900 sm:text-3xl">{role === "receptionist" ? "Drug Inventory Base" : "Clinic Inventory"}</h2>
        <p className="mt-1 font-bold text-gray-500">{role === "receptionist" ? "Manage stocks and pricing" : "Manage stocks and pricing centrally"}</p>
      </div>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <div className="grid gap-6 lg:grid-cols-[minmax(0,420px)_minmax(0,1fr)]">
        <form onSubmit={add} className="rounded-xl bg-white p-4 shadow-sm sm:p-6">
          <h3 className="mb-3 border-b pb-3 text-xl font-black">{role === "receptionist" ? "Add New Drug" : "Register Treatment"}</h3>
          <p className="mb-4 text-xs font-bold text-gray-500">For syrups and eye drops, include "syrup" or "drops" in the drug name so quantity is not auto-multiplied.</p>
          <div className="space-y-4">
            <input name="drug_name" required placeholder={role === "receptionist" ? "Drug Name *" : "Drug/Treatment Name *"} className="w-full rounded-lg border-2 border-gray-200 p-4 font-bold" />
            <input name="dose" placeholder="Dose (e.g., 500mg) *" className="w-full rounded-lg border-2 border-gray-200 p-4 font-bold" />
            <input name="batch_number" placeholder="Batch Number *" className="w-full rounded-lg border-2 border-gray-200 p-4 font-bold" />
            <input name="quantity" required type="number" placeholder="Initial Quantity *" className="w-full rounded-lg border-2 border-gray-200 p-4 font-bold" />
            <input name="unit_price" required type="number" step="0.01" placeholder="Unit Price (Rs.) *" className="w-full rounded-lg border-2 border-gray-200 p-4 font-bold" />
          </div>
          <button className={`mt-4 w-full rounded-lg px-5 py-4 font-black text-white shadow ${primary}`}>{role === "receptionist" ? "Register Drug" : "Add to Inventory"}</button>
        </form>

        <section className="overflow-hidden rounded-xl bg-white p-4 shadow-sm sm:p-6">
          <h3 className="mb-5 text-xl font-black">Stock Overview</h3>
          <div className="overflow-x-auto">
            <table className="min-w-[900px] w-full text-left">
              <thead className="bg-gray-50 text-xs uppercase text-gray-500">
                <tr><th className="p-3">Name & Dose & Batch</th><th className="p-3">Stock</th><th className="p-3">Unit Price</th><th className="p-3 text-right">Actions</th></tr>
              </thead>
              <tbody>
                {drugs.map((drug) => (
                  <tr key={drug.drug_id} className="border-b">
                    <td className="p-3"><b>{drug.drug_name}</b> <span className="text-xs font-black text-teal-600">({drug.dose})</span><p className="text-xs font-bold uppercase text-gray-500">Batch: {drug.batch_number}</p></td>
                    <td className="p-3 font-black text-green-600">{drug.quantity} units</td>
                    <td className="p-3 font-black">{money(drug.unit_price)}</td>
                    <td className="space-x-2 whitespace-nowrap p-3 text-right">
                      <button onClick={() => update(drug.drug_id, "stock", { add_quantity: Number(prompt("Add quantity", "0") ?? 0) })} className="rounded bg-green-100 px-3 py-2 text-xs font-black text-green-700">Add Stock</button>
                      <button onClick={() => update(drug.drug_id, "price", { price: Number(prompt("New price", String(drug.unit_price)) ?? drug.unit_price) })} className="rounded bg-blue-100 px-3 py-2 text-xs font-black text-blue-700">Edit Price</button>
                      <button onClick={() => update(drug.drug_id, "edit", { drug_name: prompt("Drug name", drug.drug_name) ?? drug.drug_name, dose: prompt("Dose", drug.dose ?? "") ?? drug.dose, batch_number: prompt("Batch", drug.batch_number ?? "") ?? drug.batch_number })} className="rounded bg-yellow-100 px-3 py-2 text-xs font-black text-yellow-700">Edit Details</button>
                      <button onClick={() => confirm("Delete drug?") && update(drug.drug_id, "delete", {})} className="rounded bg-red-100 px-3 py-2 text-xs font-black text-red-700">Delete</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  );
}
