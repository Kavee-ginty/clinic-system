import { ClinicShell } from "@/components/ClinicShell";
import { DashboardClient } from "@/components/DashboardClient";
import { requireRole } from "@/lib/auth";

export default async function AdminDashboard() {
  const { email } = await requireRole(["admin"]);
  return (
    <ClinicShell role="admin" email={email}>
      <DashboardClient role="admin" />
    </ClinicShell>
  );
}
