import { ClinicShell } from "@/components/ClinicShell";
import { requireRole } from "@/lib/auth";

export default async function AdminImport() {
  const { email } = await requireRole(["admin"]);
  return (
    <ClinicShell role="admin" email={email}>
      <h2 className="text-3xl font-black text-gray-800">Supabase Import</h2>
      <p className="mb-6 font-semibold text-gray-500">Run the SQL migration first, then import converted data in Supabase SQL Editor.</p>
      <div className="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <ol className="list-decimal space-y-3 pl-5 font-semibold text-gray-700">
          <li>Open Supabase SQL Editor.</li>
          <li>Run <code className="rounded bg-gray-100 px-2 py-1">supabase/schema.sql</code>.</li>
          <li>Add allowed Google emails to <code className="rounded bg-gray-100 px-2 py-1">user_roles</code>.</li>
          <li>Convert and run old MySQL backup inserts against the new lower-case table/column names.</li>
        </ol>
      </div>
    </ClinicShell>
  );
}
