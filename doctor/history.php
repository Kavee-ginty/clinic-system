<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) && !isset($_SESSION['admin_logged_in']) && !isset($_SESSION['receptionist_view'])) {
    header('Location: ../index.php');
    exit;
}
$patientId = $_GET['patient_id'] ?? null;
if (!$patientId) die("Patient ID required.");
?>
<?php
$pageTitle = 'Patient History';
include '../includes/header.php';
$isEmbedded = isset($_GET['embed']) && $_GET['embed'] === 'true';
?>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors <?= $isEmbedded ? 'p-2 overflow-y-auto' : 'flex h-screen overflow-hidden' ?>">
    <!-- Sidebar -->
    <?php 
    if (!$isEmbedded) {
        if (isset($_SESSION['admin_logged_in'])) {
            include '../includes/sidebar_admin.php'; 
        } elseif (isset($_SESSION['doctor_logged_in'])) {
            include '../includes/sidebar_doctor.php';
        } else {
            include '../includes/sidebar_receptionist.php';
        }
    }
    ?>

    <div class="<?= $isEmbedded ? 'w-full' : 'flex-1 flex flex-col h-screen overflow-hidden relative' ?>">
    <?php if (!$isEmbedded): ?>
    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Patient History</h1>
        <div>
            <a href="../index.php" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 rounded font-semibold mr-2 transition">Dashboard</a>
            <a href="javascript:void(0)" onclick="if(window.history.length > 1) { window.history.back(); } else { window.close(); }" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold transition">Back / Close</a>
        </div>
    </nav>
    <?php endif; ?>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <h2 class="text-3xl font-black text-gray-800 dark:text-white mb-6">Patient History</h2>
        
        <div id="patientInfo" class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow mb-6 flex justify-between items-center border-l-4 border-blue-500">
             <!-- Load via JS -->
        </div>

        <h2 class="text-xl font-bold mb-4 text-gray-700 dark:text-gray-300">Past Visits</h2>
        <div id="historyContainer" class="space-y-6">
             <!-- Load via JS -->
             <p class="text-gray-500 italic p-4 bg-white rounded shadow">Loading history...</p>
        </div>

        </main>
    </div>

    <script>
        const patientId = <?= json_encode($patientId) ?>;

        async function loadData() {
            // Patient Info
            const pRes = await fetch(`../api/get_patient.php?id=${patientId}`);
            const p = await pRes.json();
            document.getElementById('patientInfo').innerHTML = `
                <div>
                    <h2 class="text-2xl font-bold dark:text-white">${p.FirstName} ${p.LastName}</h2>
                    <p class="text-gray-600 dark:text-gray-400">DOB: ${p.DOB} | Gender: ${p.Gender} | Phone: ${p.Phone}</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm">${p.Address}</p>
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
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-center border-b dark:border-gray-700 pb-3 mb-3 flex-wrap gap-2">
                        <div class="text-lg font-bold text-teal-700 dark:text-teal-400">Visit Date: ${new Date(v.VisitDateTime).toLocaleString()}</div>
                        <div class="flex gap-2">
                            <a href="print_report.php?visit_id=${v.VisitID}" target="_blank" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white rounded text-sm font-semibold">Preview</a>
                            <button onclick="directPrint(${v.VisitID})" class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white rounded text-sm font-semibold transition flex items-center gap-1">🖨️ Print</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Complaint:</p>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line mb-3 bg-gray-50 dark:bg-gray-900 p-2 rounded">${v.Complaint || 'N/A'}</p>
                            
                            <p class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Diagnosis:</p>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line bg-gray-50 dark:bg-gray-900 p-2 rounded">${v.Diagnosis || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Treatment:</p>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line mb-3 bg-gray-50 dark:bg-gray-900 p-2 rounded">${v.Treatment || 'N/A'}</p>
                            
                            <p class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Notes:</p>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line bg-gray-50 dark:bg-gray-900 p-2 rounded">${v.Notes || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function directPrint(visitId) {
            let iframe = document.getElementById('printIframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'printIframe';
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
            }
            iframe.src = `print_report.php?visit_id=${visitId}`;
            iframe.onload = function() {
                setTimeout(() => {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch(e) {
                        console.error('Failed to trigger print: ', e);
                    }
                }, 500);
            };
        }

        loadData();
    </script>
</body>
</html>
