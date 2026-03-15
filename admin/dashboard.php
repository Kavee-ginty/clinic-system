<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

// Get Stats
$stats = [
    'total_patients' => $pdo->query("SELECT COUNT(*) FROM Patients")->fetchColumn(),
    'patients_today' => $pdo->query("SELECT COUNT(*) FROM Patients WHERE RegisteredDate = CURRENT_DATE")->fetchColumn(),
    'visits_today' => $pdo->query("SELECT COUNT(*) FROM Visits WHERE DATE(VisitDateTime) = CURRENT_DATE")->fetchColumn(),
    'waiting' => $pdo->query("SELECT COUNT(*) FROM Queue WHERE QueueDate = CURRENT_DATE AND Status='waiting'")->fetchColumn()
];

// Get current visit fee
$feeStmt = $pdo->query("SELECT SettingValue FROM Settings WHERE SettingKey = 'visit_fee'");
$currentFee = $feeStmt->fetchColumn() ?: 500;
?>
<?php
$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
?>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Mobile Nav Header -->
        <nav class="bg-gray-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Admin Panel</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-gray-700 hover:bg-gray-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="logout.php" class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded font-bold text-sm transition">Logout</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 w-full">
            <h2 class="text-3xl font-black text-gray-800 dark:text-white mb-6">Dashboard Overview</h2>
            
            <!-- Statistics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-2 border-transparent hover:border-blue-100 dark:hover:border-blue-900 transition relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-full w-2 bg-blue-500"></div>
                    <p class="text-gray-500 font-bold mb-1 uppercase tracking-wider text-xs">Total Patients</p>
                    <p class="text-4xl font-black text-blue-600"><?= $stats['total_patients'] ?></p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-2 border-transparent hover:border-green-100 dark:hover:border-green-900 transition relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-2 bg-green-500"></div>
                    <p class="text-gray-500 font-bold mb-1 uppercase tracking-wider text-xs">Visits Today</p>
                    <p class="text-4xl font-black text-green-600"><?= $stats['visits_today'] ?></p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-2 border-transparent hover:border-yellow-100 dark:hover:border-yellow-900 transition relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-2 bg-yellow-400"></div>
                    <p class="text-gray-500 font-bold mb-1 uppercase tracking-wider text-xs">Waiting Patients</p>
                    <p class="text-4xl font-black text-yellow-500"><?= $stats['waiting'] ?></p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-2 border-transparent hover:border-purple-100 dark:hover:border-purple-900 transition relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-2 bg-purple-500"></div>
                    <p class="text-gray-500 font-bold mb-1 uppercase tracking-wider text-xs">Registered Today</p>
                    <p class="text-4xl font-black text-purple-600"><?= $stats['patients_today'] ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Controls Admin -->
                <div class="col-span-1 space-y-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-300 font-bold">!</div>
                            <h2 class="font-black text-lg text-gray-800 dark:text-gray-100">Daily Operations</h2>
                        </div>
                        <p class="text-sm text-gray-500 mb-4 font-medium">Permanently removes ALL patients from today's queue and resets tokens to #1. Use for a fresh start or at end of day.</p>
                        <button onclick="resetQueue()" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold p-3 rounded-xl transition shadow-md shadow-red-500/30">
                            Reset Today's Queue
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                            </div>
                            <h2 class="font-black text-lg text-gray-800 dark:text-gray-100">Security</h2>
                        </div>
                        <input type="password" id="newPass" class="w-full border-2 border-gray-200 p-3 mb-3 rounded-xl focus:border-gray-800 focus:ring-0 font-bold" placeholder="New Password">
                        <button onclick="changePassword()" class="w-full bg-gray-900 hover:bg-black text-white font-bold p-3 rounded-xl transition shadow-md">Update Password</button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="font-black text-lg text-gray-800 dark:text-gray-100">Billing Settings</h2>
                        </div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Default Visit Fee (Rs.)</label>
                        <input type="number" id="visitFeeInput" value="<?= htmlspecialchars($currentFee) ?>" class="w-full border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-900 p-3 mb-3 rounded-xl font-bold focus:border-teal-500">
                        <button onclick="updateVisitFee()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold p-3 rounded-xl transition shadow-md">Save New Fee</button>
                    </div>
                </div>

                <!-- Database Overview -->
                <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col h-[600px] w-full max-w-full min-w-0">
                    <h2 class="text-xl font-black text-gray-800 dark:text-gray-100 mb-4">Quick Search & Edit</h2>
                    <input type="text" id="adminSearchInput" class="w-full border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-900 p-4 rounded-xl mb-4 font-bold focus:border-teal-500 focus:ring-0" placeholder="Search by name or phone...">
                    
                    <div class="flex-1 overflow-y-auto pr-2 w-full">
                        <table class="w-full text-left border-collapse table-fixed">
                            <thead class="sticky top-0 bg-white dark:bg-gray-800 shadow-sm z-10 w-full">
                                <tr class="border-b-2 border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">
                                    <th class="p-3 font-bold w-16">ID</th>
                                    <th class="p-3 font-bold">Name</th>
                                    <th class="p-3 font-bold w-32">Phone</th>
                                    <th class="p-3 font-bold hidden md:table-cell w-24">Gender</th>
                                    <th class="p-3 font-bold w-48 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="adminSearchResults">
                                <!-- Filled by JS -->
                                 <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400 font-bold">Type entirely to search database</td>
                                 </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('adminSearchInput').addEventListener('input', async (e) => {
            const query = e.target.value;
            const tbody = document.getElementById('adminSearchResults');
            if(query.length < 2) { 
                tbody.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-gray-400 font-bold">Type minimum 2 characters...</td></tr>'; 
                return; 
            }
            
            const res = await fetch(`../api/search_patient.php?q=${encodeURIComponent(query)}`);
            const patients = await res.json();
            
            if(patients.length === 0) {
                 tbody.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-red-400 font-bold">No exact matches found.</td></tr>'; 
                 return;
            }

            tbody.innerHTML = patients.map(p => `
                <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition group max-w-full">
                    <td class="p-3 text-gray-400 font-bold text-xs truncate max-w-0">#${p.PatientID}</td>
                    <td class="p-3 font-bold text-gray-800 dark:text-gray-200 truncate max-w-0">${p.FirstName} ${p.LastName}</td>
                    <td class="p-3 font-mono text-sm dark:text-gray-300 truncate max-w-0">${p.Phone}</td>
                    <td class="p-3 hidden md:table-cell text-sm dark:text-gray-300 truncate max-w-0">${p.Gender}</td>
                    <td class="p-3 text-right">
                        <div class="flex justify-end gap-2 outline-none">
                            <a href="edit_patient.php?id=${p.PatientID}" class="px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-800 dark:hover:bg-gray-500 hover:text-white font-bold transition">Edit</a>
                            <a href="../doctor/history.php?patient_id=${p.PatientID}" target="_blank" class="px-3 py-2 bg-blue-50 dark:bg-gray-600 text-blue-600 dark:text-blue-400 rounded-lg text-sm hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white font-bold transition">History</a>
                        </div>
                    </td>
                </tr>
            `).join('');
        });

        async function resetQueue() {
            if(!confirm("Are you sure? This will permanently wipe today's entire queue and reset token numbers to 1!")) return;
            const res = await fetch('../api/reset_queue.php');
            const data = await res.json();
            if(data.success) {
                showToast('Daily queue has been thoroughly reset.');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showToast(data.error || 'Failed to reset queue', 'error');
            }
        }

        async function changePassword() {
            const pass = document.getElementById('newPass').value;
            if(pass.length < 3) return showToast('Password too short!', 'error');
            const res = await fetch('../api/change_password.php', {
                method: 'POST', body: JSON.stringify({password: pass}), headers: {'Content-Type': 'application/json'}
            });
            const data = await res.json();
            if(data.success) { showToast('Password changed securely!'); document.getElementById('newPass').value = ''; }
        }

        async function updateVisitFee() {
            const fee = document.getElementById('visitFeeInput').value;
            const res = await fetch('../api/update_visit_fee.php', {
                method: 'POST', body: JSON.stringify({visit_fee: fee}), headers: {'Content-Type': 'application/json'}
            });
            const data = await res.json();
            if(data.success) showToast('Default Visit Fee updated successfully!');
            else showToast('Error updating fee.', 'error');
        }
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
