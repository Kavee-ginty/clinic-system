"use client";

import { FormEvent, useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { money } from "@/lib/format";
import type { Drug } from "@/lib/types";

export function InventoryClient() {
  const [drugs, setDrugs] = useState<Drug[]>([]);
  const [message, setMessage] = useState("");

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
        <h2 className="text-3xl font-black text-gray-800">Drug Inventory</h2>
        <p className="mt-1 font-semibold text-gray-500">Shared stock for doctor and receptionist</p>
      </div>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <form onSubmit={add} className="mb-6 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div className="grid gap-3 md:grid-cols-5">
          <input name="drug_name" required placeholder="Drug Name *" className="rounded-lg border-2 border-gray-100 p-3 font-bold" />
          <input name="dose" placeholder="Dose" className="rounded-lg border-2 border-gray-100 p-3 font-bold" />
          <input name="batch_number" placeholder="Batch Number" className="rounded-lg border-2 border-gray-100 p-3 font-bold" />
          <input name="quantity" required type="number" placeholder="Quantity *" className="rounded-lg border-2 border-gray-100 p-3 font-bold" />
          <input name="unit_price" required type="number" step="0.01" placeholder="Unit Price *" className="rounded-lg border-2 border-gray-100 p-3 font-bold" />
        </div>
        <button className="mt-4 rounded-lg bg-teal-600 px-5 py-2 font-bold text-white hover:bg-teal-700">Add Stock Item</button>
      </form>
      <div className="overflow-x-auto rounded-xl border border-gray-100 bg-white shadow-sm">
        <table className="w-full text-left">
          <thead className="border-b bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th className="p-3">Drug</th><th className="p-3">Batch</th><th className="p-3">Qty</th><th className="p-3">Price</th><th className="p-3 text-right">Actions</th></tr>
          </thead>
          <tbody>
            {drugs.map((drug) => (
              <tr key={drug.drug_id} className="border-b">
                <td className="p-3"><b>{drug.drug_name}</b><p className="text-xs text-gray-500">{drug.dose}</p></td>
                <td className="p-3">{drug.batch_number}</td>
                <td className="p-3 font-black">{drug.quantity}</td>
                <td className="p-3">{money(drug.unit_price)}</td>
                <td className="space-x-2 p-3 text-right">
                  <button onClick={() => update(drug.drug_id, "stock", { add_quantity: Number(prompt("Add quantity", "0") ?? 0) })} className="rounded bg-blue-50 px-3 py-1 text-sm font-bold text-blue-700">Stock</button>
                  <button onClick={() => update(drug.drug_id, "price", { price: Number(prompt("New price", String(drug.unit_price)) ?? drug.unit_price) })} className="rounded bg-gray-50 px-3 py-1 text-sm font-bold text-gray-700">Price</button>
                  <button onClick={() => confirm("Delete drug?") && update(drug.drug_id, "delete", {})} className="rounded bg-red-50 px-3 py-1 text-sm font-bold text-red-700">Delete</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
