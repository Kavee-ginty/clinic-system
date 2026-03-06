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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-gray-800 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">All Patients Record</h1>
        <div>
            <a href="../index.php" class="px-4 py-2 bg-gray-900 hover:bg-black rounded font-semibold mr-2 transition">Dashboard</a>
            <a href="dashboard.php" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded font-semibold transition">Back to Admin</a>
        </div>
    </nav>

    <div class="container mx-auto p-4 max-w-6xl mt-6">
        <div class="bg-white p-6 rounded-xl shadow border-t-4 border-gray-800">
            <h2 class="text-xl font-bold mb-4">Master Patient List</h2>
            <div class="overflow-x-auto max-h-[700px]">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-gray-100 border-b shadow-sm">
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
                    <tbody>
                        <?php foreach($patients as $p): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 text-gray-500 font-bold"><?= $p['PatientID'] ?></td>
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
