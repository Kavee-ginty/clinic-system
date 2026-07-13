import { ClinicShell } from "@/components/ClinicShell";
import { InventoryClient } from "@/components/InventoryClient";
import { requireRole } from "@/lib/auth";

export default async function DoctorInventory() {
  const { email } = await requireRole(["doctor"]);
  return <ClinicShell role="doctor" email={email}><InventoryClient role="doctor" /></ClinicShell>;
}
