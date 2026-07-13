import { AddVisitClient } from "@/components/AddVisitClient";
import { requireRole } from "@/lib/auth";

export default async function AddVisit({ searchParams }: { searchParams: Promise<{ patient_id?: string; queue_id?: string }> }) {
  await requireRole(["doctor"]);
  const params = await searchParams;

  return (
    <main className="min-h-screen bg-gray-50">
      <header className="flex flex-col gap-3 bg-teal-600 px-4 py-4 text-white shadow-md sm:flex-row sm:items-center sm:justify-between">
        <h1 className="text-xl font-black sm:text-2xl">Add Visit Record</h1>
        <a href="/doctor/dashboard" className="rounded bg-teal-700 px-4 py-2 text-center text-sm font-black hover:bg-teal-800">
          Back to Dashboard
        </a>
      </header>
      <AddVisitClient patientId={Number(params.patient_id)} queueId={Number(params.queue_id)} />
    </main>
  );
}
