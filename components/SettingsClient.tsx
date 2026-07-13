"use client";

import { FormEvent, useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";

const fields = [
  ["clinic_name", "Clinic Name"],
  ["clinic_address", "Clinic Address"],
  ["clinic_phone", "Clinic Phone"],
  ["clinic_email", "Clinic Email"],
  ["doctor_name", "Doctor Name"],
  ["doctor_qualifications", "Doctor Qualifications"],
  ["doctor_slmc", "Doctor SLMC"],
  ["visit_fee", "Visit Fee"],
  ["print_page_size", "Print Page Size"],
  ["print_text_size", "Print Text Size"]
] as const;

export function SettingsClient() {
  const [settings, setSettings] = useState<Record<string, string>>({});
  const [message, setMessage] = useState("");

  useEffect(() => {
    apiGet<Record<string, string>>("settings").then(setSettings).catch((error) => setMessage(error.message));
  }, []);

  async function save(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    await apiPost("settings", Object.fromEntries(new FormData(event.currentTarget)));
    setMessage("Settings saved");
  }

  return (
    <form onSubmit={save} className="max-w-3xl">
      <h2 className="text-3xl font-black text-gray-800">Clinic Settings</h2>
      <p className="mb-6 font-semibold text-gray-500">Used on billing and print reports</p>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <div className="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div className="grid gap-4 md:grid-cols-2">
          {fields.map(([name, label]) => (
            <label key={name} className="block">
              <span className="mb-1 block text-sm font-black uppercase text-gray-500">{label}</span>
              <input name={name} defaultValue={settings[name] ?? ""} className="w-full rounded-lg border p-3 font-semibold" />
            </label>
          ))}
        </div>
        <button className="mt-5 rounded-lg bg-teal-700 px-6 py-3 font-black text-white">Save Settings</button>
      </div>
    </form>
  );
}
