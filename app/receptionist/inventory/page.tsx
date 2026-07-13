import { ClinicShell } from "@/components/ClinicShell";
import { InventoryClient } from "@/components/InventoryClient";
import { requireRole } from "@/lib/auth";

export default async function ReceptionistInventory() {
  const { email } = await requireRole(["receptionist"]);
  return <ClinicShell role="receptionist" email={email}><InventoryClient role="receptionist" /></ClinicShell>;
}
