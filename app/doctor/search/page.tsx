import { ClinicShell } from "@/components/ClinicShell";
import { SearchClient } from "@/components/SearchClient";
import { requireRole } from "@/lib/auth";

export default async function DoctorSearch() {
  const { email } = await requireRole(["doctor"]);
  return <ClinicShell role="doctor" email={email}><SearchClient role="doctor" /></ClinicShell>;
}
