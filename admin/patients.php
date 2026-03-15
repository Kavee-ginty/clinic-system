<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM Patients ORDER BY PatientID DESC");
$patients = $stmt->fetchAll();
?>
<?php
$pageTitle = 'All Patients - Admin';
include '../includes/header.php';
?>
<body class="bg-gray-50 flex h-screen overflow-hidden dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-gray-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">All Patients Record</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-gray-700 hover:bg-gray-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 w-full">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border-t-4 border-gray-800 dark:border-gray-600">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Master Patient List</h2>
            <div class="overflow-x-auto max-h-[700px]">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-gray-100 dark:bg-gray-900 border-b dark:border-gray-700 shadow-sm text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">First Name</th>
                            <th class="p-3">Last Name</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Gender</th>
                            <th class="p-3">DOB</th>
                            <th class="p-3">Reg. Date</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 dark:text-gray-300">
                        <?php foreach($patients as $p): ?>
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="p-3 text-gray-500 dark:text-gray-400 font-bold"><?= $p['PatientID'] ?></td>
                            <td class="p-3"><?= htmlspecialchars($p['FirstName']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($p['LastName']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($p['Phone']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($p['Gender']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($p['DOB']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($p['RegisteredDate']) ?></td>
                            <td class="p-3 text-right space-x-2">
                                <a href="edit_patient.php?id=<?= $p['PatientID'] ?>" class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 font-bold transition">Edit</a>
                                <a href="../doctor/history.php?patient_id=<?= $p['PatientID'] ?>" target="_blank" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 font-bold transition">History</a>
                                <button onclick="deletePatient(<?= $p['PatientID'] ?>)" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600 font-bold transition">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </main>
    </div>
    <script>
        async function deletePatient(patientId) {
            if(confirm('Are you sure you want to delete this patient and all related visits, queues, and bill records? This cannot be undone.')) {
                try {
                    const res = await fetch('../api/delete_patient.php', {
                        method: 'POST',
                        body: JSON.stringify({ patient_id: patientId }),
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const data = await res.json();
                    if(data.success) {
                        alert('Patient deleted successfully.');
                        location.reload();
                    } else {
                        alert('Error deleting patient: ' + data.error);
                    }
                } catch(e) {
                    alert('Request failed');
                }
            }
        }
    </script>
</body>
</html>
