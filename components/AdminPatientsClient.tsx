"use client";

import { useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
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
    <div className="rounded-xl border-t-4 border-slate-900 bg-white p-4 shadow-sm sm:p-6">
      <h2 className="mb-5 text-xl font-black text-gray-900">Master Patient List</h2>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <div className="max-h-[72vh] overflow-auto">
        <table className="min-w-[980px] w-full text-left">
          <thead className="sticky top-0 bg-gray-100 text-sm text-gray-700">
            <tr><th className="p-3">ID</th><th>First Name</th><th>Last Name</th><th>Phone</th><th>Gender</th><th>DOB</th><th>Reg. Date</th><th className="text-right">Action</th></tr>
          </thead>
          <tbody>
            {patients.map((patient) => (
              <tr key={patient.patient_id} className="border-b">
                <td className="p-3 font-bold">{patient.patient_id}</td><td>{patient.first_name}</td><td>{patient.last_name}</td><td>{patient.phone}</td><td>{patient.gender}</td><td>{patient.dob}</td><td>{patient.registered_date}</td>
                <td className="space-x-2 whitespace-nowrap p-3 text-right">
                  <button className="rounded bg-slate-700 px-3 py-2 text-sm font-black text-white">Edit</button>
                  <a href={`/doctor/history?patient_id=${patient.patient_id}`} className="rounded bg-blue-500 px-3 py-2 text-sm font-black text-white">History</a>
                  <button onClick={() => remove(patient.patient_id)} className="rounded bg-red-500 px-3 py-2 text-sm font-black text-white">Delete</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
