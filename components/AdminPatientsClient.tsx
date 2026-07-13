"use client";

import { useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { displayDate } from "@/lib/format";
import type { Patient } from "@/lib/types";

export function AdminPatientsClient() {
  const [patients, setPatients] = useState<Patient[]>([]);
  const [message, setMessage] = useState("");

  async function refresh() {
    setPatients(await apiGet<Patient[]>("patients"));
  }

  useEffect(() => {
    refresh().catch((error) => setMessage(error.message));
  }, []);

  async function remove(patient_id: number) {
    if (!confirm("Delete this patient and related records?")) return;
    await apiPost("delete-patient", { patient_id });
    setMessage("Patient deleted");
    await refresh();
  }

  return (
    <div>
      <h2 className="text-3xl font-black text-gray-800">Patients</h2>
      <p className="mb-6 font-semibold text-gray-500">Latest 300 patient records</p>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <div className="overflow-x-auto rounded-xl border border-gray-100 bg-white shadow-sm">
        <table className="w-full text-left">
          <thead className="border-b bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th className="p-3">Patient</th><th className="p-3">DOB</th><th className="p-3">Phone</th><th className="p-3">NIC</th><th className="p-3"></th></tr>
          </thead>
          <tbody>
            {patients.map((patient) => (
              <tr key={patient.patient_id} className="border-b">
                <td className="p-3"><b>{patient.first_name} {patient.last_name}</b><p className="text-xs text-gray-500">{patient.patient_number}</p></td>
                <td className="p-3">{displayDate(patient.dob)}</td>
                <td className="p-3">{patient.phone}</td>
                <td className="p-3">{patient.nic}</td>
                <td className="p-3 text-right">
                  <a href={`/doctor/history?patient_id=${patient.patient_id}`} className="mr-2 rounded bg-gray-100 px-3 py-1 text-sm font-bold text-gray-700">History</a>
                  <button onClick={() => remove(patient.patient_id)} className="rounded bg-red-50 px-3 py-1 text-sm font-bold text-red-700">Delete</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
