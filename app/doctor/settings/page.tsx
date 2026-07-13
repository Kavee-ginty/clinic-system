import { ClinicShell } from "@/components/ClinicShell";
import { SettingsClient } from "@/components/SettingsClient";
import { requireRole } from "@/lib/auth";

export default async function DoctorSettings() {
  const { email } = await requireRole(["doctor"]);
  return <ClinicShell role="doctor" email={email}><SettingsClient /></ClinicShell>;
}
