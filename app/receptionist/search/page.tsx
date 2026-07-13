import { ClinicShell } from "@/components/ClinicShell";
import { SearchClient } from "@/components/SearchClient";
import { requireRole } from "@/lib/auth";

export default async function ReceptionistSearch() {
  const { email } = await requireRole(["receptionist"]);
  return <ClinicShell role="receptionist" email={email}><SearchClient role="receptionist" /></ClinicShell>;
}
