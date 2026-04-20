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
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 font-sans">
    
    <!-- Sidebar -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Mobile Nav Header -->
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 md:hidden flex justify-between items-center shadow-lg z-10 relative">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Admin Panel</h1>
            <div class="flex gap-2">
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg font-medium text-sm transition-all shadow-sm">Dashboard</a>
                <a href="logout.php" class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 rounded-lg font-medium text-sm transition-all shadow-sm">Logout</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 w-full relative z-0">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight drop-shadow-md">Dashboard Overview</h2>
                    <p class="text-slate-400 font-medium mt-1">System status and settings</p>
                </div>
            </div>
            
            <!-- Statistics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/5 backdrop-blur-3xl p-6 rounded-[2rem] shadow-2xl border border-white/10 hover:border-indigo-500/30 transition hover:shadow-[0_0_20px_rgba(99,102,241,0.2)] relative overflow-hidden group">
                    <p class="text-slate-400 font-bold mb-1 uppercase tracking-wider text-[10px]">Total Patients</p>
                    <p class="text-4xl font-bold text-indigo-400 drop-shadow"><?= $stats['total_patients'] ?></p>
                </div>
                <div class="bg-white/5 backdrop-blur-3xl p-6 rounded-[2rem] shadow-2xl border border-white/10 hover:border-emerald-500/30 transition hover:shadow-[0_0_20px_rgba(16,185,129,0.2)] relative overflow-hidden group">
                    <p class="text-slate-400 font-bold mb-1 uppercase tracking-wider text-[10px]">Visits Today</p>
                    <p class="text-4xl font-bold text-emerald-400 drop-shadow"><?= $stats['visits_today'] ?></p>
                </div>
                <div class="bg-white/5 backdrop-blur-3xl p-6 rounded-[2rem] shadow-2xl border border-white/10 hover:border-amber-500/30 transition hover:shadow-[0_0_20px_rgba(245,158,11,0.2)] relative overflow-hidden group">
                    <p class="text-slate-400 font-bold mb-1 uppercase tracking-wider text-[10px]">Waiting Patients</p>
                    <p class="text-4xl font-bold text-amber-400 drop-shadow"><?= $stats['waiting'] ?></p>
                </div>
                <div class="bg-white/5 backdrop-blur-3xl p-6 rounded-[2rem] shadow-2xl border border-white/10 hover:border-teal-500/30 transition hover:shadow-[0_0_20px_rgba(20,184,166,0.2)] relative overflow-hidden group">
                    <p class="text-slate-400 font-bold mb-1 uppercase tracking-wider text-[10px]">Registered Today</p>
                    <p class="text-4xl font-bold text-teal-400 drop-shadow"><?= $stats['patients_today'] ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Controls Admin -->
                <div class="col-span-1 space-y-6">
                    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-300 font-bold flex-shrink-0 shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <h2 class="font-bold text-lg text-white tracking-tight drop-shadow-sm">Daily Operations</h2>
                        </div>
                        <p class="text-sm text-slate-400 mb-5 font-medium leading-relaxed">Permanently removes ALL patients from today's queue and resets tokens to #1. Use for a fresh start or at end of day.</p>
                        <button onclick="resetQueue()" class="mt-auto w-full bg-rose-500/20 hover:bg-rose-500/30 border border-rose-500/30 text-rose-200 font-bold p-3 rounded-xl transition-all shadow-sm hover:shadow-[0_0_20px_rgba(244,63,94,0.4)] backdrop-blur-md">
                            Reset Today's Queue
                        </button>
                    </div>

                    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-slate-500/20 border border-slate-500/30 flex items-center justify-center text-slate-300 flex-shrink-0 shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                            </div>
                            <h2 class="font-bold text-lg text-white tracking-tight drop-shadow-sm">Security</h2>
                        </div>
                        <input type="password" id="newPass" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 mb-4 focus:ring-2 focus:ring-slate-500/30 focus:border-slate-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" placeholder="New Password">
                        <button onclick="changePassword()" class="mt-auto w-full bg-slate-500/20 hover:bg-slate-500/30 border border-slate-500/30 text-white font-bold p-3 rounded-xl transition-all shadow-sm hover:shadow-[0_0_20px_rgba(100,116,139,0.4)] backdrop-blur-md">Update Password</button>
                    </div>

                    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-300 flex-shrink-0 shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h2 class="font-bold text-lg text-white tracking-tight drop-shadow-sm">Billing Settings</h2>
                        </div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Default Visit Fee (Rs.)</label>
                        <input type="number" id="visitFeeInput" value="<?= htmlspecialchars($currentFee) ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 mb-4 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm">
                        <button onclick="updateVisitFee()" class="mt-auto w-full bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-200 font-bold p-3 rounded-xl transition-all shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] backdrop-blur-md">Save New Fee</button>
                    </div>
                </div>

                <!-- Database Overview -->
                <div class="col-span-1 md:col-span-2 bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 flex flex-col h-[600px] w-full max-w-full min-w-0">
                    <h2 class="text-xl font-bold text-white mb-6 tracking-tight drop-shadow-sm">Quick Search & Edit</h2>
                    <input type="text" id="adminSearchInput" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-4 mb-6 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm text-sm" placeholder="Search by name or phone...">
                    
                    <div class="flex-1 overflow-y-auto w-full custom-scrollbar rounded-xl border border-white/10 bg-black/10 backdrop-blur-sm">
                        <table class="w-full text-left border-collapse table-fixed">
                            <thead class="sticky top-0 bg-white/5 backdrop-blur-md shadow-sm z-10 w-full border-b border-white/10">
                                <tr class="text-slate-300 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-bold w-16">ID</th>
                                    <th class="p-4 font-bold">Name</th>
                                    <th class="p-4 font-bold w-32">Phone</th>
                                    <th class="p-4 font-bold hidden md:table-cell w-24">Gender</th>
                                    <th class="p-4 font-bold w-48 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="adminSearchResults" class="divide-y divide-white/5">
                                <!-- Filled by JS -->
                                 <tr>
                                    <td colspan="5" class="p-10 text-center text-slate-400 font-bold drop-shadow-sm">Type entirely to search database</td>
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
                tbody.innerHTML = '<tr><td colspan="5" class="p-10 text-center text-slate-400 font-bold drop-shadow-sm">Type minimum 2 characters...</td></tr>'; 
                return; 
            }
            
            const res = await fetch(`../api/search_patient.php?q=${encodeURIComponent(query)}`);
            const patients = await res.json();
            
            if(patients.length === 0) {
                 tbody.innerHTML = '<tr><td colspan="5" class="p-10 text-center text-rose-400 font-bold drop-shadow-sm">No exact matches found.</td></tr>'; 
                 return;
            }

            tbody.innerHTML = patients.map(p => `
                <tr class="hover:bg-white/5 transition-colors group max-w-full">
                    <td class="p-4 text-indigo-400 font-bold text-xs truncate max-w-0">#${p.PatientID}</td>
                    <td class="p-4 font-bold text-white drop-shadow-sm truncate max-w-0">${p.FirstName} ${p.LastName}</td>
                    <td class="p-4 font-medium text-slate-300 text-sm truncate max-w-0">${p.Phone}</td>
                    <td class="p-4 hidden md:table-cell text-sm text-slate-300 font-medium truncate max-w-0">${p.Gender}</td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2 outline-none">
                            <a href="edit_patient.php?id=${p.PatientID}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg text-xs font-bold transition-all shadow-sm">Edit</a>
                            <a href="../doctor/history.php?patient_id=${p.PatientID}" target="_blank" class="px-3 py-1.5 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 border border-indigo-500/30 rounded-lg text-xs font-bold transition-all shadow-sm">History</a>
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
