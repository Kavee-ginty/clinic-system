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
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 md:hidden flex justify-between items-center shadow-lg z-10 relative">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">All Patients Record</h1>
            <div>
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-200 shadow-sm transition-all text-sm font-bold rounded-lg mr-2">Dashboard</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 w-full relative z-0">
        <div class="bg-white/5 backdrop-blur-3xl p-6 md:p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20 mt-4">
            <h2 class="text-2xl font-bold mb-6 text-white tracking-tight drop-shadow-md">Master Patient List</h2>
            <div class="flex-1 overflow-y-auto custom-scrollbar rounded-xl border border-white/10 bg-black/10 backdrop-blur-sm max-h-[700px]">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead class="bg-white/5 sticky top-0 backdrop-blur-md z-10 border-b border-white/10 text-slate-300 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="p-4 font-bold w-16">ID</th>
                            <th class="p-4 font-bold">First Name</th>
                            <th class="p-4 font-bold">Last Name</th>
                            <th class="p-4 font-bold w-32">Phone</th>
                            <th class="p-4 font-bold w-24">Gender</th>
                            <th class="p-4 font-bold w-28">DOB</th>
                            <th class="p-4 font-bold w-32">Reg. Date</th>
                            <th class="p-4 font-bold text-right w-64">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-300 font-medium">
                        <?php foreach($patients as $p): ?>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                            <td class="p-4 text-indigo-400 font-bold drop-shadow-sm truncate max-w-0"><?= $p['PatientID'] ?></td>
                            <td class="p-4 text-white drop-shadow-sm truncate max-w-0"><?= htmlspecialchars($p['FirstName']) ?></td>
                            <td class="p-4 text-white drop-shadow-sm truncate max-w-0"><?= htmlspecialchars($p['LastName']) ?></td>
                            <td class="p-4 truncate max-w-0"><?= htmlspecialchars($p['Phone']) ?></td>
                            <td class="p-4 truncate max-w-0"><?= htmlspecialchars($p['Gender']) ?></td>
                            <td class="p-4 truncate max-w-0"><?= htmlspecialchars($p['DOB']) ?></td>
                            <td class="p-4 truncate max-w-0 text-sm"><?= htmlspecialchars(date('M d', strtotime($p['RegisteredDate']))) ?></td>
                            <td class="p-4 text-right space-x-1 outline-none">
                                <a href="edit_patient.php?id=<?= $p['PatientID'] ?>" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-200 transition-all shadow-sm rounded-lg text-xs font-bold backdrop-blur-sm">Edit</a>
                                <a href="../doctor/history.php?patient_id=<?= $p['PatientID'] ?>" target="_blank" class="px-3 py-1.5 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-300 transition-all shadow-sm rounded-lg text-xs font-bold backdrop-blur-sm">History</a>
                                <button onclick="deletePatient(<?= $p['PatientID'] ?>)" class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 border border-rose-500/30 text-rose-300 transition-all shadow-sm rounded-lg text-xs font-bold backdrop-blur-sm">Delete</button>
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
