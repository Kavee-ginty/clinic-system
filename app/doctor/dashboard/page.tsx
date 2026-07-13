import { ClinicShell } from "@/components/ClinicShell";
import { DashboardClient } from "@/components/DashboardClient";
import { requireRole } from "@/lib/auth";

export default async function DoctorDashboard() {
  const { email } = await requireRole(["doctor"]);
  return (
    <ClinicShell role="doctor" email={email}>
      <DashboardClient role="doctor" />
    </ClinicShell>
  );
}
