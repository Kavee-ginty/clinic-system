import { ClinicShell } from "@/components/ClinicShell";
import { BillingClient } from "@/components/BillingClient";
import { requireRole } from "@/lib/auth";

export default async function Billing({ searchParams }: { searchParams: Promise<{ visit_id?: string }> }) {
  const { email } = await requireRole(["doctor"]);
  return <ClinicShell role="doctor" email={email}><BillingClient visitId={Number((await searchParams).visit_id)} /></ClinicShell>;
}
