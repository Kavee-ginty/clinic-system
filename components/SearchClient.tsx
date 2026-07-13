"use client";

import { useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { displayDate } from "@/lib/format";
import type { Patient, Role } from "@/lib/types";

export function SearchClient({ role }: { role: Role }) {
  const [q, setQ] = useState("");
  const [patients, setPatients] = useState<Patient[]>([]);
  const [message, setMessage] = useState("");

  async function search(value: string) {
    setQ(value);
    setPatients(value.trim() ? await apiGet<Patient[]>("search", { q: value }) : []);
  }

  async function addQueue(patient_id: number) {
    const saved = await apiPost<{ queue_number: number }>("add-queue", { patient_id });
    setMessage(`Queue token ${saved.queue_number} added`);
  }

  return (
    <div>
      <h2 className="mb-1 text-3xl font-black text-gray-800">Patient Search</h2>
      <p className="mb-6 font-semibold text-gray-500">Find records by name, phone, NIC, or patient number</p>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <input value={q} onChange={(event) => search(event.target.value)} autoFocus placeholder="Search patients..." className="mb-6 w-full rounded-xl border-2 border-gray-100 bg-white p-4 text-lg font-bold shadow-sm" />
      <div className="space-y-3">
        {patients.map((patient) => (
          <div key={patient.patient_id} className="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div>
              <h3 className="text-lg font-black">{patient.first_name} {patient.last_name}</h3>
              <p className="text-sm font-semibold text-gray-500">{patient.patient_number} • {patient.gender} • DOB {displayDate(patient.dob)} • {patient.phone}</p>
            </div>
            <div className="space-x-2">
              <a href={`/doctor/history?patient_id=${patient.patient_id}`} className="rounded-lg bg-gray-100 px-4 py-2 text-sm font-bold text-gray-700">History</a>
              {role === "receptionist" && <button onClick={() => addQueue(patient.patient_id)} className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white">Add Queue</button>}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
