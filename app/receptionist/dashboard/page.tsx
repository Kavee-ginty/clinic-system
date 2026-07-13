import { ClinicShell } from "@/components/ClinicShell";
import { DashboardClient } from "@/components/DashboardClient";
import { requireRole } from "@/lib/auth";

export default async function ReceptionistDashboard() {
  const { email } = await requireRole(["receptionist"]);
  return (
    <ClinicShell role="receptionist" email={email}>
      <DashboardClient role="receptionist" />
    </ClinicShell>
  );
}
