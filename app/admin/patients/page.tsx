import { ClinicShell } from "@/components/ClinicShell";
import { AdminPatientsClient } from "@/components/AdminPatientsClient";
import { requireRole } from "@/lib/auth";

export default async function AdminPatients() {
  const { email } = await requireRole(["admin"]);
  return <ClinicShell role="admin" email={email}><AdminPatientsClient /></ClinicShell>;
}
