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
    const fallback = window.setInterval(() => refresh().catch(() => undefined), 3000);
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
    const submitter = (event.nativeEvent as SubmitEvent).submitter as HTMLButtonElement | null;
    const shouldQueue = submitter?.value === "queue";
    const form = new FormData(target);
    setSavingPatient(true);
    try {
      const saved = await apiPost<{ patient_id: number; patient_number: string }>("add-patient", Object.fromEntries(form));
      if (shouldQueue) await apiPost("add-queue", { patient_id: saved.patient_id });
      target.reset();
      setSearch("");
      setResults([]);
      setMessage(shouldQueue ? `Patient ${saved.patient_number} registered and queued.` : `Patient ${saved.patient_number} registered.`);
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

  if (role === "admin") {
    return (
      <div>
        <h2 className="text-2xl font-black text-gray-900 sm:text-3xl">Dashboard Overview</h2>
        <div className="my-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 lg:my-8 lg:gap-6">
          <Stat label="Total Patients" value={stats.total_patients} tone="text-blue-600" edge="border-r-blue-500" />
          <Stat label="Visits Today" value={stats.visits_today} tone="text-green-600" edge="border-r-green-500" />
          <Stat label="Waiting Patients" value={stats.waiting} tone="text-yellow-500" edge="border-r-yellow-400" />
          <Stat label="Registered Today" value={stats.patients_today} tone="text-purple-600" edge="border-r-purple-500" />
        </div>
        <div className="grid gap-6 lg:grid-cols-[minmax(0,420px)_minmax(0,1fr)]">
          <section className="space-y-6">
            <div className="rounded-xl bg-white p-6 shadow-sm">
              <h3 className="mb-4 text-xl font-black">Daily Operations</h3>
              <p className="mb-5 text-sm font-semibold text-gray-500">Permanently removes all patients from today's queue and resets tokens to #1.</p>
              <button onClick={resetQueue} className="w-full rounded-lg bg-red-500 px-5 py-4 font-black text-white shadow">Reset Today's Queue</button>
            </div>
            <div className="rounded-xl bg-white p-6 shadow-sm">
              <h3 className="mb-4 text-xl font-black">Billing Settings</h3>
              <input placeholder="Default visit fee (Rs.)" className="mb-3 w-full rounded-lg border p-3 font-bold" />
              <button className="w-full rounded-lg bg-teal-600 px-5 py-4 font-black text-white">Save New Fee</button>
            </div>
          </section>
          <section className="rounded-xl bg-white p-6 shadow-sm">
            <h3 className="mb-4 text-xl font-black">Quick Search & Edit</h3>
            <input value={search} onChange={(event) => runSearch(event.target.value)} placeholder="Search by name or phone..." className="mb-4 w-full rounded-lg border p-4 font-bold" />
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm">
                <thead className="border-b text-xs uppercase text-gray-500"><tr><th className="p-3">ID</th><th>Name</th><th>Phone</th><th>Gender</th><th>Action</th></tr></thead>
                <tbody>
                  {results.length === 0 ? <tr><td colSpan={5} className="p-10 text-center font-bold text-gray-400">Type entirely to search database</td></tr> : results.map((patient) => (
                    <tr key={patient.patient_id} className="border-b">
                      <td className="p-3">{patient.patient_id}</td><td>{patient.first_name} {patient.last_name}</td><td>{patient.phone}</td><td>{patient.gender}</td>
                      <td><a href={`/doctor/history?patient_id=${patient.patient_id}`} className="rounded bg-blue-500 px-3 py-2 font-bold text-white">History</a></td>
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

  if (role === "receptionist") {
    const active = queue.filter((row) => row.status !== "completed");
    const completed = queue.filter((row) => row.status === "completed");
    return (
      <div>
        <h2 className="text-2xl font-black text-gray-900 sm:text-3xl">Receptionist Desk</h2>
        <p className="mb-6 font-bold text-gray-500">Patient intake & flow management</p>
        {message && <Notice message={message} />}
        <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
          <Stat label="Visits Today" value={stats.visits_today} tone="text-blue-600" />
          <Stat label="Waiting Now" value={stats.waiting} tone="text-yellow-500" />
          <Stat label="Registered Today" value={stats.patients_today} tone="text-purple-600" />
          <Stat label="Total Patients" value={stats.total_patients} tone="text-gray-700" />
        </div>
        <div className="grid min-w-0 gap-6 xl:grid-cols-[1fr_1fr_1fr]">
          <section className="min-w-0 space-y-6">
            <div className="rounded-xl bg-white p-6 shadow-sm">
              <h3 className="mb-4 text-xl font-black">1. Select Patient</h3>
              <input value={search} onChange={(e) => runSearch(e.target.value)} placeholder="Search Name, Phone..." className="w-full rounded-lg border p-3 font-bold" />
              <div className="mt-3 max-h-52 space-y-2 overflow-auto">
                {results.map((patient) => (
                  <button key={patient.patient_id} onClick={() => addQueue(patient.patient_id)} className="block w-full rounded border p-2 text-left text-sm font-bold hover:bg-blue-50">
                    {patient.patient_number} - {patient.first_name} {patient.last_name}
                  </button>
                ))}
              </div>
            </div>
            <form onSubmit={addPatient} className="rounded-xl border-2 border-green-400 bg-white p-6 shadow-sm">
              <h3 className="mb-4 text-xl font-black">2. Pre-Register New</h3>
              <div className="grid gap-3 md:grid-cols-2">
                <input name="first_name" required placeholder="First Name *" className="rounded-lg border p-3 font-bold" />
                <input name="last_name" required placeholder="Last Name *" className="rounded-lg border p-3 font-bold" />
                <input name="dob" required placeholder="yyyy/mm/dd" pattern="\d{4}[-/]\d{2}[-/]\d{2}" className="rounded-lg border p-3 font-bold" />
                <select name="gender" className="rounded-lg border p-3 font-bold"><option>Male</option><option>Female</option><option>Other</option></select>
                <input name="phone" placeholder="Phone Number" className="rounded-lg border p-3 font-bold" />
                <input name="nic" placeholder="NIC / ID" className="rounded-lg border p-3 font-bold" />
                <textarea name="address" placeholder="Residential Address" className="rounded-lg border p-3 font-bold md:col-span-2" />
              </div>
              <div className="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <button disabled={savingPatient} value="register" className="rounded-lg bg-gray-600 px-4 py-3 font-black text-white">Register Only</button>
                <button disabled={savingPatient} value="queue" className="rounded-lg bg-green-600 px-4 py-3 font-black text-white">Register & Queue</button>
              </div>
            </form>
          </section>
          <QueuePanel title="Dispatch Line" rows={active} role={role} startVisit={startVisit} refresh={refresh} />
          <QueuePanel title="Completed Today" rows={completed} role={role} startVisit={startVisit} refresh={refresh} completed />
        </div>
      </div>
    );
  }

  return (
    <div>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-2xl font-black text-gray-900 sm:text-3xl">My Dashboard</h2>
          <p className="mt-1 font-bold text-gray-500">Real-time daily operations</p>
        </div>
        <button className="rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-200 shadow-sm">Backup to Drive</button>
      </div>
      {message && <Notice message={message} />}
      <div className="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
        <Stat label="Visits Today" value={stats.visits_today} tone="text-teal-600" />
        <Stat label="Waiting Now" value={stats.waiting} tone="text-yellow-500" />
        <Stat label="Registered Today" value={stats.patients_today} tone="text-purple-600" />
        <Stat label="Total Patients" value={stats.total_patients} tone="text-gray-700" />
      </div>
      <QueuePanel title="Live Queue Window" rows={queue} role={role} startVisit={startVisit} refresh={refresh} />
    </div>
  );
}

function Notice({ message }: { message: string }) {
  return <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>;
}

function Stat({ label, value, tone, edge = "" }: { label: string; value: number; tone: string; edge?: string }) {
  return (
    <div className={`rounded-xl border-r-4 ${edge} bg-white p-4 shadow-sm`}>
      <p className="text-xs font-black uppercase tracking-wide text-gray-500">{label}</p>
      <p className={`text-3xl font-black ${tone}`}>{value}</p>
    </div>
  );
}

function QueuePanel({ title, rows, role, startVisit, refresh, completed = false }: { title: string; rows: QueueRow[]; role: Role; startVisit: (row: QueueRow) => void; refresh: () => Promise<void>; completed?: boolean }) {
  const isReceptionist = role === "receptionist";

  return (
    <section className={`min-h-[420px] min-w-0 rounded-xl border-2 ${completed ? "border-green-400" : role === "receptionist" ? "border-purple-400" : "border-teal-400"} bg-white p-4 shadow-sm sm:p-6`}>
      <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 className="text-xl font-black text-gray-900 sm:text-2xl">{title}</h3>
        <span className="rounded-full border border-teal-100 bg-teal-50 px-3 py-1 text-xs font-black uppercase tracking-widest text-teal-700">Live Syncing</span>
      </div>
      {isReceptionist ? (
        <div>
          <div className="grid grid-cols-[44px_minmax(0,1fr)_auto] gap-3 border-b-2 border-gray-100 px-1 pb-3 text-xs font-black uppercase tracking-widest text-gray-400">
            <span>No.</span>
            <span>Patient Data</span>
            <span className="text-right">Status / Bill</span>
          </div>
          {rows.length === 0 ? (
            <div className="p-10 text-center text-xl font-black text-gray-300">The queue is currently empty.</div>
          ) : rows.map((row) => (
            <div key={row.queue_id} className={`grid grid-cols-[44px_minmax(0,1fr)_auto] gap-3 border-b border-gray-100 py-4 ${row.status === "completed" ? "opacity-55" : ""}`}>
              <div className="text-3xl font-black text-gray-300">{row.queue_number}</div>
              <div className="min-w-0">
                <div className="break-words text-lg font-black text-gray-800">{row.first_name} {row.last_name}</div>
                <div className="mt-1 text-xs font-bold uppercase tracking-wider text-gray-500">
                  {row.gender} - DOB: {displayDate(row.dob)} - <span className="inline-block rounded border border-teal-100 bg-teal-50 px-2 py-1 text-teal-600">Visits: {row.previous_visits}</span>
                </div>
              </div>
              <div className="flex min-w-[92px] flex-col items-end gap-2 text-right">
                <Status status={row.status} />
                {row.visit_id && <span className="rounded-lg bg-green-50 px-3 py-2 text-xs font-black text-green-700">{money(row.total_bill)}</span>}
                {row.visit_id && !row.is_paid && <button onClick={async () => { await apiPost("mark-paid", { visit_id: row.visit_id }); await refresh(); }} className="rounded-lg bg-green-600 px-3 py-2 text-xs font-black text-white">Paid</button>}
                {!row.visit_id && <button onClick={async () => { await apiPost("remove-queue", { queue_id: row.queue_id }); await refresh(); }} className="rounded bg-red-50 px-2 py-1 text-xs font-bold text-red-600">Remove</button>}
              </div>
            </div>
          ))}
        </div>
      ) : (
      <div className="overflow-x-auto">
        <table className="min-w-[760px] w-full border-collapse text-left">
          <thead>
            <tr className="border-b-2 border-gray-100 text-xs uppercase tracking-widest text-gray-400">
              <th className="p-4">No.</th><th className="p-4">Patient Data</th><th className="p-4">Current Status</th><th className="p-4 text-right">{role === "doctor" ? "Medical Actions" : "Bill"}</th>
            </tr>
          </thead>
          <tbody>
            {rows.length === 0 ? (
              <tr><td colSpan={4} className="p-16 text-center text-xl font-black text-gray-300">The queue is currently empty.</td></tr>
            ) : rows.map((row) => (
              <tr key={row.queue_id} className={`border-b border-gray-100 ${row.status === "completed" ? "opacity-55" : ""}`}>
                <td className="p-4 text-3xl font-black text-gray-300">{row.queue_number}</td>
                <td className="p-4">
                  <div className="text-lg font-black text-gray-800">{row.first_name} {row.last_name}</div>
                  <div className="mt-1 text-xs font-bold uppercase tracking-wider text-gray-500">{row.gender} - DOB: {displayDate(row.dob)} - <span className="rounded border border-teal-100 bg-teal-50 px-2 py-1 text-teal-600">Visits: {row.previous_visits}</span></div>
                </td>
                <td className="p-4"><Status status={row.status} /></td>
                <td className="space-x-2 p-4 text-right">
                  {role === "doctor" && row.status === "waiting" && <button onClick={() => startVisit(row)} className="rounded-lg bg-blue-500 px-4 py-2 text-sm font-black text-white shadow">Call Patient & Diagnose</button>}
                  {role === "doctor" && row.status === "with_doctor" && <a href={`/doctor/add-visit?queue_id=${row.queue_id}&patient_id=${row.patient_id}`} className="rounded-lg bg-teal-600 px-4 py-2 text-sm font-black text-white shadow">Continue Diagnosis</a>}
                  {row.visit_id && <a href={`/doctor/print-report?visit_id=${row.visit_id}`} target="_blank" className="rounded-lg bg-gray-100 px-4 py-2 text-sm font-black text-gray-700">View Record</a>}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      )}
    </section>
  );
}

function Status({ status }: { status: QueueRow["status"] }) {
  const styles = {
    waiting: "bg-yellow-100 text-yellow-800 border-yellow-200",
    with_doctor: "bg-blue-100 text-blue-800 border-blue-200",
    completed: "bg-green-50 text-green-600 border-green-200"
  };
  const labels = { waiting: "Waiting", with_doctor: "Consulting Now", completed: "Discharged" };
  return <span className={`rounded-full border px-3 py-1 text-xs font-black uppercase tracking-wide ${styles[status]}`}>{labels[status]}</span>;
}
