import Link from "next/link";
import type { Role } from "@/lib/types";
import { SignOutButton } from "@/components/AuthButtons";

const nav: Record<Role, Array<[string, string]>> = {
  admin: [
    ["Dashboard", "/admin/dashboard"],
    ["All Patients", "/admin/patients"],
    ["Backup DB", "/admin/import"],
    ["Import DB", "/admin/import"]
  ],
  doctor: [
    ["Live Dashboard", "/doctor/dashboard"],
    ["Search Patient", "/doctor/search"],
    ["Manage Inventory", "/doctor/inventory"],
    ["Print Settings", "/doctor/settings"],
    ["Backup Settings", "/doctor/settings"]
  ],
  receptionist: [
    ["Live Register", "/receptionist/dashboard"],
    ["Search Patient", "/receptionist/search"],
    ["Drug Inventory", "/receptionist/inventory"]
  ]
};

const theme: Record<Role, { bg: string; active: string; subtitle: string; bottom: string }> = {
  admin: { bg: "bg-slate-950", active: "bg-slate-800", subtitle: "ADMINISTRATOR", bottom: "bg-slate-800 hover:bg-slate-700" },
  doctor: { bg: "bg-teal-950", active: "bg-teal-800/80", subtitle: "MEDICAL STAFF", bottom: "bg-teal-800 hover:bg-teal-700" },
  receptionist: { bg: "bg-blue-950", active: "bg-blue-800", subtitle: "FRONT DESK", bottom: "bg-blue-800 hover:bg-blue-700" }
};

export function ClinicShell({ role, email, children }: { role: Role; email: string; children: React.ReactNode }) {
  const t = theme[role];
  return (
    <div className="flex min-h-screen bg-gray-50 text-gray-800">
      <aside className={`${t.bg} fixed inset-y-0 left-0 hidden w-64 shrink-0 flex-col text-white md:flex`}>
        <div className="border-b border-white/10 p-6">
          <h1 className="text-2xl font-black">Clinic System</h1>
          <p className="mt-1 text-xs font-black uppercase tracking-widest text-teal-300">{t.subtitle}</p>
          {role === "doctor" && <p className="mt-3 break-all text-xs text-white/50">{email}</p>}
        </div>
        <nav className="space-y-2 p-4">
          {nav[role].map(([label, href]) => (
            <Link key={`${label}-${href}`} href={href} className={`block rounded-lg px-3 py-3 font-black text-white/80 transition hover:bg-white/10 hover:text-white`}>
              {label}
            </Link>
          ))}
        </nav>
        <div className="mt-auto space-y-3 border-t border-white/10 p-4">
          {role !== "admin" && <button className={`w-full rounded-lg px-4 py-3 text-sm font-black text-white/80 ${t.bottom}`}>{role === "receptionist" ? "Toggle Theme" : "Theme"}</button>}
          <Link href="/" className={`block w-full rounded-lg px-4 py-3 text-center text-sm font-black text-white/80 ${t.bottom}`}>Main Menu</Link>
          <SignOutButton className="w-full rounded-lg bg-red-500 px-4 py-3 text-sm font-black text-white hover:bg-red-600" />
        </div>
      </aside>
      <div className="flex min-w-0 flex-1 flex-col md:ml-64">
        <nav className={`${t.bg} flex items-center justify-between p-4 text-white md:hidden`}>
          <b>Clinic System</b>
          <SignOutButton />
        </nav>
        <div className={`${t.bg} overflow-x-auto border-t border-white/10 px-3 pb-3 md:hidden`}>
          <div className="flex min-w-max gap-2">
            {nav[role].map(([label, href]) => (
              <Link key={`${label}-${href}-mobile`} href={href} className="rounded-lg bg-white/10 px-3 py-2 text-sm font-black text-white/85">
                {label}
              </Link>
            ))}
            <Link href="/" className="rounded-lg bg-white/10 px-3 py-2 text-sm font-black text-white/85">Main Menu</Link>
          </div>
        </div>
        <main className="min-h-screen p-3 sm:p-4 md:p-8">{children}</main>
      </div>
    </div>
  );
}
