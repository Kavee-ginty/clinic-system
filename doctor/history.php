<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
$patientId = $_GET['patient_id'] ?? null;
if (!$patientId) die("Patient ID required.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Patient History</h1>
        <div>
            <a href="../index.php" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 rounded font-semibold mr-2 transition">Dashboard</a>
            <a href="javascript:history.back()" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold transition">Back</a>
        </div>
    </nav>

    <div class="container mx-auto p-4 max-w-5xl mt-6">
        
        <div id="patientInfo" class="bg-white p-6 rounded-xl shadow mb-6 flex justify-between items-center border-l-4 border-blue-500">
             <!-- Load via JS -->
        </div>

        <h2 class="text-xl font-bold mb-4 text-gray-700">Past Visits</h2>
        <div id="historyContainer" class="space-y-6">
             <!-- Load via JS -->
             <p class="text-gray-500 italic p-4 bg-white rounded shadow">Loading history...</p>
        </div>

    </div>

    <script>
        const patientId = <?= json_encode($patientId) ?>;

        async function loadData() {
            // Patient Info
            const pRes = await fetch(`../api/get_patient.php?id=${patientId}`);
            const p = await pRes.json();
            document.getElementById('patientInfo').innerHTML = `
                <div>
                    <h2 class="text-2xl font-bold">${p.FirstName} ${p.LastName}</h2>
                    <p class="text-gray-600">DOB: ${p.DOB} | Gender: ${p.Gender} | Phone: ${p.Phone}</p>
                    <p class="text-gray-500 text-sm">${p.Address}</p>
                </div>
            `;

            // History
            const hRes = await fetch(`../api/get_patient_history.php?patient_id=${patientId}`);
            const history = await hRes.json();

            const container = document.getElementById('historyContainer');
            if(history.length === 0) {
                container.innerHTML = `<div class="bg-white p-6 rounded shadow text-center text-gray-500">No past visits recorded.</div>`;
                return;
            }

            container.innerHTML = history.map(v => `
                <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                    <div class="flex justify-between items-start border-b pb-3 mb-3">
                        <div class="text-lg font-bold text-teal-700">Visit Date: ${new Date(v.VisitDateTime).toLocaleString()}</div>
                        <a href="print_report.php?visit_id=${v.VisitID}" target="_blank" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm font-semibold">Print</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="font-semibold text-gray-700 text-sm">Complaint:</p>
                            <p class="text-gray-800 whitespace-pre-line mb-3 bg-gray-50 p-2 rounded">${v.Complaint || 'N/A'}</p>
                            
                            <p class="font-semibold text-gray-700 text-sm">Diagnosis:</p>
                            <p class="text-gray-800 whitespace-pre-line bg-gray-50 p-2 rounded">${v.Diagnosis || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 text-sm">Treatment:</p>
                            <p class="text-gray-800 whitespace-pre-line mb-3 bg-gray-50 p-2 rounded">${v.Treatment || 'N/A'}</p>
                            
                            <p class="font-semibold text-gray-700 text-sm">Notes:</p>
                            <p class="text-gray-800 whitespace-pre-line bg-gray-50 p-2 rounded">${v.Notes || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        loadData();
    </script>
</body>
</html>
