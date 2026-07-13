"use client";

import { FormEvent, useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";
import { supabaseBrowser } from "@/lib/supabase/client";
import { displayDate, money } from "@/lib/format";
import type { Patient, QueueRow, Role } from "@/lib/types";

type Stats = { visits_today: number; waiting: number; patients_today: number; total_patients: number };

export function DashboardClient({ role }: { role: Role }) {
  const [stats, setStats] = useState<Stats>({ visits_today: 0, waiting: 0, patients_today: 0, total_patients: 0 });
  const [queue, setQueue] = useState<QueueRow[]>([]);
  const [search, setSearch] = useState("");
  const [results, setResults] = useState<Patient[]>([]);
  const [message, setMessage] = useState("");
  const [savingPatient, setSavingPatient] = useState(false);

  async function refresh() {
    const [nextStats, nextQueue] = await Promise.all([apiGet<Stats>("stats"), apiGet<QueueRow[]>("queue")]);
    setStats(nextStats);
    setQueue(nextQueue);
  }

  useEffect(() => {
    refresh().catch((error) => setMessage(error.message));
    const fallback = window.setInterval(() => {
      refresh().catch(() => undefined);
    }, 3000);
    const supabase = supabaseBrowser();
    const channel = supabase
      .channel("clinic-live-queue")
      .on("postgres_changes", { event: "*", schema: "public", table: "queue" }, () => refresh())
      .on("postgres_changes", { event: "*", schema: "public", table: "visits" }, () => refresh())
      .subscribe();
    return () => {
      window.clearInterval(fallback);
      supabase.removeChannel(channel);
    };
  }, []);

  async function addPatient(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (savingPatient) return;
    const target = event.currentTarget;
    const form = new FormData(target);
    setSavingPatient(true);
    try {
      const saved = await apiPost<{ patient_id: number; patient_number: string }>("add-patient", Object.fromEntries(form));
      target.reset();
      setSearch("");
      setResults([]);
      setMessage(`Patient ${saved.patient_number} added. Form cleared.`);
      await refresh();
    } catch (error) {
      setMessage(error instanceof Error ? error.message : "Patient save failed");
    } finally {
      setSavingPatient(false);
    }
  }

  async function runSearch(value = search) {
    setSearch(value);
    setResults(value.trim() ? await apiGet<Patient[]>("search", { q: value }) : []);
  }

  async function addQueue(patientId: number) {
    const saved = await apiPost<{ queue_number: number }>("add-queue", { patient_id: patientId });
    setMessage(`Queue token ${saved.queue_number} added`);
    await refresh();
  }

  async function startVisit(row: QueueRow) {
    await apiPost("update-queue", { queue_id: row.queue_id, status: "with_doctor" });
    location.href = `/doctor/add-visit?queue_id=${row.queue_id}&patient_id=${row.patient_id}`;
  }

  async function resetQueue() {
    if (!confirm("Reset today's queue?")) return;
    await apiPost("reset-queue", {});
    setMessage("Queue reset");
    await refresh();
  }

  return (
    <div>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-3xl font-black text-gray-800">{role === "admin" ? "Admin Dashboard" : role === "doctor" ? "My Dashboard" : "Reception Desk"}</h2>
          <p className="mt-1 font-semibold text-gray-500">Online clinic operations</p>
        </div>
        <div className="flex gap-2">
          <button onClick={() => refresh()} className="rounded-lg bg-gray-100 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-200">Refresh</button>
          {role === "admin" && <button onClick={resetQueue} className="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Reset Queue</button>}
        </div>
      </div>

      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}

      <div className="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
        <Stat label="Visits Today" value={stats.visits_today} tone="text-teal-600" />
        <Stat label="Waiting Now" value={stats.waiting} tone="text-yellow-500" />
        <Stat label="Registered Today" value={stats.patients_today} tone="text-purple-600" />
        <Stat label="Total Patients" value={stats.total_patients} tone="text-gray-700" />
      </div>

      {role === "receptionist" && (
        <section className="mb-8 grid gap-6 lg:grid-cols-[1fr_1fr]">
          <form onSubmit={addPatient} className="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 className="mb-4 text-xl font-black text-gray-800">Register Patient</h3>
            <div className="grid gap-3 md:grid-cols-2">
              <input name="first_name" required placeholder="First Name *" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold" />
              <input name="last_name" required placeholder="Last Name *" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold" />
              <input name="dob" required placeholder="yyyy/mm/dd" pattern="\d{4}[-/]\d{2}[-/]\d{2}" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold" />
              <select name="gender" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold">
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
              </select>
              <input name="phone" placeholder="Phone" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold" />
              <input name="nic" placeholder="NIC" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold" />
              <input name="address" placeholder="Address" className="rounded-lg border-2 border-gray-100 p-2 text-sm font-semibold md:col-span-2" />
            </div>
            <button disabled={savingPatient} className="mt-4 rounded-lg bg-green-600 px-5 py-2 font-bold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60">
              {savingPatient ? "Saving..." : "Save Patient"}
            </button>
          </form>

          <section className="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 className="mb-4 text-xl font-black text-gray-800">Search and Queue</h3>
            <input value={search} onChange={(e) => runSearch(e.target.value)} placeholder="Search name, phone, NIC or patient no." className="mb-3 w-full rounded-lg border-2 border-gray-100 p-3 text-sm font-semibold" />
            <div className="max-h-64 space-y-2 overflow-auto">
              {results.map((patient) => (
                <div key={patient.patient_id} className="flex items-center justify-between rounded-lg border border-gray-100 p-3">
                  <div>
                    <b>{patient.first_name} {patient.last_name}</b>
                    <p className="text-xs text-gray-500">{patient.patient_number} • {patient.phone}</p>
                  </div>
                  <button onClick={() => addQueue(patient.patient_id)} className="rounded bg-blue-600 px-3 py-2 text-sm font-bold text-white">Add</button>
                </div>
              ))}
            </div>
          </section>
        </section>
      )}

      <section className="min-h-[420px] rounded-xl border border-gray-100 border-t-4 border-t-teal-500 bg-white p-6 shadow-sm">
        <div className="mb-6 flex items-center justify-between">
          <h3 className="text-2xl font-bold text-gray-800">Live Queue Window</h3>
          <span className="rounded-full border border-teal-100 bg-teal-50 px-3 py-1 text-xs font-bold uppercase tracking-widest text-teal-700">Live Syncing</span>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full border-collapse text-left">
            <thead>
              <tr className="border-b-2 border-gray-100 text-xs uppercase tracking-widest text-gray-400">
                <th className="p-4">No.</th>
                <th className="p-4">Patient Data</th>
                <th className="p-4">Status</th>
                <th className="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              {queue.length === 0 ? (
                <tr><td colSpan={4} className="p-20 text-center text-2xl font-black text-gray-300">The queue is currently empty.</td></tr>
              ) : queue.map((row) => (
                <tr key={row.queue_id} className={`border-b border-gray-100 transition hover:bg-gray-50 ${row.status === "completed" ? "opacity-60" : ""}`}>
                  <td className="p-4 text-3xl font-black text-gray-300">{row.queue_number}</td>
                  <td className="p-4">
                    <div className="text-lg font-bold text-gray-800">{row.first_name} {row.last_name}</div>
                    <div className="mt-1 text-xs font-semibold uppercase tracking-wider text-gray-500">{row.gender} • DOB: {displayDate(row.dob)} • Visits: {row.previous_visits}</div>
                    {row.total_bill !== null && <div className="mt-1 text-xs font-bold text-gray-500">Bill: {money(row.total_bill)} {row.is_paid ? "• Paid" : "• Pending"}</div>}
                  </td>
                  <td className="p-4"><Status status={row.status} /></td>
                  <td className="space-x-2 p-4 text-right">
                    {role === "doctor" && row.status === "waiting" && <button onClick={() => startVisit(row)} className="rounded-lg bg-blue-500 px-4 py-2 text-sm font-bold text-white">Call Patient & Diagnose</button>}
                    {role === "doctor" && row.status === "with_doctor" && <a href={`/doctor/add-visit?queue_id=${row.queue_id}&patient_id=${row.patient_id}`} className="rounded-lg bg-teal-600 px-4 py-2 text-sm font-bold text-white">Continue Diagnosis</a>}
                    {row.visit_id && <a href={`/doctor/print-report?visit_id=${row.visit_id}`} target="_blank" className="rounded-lg bg-gray-100 px-4 py-2 text-sm font-bold text-gray-700">Record</a>}
                    {role === "receptionist" && row.visit_id && !row.is_paid && <button onClick={async () => { await apiPost("mark-paid", { visit_id: row.visit_id }); await refresh(); }} className="rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white">Paid</button>}
                    {role === "receptionist" && !row.visit_id && <button onClick={async () => { await apiPost("remove-queue", { queue_id: row.queue_id }); await refresh(); }} className="rounded-lg bg-red-50 px-4 py-2 text-sm font-bold text-red-600">Remove</button>}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  );
}

function Stat({ label, value, tone }: { label: string; value: number; tone: string }) {
  return (
    <div className="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
      <p className="text-xs font-bold uppercase tracking-wide text-gray-500">{label}</p>
      <p className={`text-2xl font-black ${tone}`}>{value}</p>
    </div>
  );
}

function Status({ status }: { status: QueueRow["status"] }) {
  const styles = {
    waiting: "bg-yellow-100 text-yellow-800 border-yellow-200",
    with_doctor: "bg-blue-100 text-blue-800 border-blue-200",
    completed: "bg-green-50 text-green-600 border-green-200"
  };
  const labels = { waiting: "Waiting", with_doctor: "Consulting Now", completed: "Discharged" };
  return <span className={`rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide ${styles[status]}`}>{labels[status]}</span>;
}
