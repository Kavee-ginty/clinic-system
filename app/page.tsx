import Link from "next/link";
import { GoogleSignIn, SignOutButton } from "@/components/AuthButtons";
import { currentUserAndRoles } from "@/lib/auth";

const roleLinks = {
  receptionist: { href: "/receptionist/dashboard", label: "Receptionist", color: "bg-blue-600 hover:bg-blue-700" },
  doctor: { href: "/doctor/dashboard", label: "Doctor", color: "bg-teal-600 hover:bg-teal-700" },
  admin: { href: "/admin/dashboard", label: "Admin", color: "bg-gray-700 hover:bg-gray-800" }
} as const;

export default async function Home() {
  const { user, email, roles } = await currentUserAndRoles();

  return (
    <main className="flex min-h-screen items-center justify-center bg-teal-50 p-3 sm:p-4">
      <section className="w-full max-w-md rounded-xl bg-white p-6 text-center shadow-xl sm:p-10">
        <h1 className="mb-2 text-2xl font-black text-teal-700 sm:text-3xl">Clinic System</h1>
        {!user ? (
          <>
            <p className="mb-8 text-gray-500">Select your role to continue</p>
            <div className="space-y-4">
              <GoogleSignIn label="Receptionist" next="/receptionist/dashboard" className="block w-full rounded-lg bg-blue-600 px-6 py-4 text-lg font-semibold text-white shadow-md transition hover:bg-blue-700" />
              <GoogleSignIn label="Doctor" next="/doctor/dashboard" className="block w-full rounded-lg bg-teal-600 px-6 py-4 text-lg font-semibold text-white shadow-md transition hover:bg-teal-700" />
              <GoogleSignIn label="Admin" next="/admin/dashboard" className="block w-full rounded-lg bg-gray-700 px-6 py-4 text-lg font-semibold text-white shadow-md transition hover:bg-gray-800" />
            </div>
            <div className="mt-6 border-t border-gray-200 pt-6">
              <div className="rounded-lg border-2 border-indigo-200 bg-indigo-50 px-6 py-3 font-black text-indigo-700">Link a Receptionist PC</div>
              <p className="mt-2 text-xs text-gray-400">Sign in first; online mode uses this site URL on every device</p>
            </div>
          </>
        ) : roles.length === 0 ? (
          <>
            <p className="mb-4 text-sm font-bold text-red-600">Access not approved for {email}.</p>
            <p className="mb-6 text-sm text-gray-500">Ask an admin to add this Google email to the Supabase user_roles table.</p>
            <SignOutButton />
          </>
        ) : (
          <>
            <p className="mb-8 text-gray-500">Select your role to continue</p>
            <div className="space-y-4">
              {roles.map((role) => (
                <Link key={role} href={roleLinks[role].href} className={`block w-full rounded-lg px-6 py-4 text-lg font-semibold text-white shadow-md transition ${roleLinks[role].color}`}>
                  {roleLinks[role].label}
                </Link>
              ))}
            </div>
            <div className="mt-6 border-t border-gray-200 pt-6">
              <div className="rounded-lg border-2 border-indigo-200 bg-indigo-50 px-6 py-3 font-black text-indigo-700">Link a Receptionist PC</div>
              <p className="mt-2 text-xs text-gray-400">Online mode uses this site URL on every device</p>
            </div>
            <div className="mt-8">
              <SignOutButton />
            </div>
          </>
        )}
      </section>
    </main>
  );
}
