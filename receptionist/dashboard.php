<?php
$pageTitle = 'Receptionist Desk';
include '../includes/header.php';
?>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_receptionist.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center z-10 relative md:hidden">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Front Desk</h1>
            <div class="flex gap-2">
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 rounded-lg font-medium text-sm transition-all border border-white/10 shadow-sm">Main Menu</a>
                <a href="../index.php" class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-lg font-medium text-sm transition-all border border-rose-500/30 shadow-sm">Exit</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 relative z-0">
            
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight drop-shadow-md">Receptionist Desk</h2>
                    <p class="text-slate-400 font-medium mt-1">Patient intake & flow management</p>
                </div>
            </div>

            <!-- Stats Module -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8" id="statsGrid">
                <div class="bg-white/5 backdrop-blur-3xl p-5 rounded-2xl shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Visits Today</p><p class="text-3xl font-bold text-indigo-400 drop-shadow" id="stat_visits">-</p></div>
                </div>
                <div class="bg-white/5 backdrop-blur-3xl p-5 rounded-2xl shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Waiting Now</p><p class="text-3xl font-bold text-amber-400 animate-pulse drop-shadow" id="stat_waiting">-</p></div>
                </div>
                <div class="bg-white/5 backdrop-blur-3xl p-5 rounded-2xl shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Registered Today</p><p class="text-3xl font-bold text-emerald-400 drop-shadow" id="stat_registered">-</p></div>
                </div>
                <div class="bg-white/5 backdrop-blur-3xl p-5 rounded-2xl shadow-xl border border-white/10 border-t-white/20 flex items-center justify-between transition hover:shadow-2xl">
                    <div><p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">Total Patients</p><p class="text-3xl font-bold text-white drop-shadow" id="stat_total">-</p></div>
                </div>
            </div>

            <!-- Dual Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
                
                <!-- Left Column Controls -->
                <div class="col-span-1 space-y-6">
                    
                    <!-- Search Patient -->
                    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-3xl shadow-2xl border border-white/10 border-t-white/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-300 font-bold shadow-inner">1</div>
                            <h2 class="text-lg font-bold text-white tracking-tight drop-shadow-sm">Select Patient</h2>
                        </div>
                        <input type="text" id="searchInput" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 mb-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all shadow-inner outline-none backdrop-blur-sm" placeholder="Search Name, Phone...">
                        <div id="searchResults" class="w-full max-h-[300px] overflow-y-auto space-y-2 mt-2 pr-1 custom-scrollbar"></div>
                    </div>

                    <!-- Register New -->
                    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-3xl shadow-2xl border border-white/10 border-t-white/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-300 font-bold shadow-inner">2</div>
                            <h2 class="text-lg font-bold text-white tracking-tight drop-shadow-sm">Pre-Register New</h2>
                        </div>
                        <form id="addPatientForm" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div><input type="text" id="firstName" placeholder="First Name *" required class="bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 w-full focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 text-sm font-medium transition-all shadow-inner outline-none backdrop-blur-sm"></div>
                                <div><input type="text" id="lastName" placeholder="Last Name *" required class="bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 w-full focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 text-sm font-medium transition-all shadow-inner outline-none backdrop-blur-sm"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Date of Birth *</label>
                                    <input type="date" id="dob" required class="bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 w-full text-sm font-medium focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner outline-none backdrop-blur-sm [color-scheme:dark]">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Gender *</label>
                                    <select id="gender" class="bg-black/20 border border-white/10 text-white rounded-xl p-3 w-full text-sm font-medium focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner outline-none backdrop-blur-sm [&>option]:bg-slate-800">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <input type="text" id="phone" placeholder="Phone Number *" required class="bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 w-full text-sm font-medium focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner outline-none backdrop-blur-sm">
                                <input type="text" id="nic" placeholder="NIC / ID" class="bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 w-full text-sm font-medium focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner outline-none backdrop-blur-sm">
                            </div>
                            <textarea id="address" placeholder="Residential Address" class="bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 w-full text-sm font-medium focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner outline-none backdrop-blur-sm" rows="2"></textarea>
                            <button type="submit" class="w-full bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/30 hover:border-emerald-500/50 text-emerald-200 p-3.5 rounded-xl font-bold transition-all shadow-sm hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] mt-3 backdrop-blur-md">Register & Send to Queue</button>
                            <div id="regMsg" class="text-sm font-bold text-center hidden mt-2 drop-shadow-sm"></div>
                        </form>
                    </div>

                </div>

                <!-- Right Column Queue -->
                <div class="col-span-1 lg:col-span-2 flex flex-col">
                    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-3xl shadow-2xl border border-white/10 border-t-white/20 flex-1 flex flex-col min-h-[500px]">
                        
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-xl font-bold text-white tracking-tight drop-shadow-sm">Today's Dispatch Line</h2>
                            <div class="flex items-center gap-2 bg-indigo-500/20 px-3 py-1.5 rounded-lg border border-indigo-500/30 backdrop-blur-md">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                </span>
                                <span class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Live Syncing</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto flex-1 rounded-xl border border-white/10 bg-black/10 backdrop-blur-sm">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-white/5">
                                    <tr class="text-slate-300 text-xs uppercase tracking-wider border-b border-white/10">
                                        <th class="p-4 font-bold">Token</th>
                                        <th class="p-4 font-bold">Patient Data</th>
                                        <th class="p-4 font-bold text-right">Doctor Stage</th>
                                    </tr>
                                </thead>
                                <tbody id="queueTableBody" class="divide-y divide-white/5">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Registration
        document.getElementById('addPatientForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
                phone: document.getElementById('phone').value,
                nic: document.getElementById('nic').value,
                address: document.getElementById('address').value
            };

            const res = await fetch('../api/add_patient.php', {
                method: 'POST', body: JSON.stringify(data), headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            const msg = document.getElementById('regMsg');
            msg.classList.remove('hidden');
            if (result.success) {
                // Auto add to queue
                await addToQueue(result.patient_id, false);
                msg.className = 'text-emerald-400 text-sm font-bold mt-2 drop-shadow';
                msg.innerText = 'Registed & Queued!';
                
                document.getElementById('addPatientForm').reset();
                setTimeout(()=>msg.classList.add('hidden'), 3000);
            } else {
                msg.className = 'text-rose-400 text-sm font-bold mt-2 drop-shadow';
                msg.innerText = result.error;
            }
        });

        // Search (Debounced)
        document.getElementById('searchInput').addEventListener('input', debounce(async (e) => {
            const query = e.target.value;
            const resBox = document.getElementById('searchResults');
            if(query.length < 2) { resBox.innerHTML = ''; return; }
            
            const res = await fetch(`../api/search_patient.php?q=${encodeURIComponent(query)}`);
            const patients = await res.json();
            
            if(patients.length === 0){
                resBox.innerHTML = '<div class="p-4 text-slate-400 font-bold text-sm text-center border border-white/5 rounded-xl bg-white/5 backdrop-blur-sm shadow-inner drop-shadow-sm">No matching patients.</div>';
                return;
            }

            resBox.innerHTML = patients.map(p => `
                <div class="p-4 border border-white/10 rounded-2xl hover:border-indigo-500/30 hover:bg-white/5 hover:shadow-lg cursor-pointer flex justify-between items-center transition-all group bg-white/5 backdrop-blur-md">
                    <div>
                        <div class="font-bold text-white drop-shadow-sm">${p.FirstName} ${p.LastName}</div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-1">${p.Phone} &bull; ${p.DOB}</div>
                    </div>
                    <button onclick="addToQueue(${p.PatientID}, true)" class="bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-200 font-bold px-4 py-2 rounded-xl transition-all border border-indigo-500/30 hover:border-indigo-500/50 hover:shadow-[0_0_15px_rgba(99,102,241,0.3)] shadow-sm backdrop-blur-sm">Queue</button>
                </div>
            `).join('');
        }, 300));

        // Add to Queue
        async function addToQueue(patientId, showAlert) {
            // Optimistic UI Ghost Row
            const tbody = document.getElementById('queueTableBody');
            const ghostRow = `
                <tr class="bg-indigo-500/10 transition border-b border-white/5 animate-pulse">
                    <td class="p-4 font-bold text-2xl text-indigo-400 w-16 drop-shadow">--</td>
                    <td class="p-4">
                        <div class="font-bold text-slate-400">Syncing to Dispatch Line...</div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-1">Sending to Doctor</div>
                    </td>
                    <td class="p-4 text-right">
                        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-md text-[10px] font-bold uppercase tracking-wider border border-indigo-500/30 shadow-sm backdrop-blur-sm">Dispatching</span>
                    </td>
                </tr>
            `;
            // If table has empty message, replace it, else append
            if (tbody.innerHTML.includes('Waiting line is empty')) {
                tbody.innerHTML = ghostRow;
            } else {
                tbody.insertAdjacentHTML('beforeend', ghostRow);
            }
            lastQueueHTML = ''; // Force next poll to overwrite

            const res = await fetch('../api/add_queue.php', {
                method: 'POST',
                body: JSON.stringify({patient_id: patientId}),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            if(result.success) {
                if(showAlert) showToast(`Dispatched! Queue Token: ${result.queue_number}`);
                fetchQueueAndStats();
            } else {
                if(showAlert) showToast(`Error: ${result.error}`, 'error');
                fetchQueueAndStats();
            }
        }

        // Fetch Queue & Stats Polling
        let lastQueueHTML = '';
        async function fetchQueueAndStats() {
            try {
                // Polled Stats
                const statsRes = await fetch('../api/get_stats.php');
                const stats = await statsRes.json();
                document.getElementById('stat_visits').innerText = stats.visits_today;
                document.getElementById('stat_waiting').innerText = stats.waiting;
                document.getElementById('stat_registered').innerText = stats.patients_today;
                document.getElementById('stat_total').innerText = stats.total_patients;

                // Polled Queue
                const res = await fetch('../api/get_queue.php');
                const queue = await res.json();
                
                const tbody = document.getElementById('queueTableBody');

                if(queue.length === 0) {
                     const emptyHTML = '<tr><td colspan="3" class="p-10 text-center font-bold text-slate-400/50 text-lg drop-shadow-sm">Waiting line is empty.</td></tr>';
                     if (lastQueueHTML !== emptyHTML) {
                         tbody.innerHTML = emptyHTML;
                         lastQueueHTML = emptyHTML;
                     }
                     return;
                }

                const newHTML = queue.map(q => {
                    let statusBadge = '';
                    if(q.Status === 'waiting') statusBadge = '<span class="px-3 py-1 bg-amber-500/20 text-amber-300 rounded-md text-[10px] font-bold uppercase tracking-wider border border-amber-500/30 shadow-sm backdrop-blur-sm">Wait Area</span>';
                    else if(q.Status === 'with_doctor') statusBadge = '<span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-md text-[10px] font-bold uppercase tracking-wider border border-indigo-500/30 animate-pulse shadow-sm backdrop-blur-sm">In Room</span>';
                    else if(q.Status === 'completed') statusBadge = '<span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-md text-[10px] font-bold uppercase tracking-wider border border-emerald-500/30 shadow-sm backdrop-blur-sm">Left Clinic</span>';

                    const rowClass = q.Status === 'completed' ? 'opacity-50 grayscale' : 'hover:bg-white/5';

                    return `
                    <tr class="transition-colors border-b border-white/5 ${rowClass}">
                        <td class="p-4 font-bold text-2xl text-indigo-400 w-16 drop-shadow">${q.QueueNumber}</td>
                        <td class="p-4">
                            <div class="font-bold text-white drop-shadow-sm">${q.FirstName} ${q.LastName}</div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-1">
                                ${q.Gender} &bull; ${q.DOB} &bull; 
                                <span class="text-indigo-300 font-bold ml-1 bg-indigo-500/20 px-1.5 py-0.5 rounded border border-indigo-500/30">Visits: ${q.PreviousVisits || 0}</span>
                            </div>
                        </td>
                        <td class="p-4 text-right">${statusBadge}</td>
                    </tr>
                    `;
                }).join('');
                
                if (lastQueueHTML !== newHTML) {
                    tbody.innerHTML = newHTML;
                    lastQueueHTML = newHTML;
                }
            } catch(e) {}
        }

        // Poll every 1 second
        setInterval(fetchQueueAndStats, 1000);
        fetchQueueAndStats();
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
