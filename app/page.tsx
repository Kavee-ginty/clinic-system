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
    <main className="flex min-h-screen items-center justify-center bg-teal-50 p-4">
      <section className="w-full max-w-md rounded-xl bg-white p-10 text-center shadow-xl">
        <h1 className="mb-2 text-3xl font-bold text-teal-700">Clinic System</h1>
        {!user ? (
          <>
            <p className="mb-8 text-gray-500">Sign in to continue</p>
            <GoogleSignIn />
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
            <div className="mt-8">
              <SignOutButton />
            </div>
          </>
        )}
      </section>
    </main>
  );
}
