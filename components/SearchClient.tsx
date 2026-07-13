"use client";

import { useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { displayDate } from "@/lib/format";
import type { Patient, Role } from "@/lib/types";

export function SearchClient({ role }: { role: Role }) {
  const [q, setQ] = useState("");
  const [patients, setPatients] = useState<Patient[]>([]);
  const [message, setMessage] = useState("");

  async function search(value: string) {
    setQ(value);
    setPatients(await apiGet<Patient[]>("search", { q: value }));
  }

  useEffect(() => {
    search("").catch((error) => setMessage(error.message));
  }, []);

  async function addQueue(patient_id: number) {
    const saved = await apiPost<{ queue_number: number }>("add-queue", { patient_id });
    setMessage(`Queue token ${saved.queue_number} added`);
  }

  const accent = role === "doctor" ? "teal" : "blue";
  const buttonClass = accent === "teal" ? "bg-teal-600 hover:bg-teal-700" : "bg-blue-600 hover:bg-blue-700";
  const badgeClass = accent === "teal" ? "bg-teal-100 text-teal-800" : "bg-blue-100 text-blue-800";

  return (
    <div className="rounded-xl bg-white p-4 shadow-sm sm:p-6">
      <h2 className="mb-4 text-2xl font-black text-gray-900">Patient Search</h2>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <input value={q} onChange={(event) => search(event.target.value)} autoFocus placeholder="Enter name or phone number..." className="mb-6 w-full rounded-lg border-2 border-gray-200 bg-white p-4 text-lg shadow-sm" />
      <div className="space-y-3">
        {patients.map((patient) => (
          <div key={patient.patient_id} className="flex flex-col gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div className="min-w-0 flex-1">
              <h3 className="break-words text-base font-black sm:text-lg">{patient.patient_number} - {patient.first_name} {patient.last_name}</h3>
              <details className="mt-3 rounded border bg-white px-2 py-1 text-xs font-black uppercase text-teal-700">
                <summary>Patient Demographics</summary>
                <p className="mt-2 normal-case text-gray-600">{patient.gender} - DOB {displayDate(patient.dob)} - {patient.phone || "No phone"}</p>
              </details>
            </div>
            <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
              <span className={`${badgeClass} rounded-full px-3 py-2 text-sm font-black`}>Visits: {patient.visit_count ?? 0}</span>
              <a href={`/doctor/history?patient_id=${patient.patient_id}`} className={`${buttonClass} rounded-lg px-6 py-4 text-center font-black text-white shadow`}>Review History</a>
              {role === "receptionist" && <button onClick={() => addQueue(patient.patient_id)} className="rounded-lg bg-green-600 px-4 py-3 text-sm font-bold text-white">Add Queue</button>}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
