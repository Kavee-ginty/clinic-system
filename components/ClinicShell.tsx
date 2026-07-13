import Link from "next/link";
import type { Role } from "@/lib/types";
import { SignOutButton } from "@/components/AuthButtons";

const nav: Record<Role, Array<[string, string]>> = {
  admin: [
    ["Dashboard", "/admin/dashboard"],
    ["Patients", "/admin/patients"],
    ["Import", "/admin/import"]
  ],
  doctor: [
    ["Dashboard", "/doctor/dashboard"],
    ["Search", "/doctor/search"],
    ["Inventory", "/doctor/inventory"],
    ["Settings", "/doctor/settings"]
  ],
  receptionist: [
    ["Dashboard", "/receptionist/dashboard"],
    ["Search", "/receptionist/search"],
    ["Inventory", "/receptionist/inventory"]
  ]
};

const tone: Record<Role, string> = {
  admin: "bg-gray-900",
  doctor: "bg-teal-900",
  receptionist: "bg-blue-900"
};

export function ClinicShell({ role, email, children }: { role: Role; email: string; children: React.ReactNode }) {
  return (
    <div className="flex h-screen overflow-hidden bg-gray-50 text-gray-800">
      <aside className={`${tone[role]} hidden w-64 shrink-0 flex-col p-5 text-white md:flex`}>
        <div className="mb-8">
          <h1 className="text-2xl font-black">Clinic System</h1>
          <p className="mt-1 text-xs font-bold uppercase tracking-widest text-white/60">{role}</p>
          <p className="mt-3 break-all text-xs text-white/50">{email}</p>
        </div>
        <nav className="space-y-2">
          {nav[role].map(([label, href]) => (
            <Link key={href} href={href} className="block rounded-lg px-4 py-3 font-bold text-white/80 transition hover:bg-white/10 hover:text-white">
              {label}
            </Link>
          ))}
        </nav>
        <div className="mt-auto">
          <SignOutButton />
        </div>
      </aside>
      <div className="flex min-w-0 flex-1 flex-col">
        <nav className={`${tone[role]} flex items-center justify-between p-4 text-white md:hidden`}>
          <b>Clinic System</b>
          <SignOutButton />
        </nav>
        <main className="flex-1 overflow-y-auto p-4 md:p-8">{children}</main>
      </div>
    </div>
  );
}
