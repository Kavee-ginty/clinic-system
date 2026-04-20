<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<?php
$pageTitle = 'Doctor Dashboard';
include '../includes/header.php';
?>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_doctor.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 md:hidden flex justify-between items-center shadow-lg">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Doctor Dashboard</h1>
            <a href="logout.php" class="px-4 py-1.5 bg-rose-500/10 text-rose-300 border border-rose-500/30 hover:bg-rose-500/20 hover:border-rose-500/50 hover:shadow-[0_0_15px_rgba(244,63,94,0.3)] rounded-lg font-medium text-sm transition-all">Logout</a>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 relative z-0">
            
            <div class="flex justify-between items-end mb-8 relative z-10">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight drop-shadow-md">My Dashboard</h2>
                    <p class="text-slate-400 font-medium mt-1">Real-time daily operations</p>
                </div>
            </div>

            <!-- Stats Module -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 relative z-10" id="statsGrid">
                <div class="bg-white/5 backdrop-blur-md p-5 rounded-[2rem] shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl hover:bg-white/10">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Visits Today</p><p class="text-3xl font-bold text-indigo-300 drop-shadow-md" id="stat_visits">-</p></div>
                </div>
                <div class="bg-white/5 backdrop-blur-md p-5 rounded-[2rem] shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl hover:bg-white/10">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Waiting Now</p><p class="text-3xl font-bold text-amber-300 drop-shadow-md animate-pulse" id="stat_waiting">-</p></div>
                </div>
                <div class="bg-white/5 backdrop-blur-md p-5 rounded-[2rem] shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl hover:bg-white/10">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Registered Today</p><p class="text-3xl font-bold text-teal-300 drop-shadow-md" id="stat_registered">-</p></div>
                </div>
                <div class="bg-white/5 backdrop-blur-md p-5 rounded-[2rem] shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl hover:bg-white/10">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Total Patients</p><p class="text-3xl font-bold text-white drop-shadow-md" id="stat_total">-</p></div>
                </div>
            </div>

            <!-- Live Queue -->
            <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20 min-h-[500px] relative z-10">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-white tracking-tight drop-shadow-md">Live Queue Window</h2>
                    <div class="flex items-center gap-2 bg-indigo-500/10 px-3 py-1.5 rounded-lg border border-indigo-500/20 backdrop-blur-sm">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-400"></span>
                        </span>
                        <span class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Live Syncing</span>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-white/5">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white/5 backdrop-blur-md">
                            <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/10">
                                <th class="p-4 font-medium">No.</th>
                                <th class="p-4 font-medium">Patient Data</th>
                                <th class="p-4 font-medium">Current Status</th>
                                <th class="p-4 font-medium text-right">Medical Actions</th>
                            </tr>
                        </thead>
                        <tbody id="queueTableBody" class="divide-y divide-white/5">
                            <!-- Populated by JS -->
                             <tr><td colspan="4" class="p-10 text-center font-bold text-slate-500 text-lg">Polling local server for patients...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        let lastQueueHTML = '';
        async function fetchQueueAndStats() {
            try {
                // Fetch Stats
                const statsRes = await fetch('../api/get_stats.php');
                const stats = await statsRes.json();
                document.getElementById('stat_visits').innerText = stats.visits_today;
                document.getElementById('stat_waiting').innerText = stats.waiting;
                document.getElementById('stat_registered').innerText = stats.patients_today;
                document.getElementById('stat_total').innerText = stats.total_patients;

                // Fetch Queue
                const res = await fetch('../api/get_queue.php');
                const queue = await res.json();
                
                const tbody = document.getElementById('queueTableBody');
                
                if(queue.length === 0) {
                     const emptyHTML = '<tr><td colspan="4" class="p-20 text-center font-black text-gray-300 text-2xl">The queue is currently empty.</td></tr>';
                     if (lastQueueHTML !== emptyHTML) {
                         tbody.innerHTML = emptyHTML;
                         lastQueueHTML = emptyHTML;
                     }
                     return;
                }

                const newHTML = queue.map(q => {
                    let statusBadge = '';
                    let actionButtons = '';
                    
                    if(q.Status === 'waiting') {
                        statusBadge = '<span class="px-3 py-1 bg-amber-500/20 text-amber-300 rounded-md text-xs font-medium uppercase tracking-wider border border-amber-500/30 backdrop-blur-sm">Waiting</span>';
                        actionButtons = `
                            <button onclick="startVisit(${q.QueueID}, ${q.PatientID})" class="px-5 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 border border-indigo-500/30 hover:border-indigo-500/50 backdrop-blur-sm rounded-lg font-medium transition-all shadow-sm hover:shadow-[0_0_15px_rgba(99,102,241,0.3)] text-sm">Call & Diagnose</button>
                            <a href="history.php?patient_id=${q.PatientID}" target="_blank" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 hover:border-white/20 backdrop-blur-sm rounded-lg font-medium transition-all text-sm hidden md:inline-block">History</a>
                        `;
                    }
                    else if(q.Status === 'with_doctor') {
                        statusBadge = '<span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-md text-xs font-medium uppercase tracking-wider animate-pulse border border-indigo-500/30 backdrop-blur-sm">Consulting...</span>';
                        actionButtons = `
                            <a href="add_visit.php?queue_id=${q.QueueID}&patient_id=${q.PatientID}" class="px-5 py-2 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/30 hover:border-teal-500/50 backdrop-blur-sm rounded-lg font-medium transition-all shadow-sm hover:shadow-[0_0_15px_rgba(20,184,166,0.3)] text-sm">Continue</a>
                            <a href="history.php?patient_id=${q.PatientID}" target="_blank" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 hover:border-white/20 backdrop-blur-sm rounded-lg font-medium transition-all text-sm hidden md:inline-block">History</a>
                        `;
                    }
                    else if(q.Status === 'completed') {
                        statusBadge = '<span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-md text-xs font-medium uppercase tracking-wider border border-emerald-500/30 backdrop-blur-sm">Discharged</span>';
                        actionButtons = `
                            <a href="print_report.php?visit_id=${q.VisitID}" target="_blank" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 hover:border-white/20 backdrop-blur-sm rounded-lg font-medium transition-all text-sm">View Record</a>
                        `;
                    }

                    // Ghosting out completed patients so waiting patients stand out
                    const rowClass = q.Status === 'completed' ? 'opacity-40 hover:opacity-100 bg-black/20' : 'bg-transparent hover:bg-white/5';

                    return `
                    <tr class="transition-all group ${rowClass}">
                        <td class="p-4 font-bold text-2xl text-slate-500 group-hover:text-indigo-400 transition-colors">${q.QueueNumber}</td>
                        <td class="p-4">
                            <div class="font-bold text-base text-white drop-shadow-sm">${q.FirstName} ${q.LastName}</div>
                            <div class="text-xs text-slate-400 font-medium tracking-wide mt-1">
                                ${q.Gender} &bull; DOB: ${q.DOB} &bull; 
                                <span class="text-indigo-400 font-semibold ml-2">Visits: ${q.PreviousVisits || 0}</span>
                            </div>
                        </td>
                        <td class="p-4">${statusBadge}</td>
                        <td class="p-4 text-right space-x-2 flex justify-end gap-2 items-center">
                            ${actionButtons}
                        </td>
                    </tr>
                    `;
                }).join('');
                
                if (lastQueueHTML !== newHTML) {
                    tbody.innerHTML = newHTML;
                    lastQueueHTML = newHTML;
                }
            } catch(e) {}
        }

        async function startVisit(queueId, patientId) {
            await fetch('../api/update_queue.php', {
                method: 'POST',
                body: JSON.stringify({queue_id: queueId, status: 'with_doctor'}),
                headers: {'Content-Type': 'application/json'}
            });
            // Automatically redirect to add_visit right after calling the patient
            window.location.href = `add_visit.php?queue_id=${queueId}&patient_id=${patientId}`;
        }

        async function changeDoctorPassword( ) {
            const pass = document.getElementById('docPass').value;
            if(pass.length < 3) return showToast('Password too short!', 'error');
            const res = await fetch('../api/change_doctor_password.php', {
                method: 'POST', body: JSON.stringify({password: pass}), headers: {'Content-Type': 'application/json'}
            });
            const data = await res.json();
            if(data.success) { 
                showToast('Secure Doctor Password updated!'); 
                document.getElementById('docPass').value = '';
                document.getElementById('passwordModule').classList.add('hidden');
            }
        }

        // Poll every 1 second (updates both Queue AND Stats dynamically)
        setInterval(fetchQueueAndStats, 1000);
        fetchQueueAndStats();
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
