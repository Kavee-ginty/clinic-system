"use client";

import { FormEvent, useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/client-api";

const fields = [
  ["clinic_name", "Clinic Name"],
  ["clinic_email", "Clinic Email"],
  ["clinic_address", "Clinic Address"],
  ["clinic_phone", "Phone Number(s)"],
  ["doctor_name", "Doctor's Name"],
  ["doctor_qualifications", "Doctor Qualifications"],
  ["doctor_slmc", "Doctor SLMC Reg No"],
  ["logo_width", "Logo Width (Print, e.g. w-24 or 100px)"]
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
    <form onSubmit={save} className="rounded-xl border-t-4 border-teal-400 bg-white p-4 shadow-lg sm:p-8">
      <h2 className="mb-6 text-xl font-black text-gray-900 sm:text-2xl">Edit Clinic Data (Shows on printed reports)</h2>
      {message && <div className="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm font-bold text-teal-700">{message}</div>}
      <div className="grid gap-5 md:grid-cols-2">
        {fields.map(([name, label]) => (
          <label key={name} className="block">
            <span className="mb-2 block font-black text-gray-700">{label}</span>
            <input name={name} defaultValue={settings[name] ?? ""} className="w-full rounded border border-gray-200 p-3 font-semibold" />
          </label>
        ))}
      </div>
      <div className="my-6 border-t border-gray-200 pt-5">
        <h3 className="mb-4 text-xl font-black text-gray-900">Print Properties</h3>
        <div className="grid gap-5 md:grid-cols-2">
          <label><span className="mb-2 block font-black text-gray-700">Page Size & Orientation</span><select name="print_page_size" defaultValue={settings.print_page_size ?? "A5"} className="w-full rounded border p-3"><option>A5 Portrait (Strict Layout)</option><option>A4</option></select></label>
          <label><span className="mb-2 block font-black text-gray-700">Text Size</span><select name="print_text_size" defaultValue={settings.print_text_size ?? "12px"} className="w-full rounded border p-3"><option value="12px">Small (12px)</option><option value="14px">Normal (14px)</option></select></label>
        </div>
      </div>
      <button className="w-full rounded bg-teal-600 px-6 py-4 text-lg font-black text-white shadow">Save Settings</button>
    </form>
  );
}
