<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
$patientId = $_GET['patient_id'] ?? null;
$queueId = $_GET['queue_id'] ?? null;

if (!$patientId || !$queueId) {
    die("Invalid request. Patient ID and Queue ID required.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Record Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Add Visit Record</h1>
        <a href="dashboard.php" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold">Back to Dashboard</a>
    </nav>

    <div class="container mx-auto p-4 max-w-4xl mt-6">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-teal-500">
            
            <div id="patientInfo" class="mb-6 p-4 bg-gray-100 rounded-lg flex justify-between items-center">
                <!-- Loaded via JS -->
            </div>

            <form id="visitForm" class="space-y-6">
                <!-- Group 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Presenting Complaint <span class="text-red-500">*</span></label>
                        <textarea id="complaint" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Examination Findings</label>
                        <textarea id="examination" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                </div>

                <!-- Group 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Investigations</label>
                        <textarea id="investigation" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Diagnosis <span class="text-red-500">*</span></label>
                        <textarea id="diagnosis" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
                    </div>
                </div>

                <!-- Group 3 -->
                <div>
                    <label class="block font-bold text-gray-700 mb-2">Treatment / Prescription <span class="text-red-500">*</span></label>
                    <textarea id="treatment" rows="4" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
                </div>

                <!-- Group 4 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Referrals</label>
                        <textarea id="referals" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Doctor's Notes</label>
                        <textarea id="notes" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" class="px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg shadow-md transition text-lg">
                        Complete Visit & Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const patientId = <?= json_encode($patientId) ?>;
        const queueId = <?= json_encode($queueId) ?>;

        // Load Patient Info
        async function loadPatient() {
            const res = await fetch(`../api/get_patient.php?id=${patientId}`);
            const p = await res.json();
            document.getElementById('patientInfo').innerHTML = `
                <div>
                    <h3 class="text-xl font-bold text-gray-800">${p.FirstName} ${p.LastName} <span class="text-gray-500 text-sm">(${p.PatientNumber || 'PT-N/A'})</span></h3>
                    <p class="text-gray-600 mt-1">
                        <span class="font-bold">Age:</span> ${p.Age || 'N/A'} | 
                        <span class="font-bold">NIC:</span> ${p.NIC || 'N/A'} | 
                        <span class="font-bold">Gender:</span> ${p.Gender} | 
                        <span class="font-bold">DOB:</span> ${p.DOB}
                    </p>
                    <p class="text-gray-500 text-sm"><span class="font-bold">Phone:</span> ${p.Phone}</p>
                </div>
                <a href="history.php?patient_id=${patientId}" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 rounded font-semibold hover:bg-blue-200">View History</a>
            `;
        }
        loadPatient();

        // Submit Form
        document.getElementById('visitForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                patient_id: patientId,
                queue_id: queueId,
                complaint: document.getElementById('complaint').value,
                examination: document.getElementById('examination').value,
                investigation: document.getElementById('investigation').value,
                diagnosis: document.getElementById('diagnosis').value,
                treatment: document.getElementById('treatment').value,
                referals: document.getElementById('referals').value,
                notes: document.getElementById('notes').value
            };

            const res = await fetch('../api/add_visit.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            
            if(result.success) {
                window.location.href = `billing.php?visit_id=${result.visit_id}`;
            } else {
                showToast("Error saving record: " + result.error, "error");
            }
        });
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
