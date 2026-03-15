<?php
$pageTitle = 'Receptionist Desk';
include '../includes/header.php';
?>
<body class="bg-gray-50 flex h-screen overflow-hidden dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_receptionist.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-blue-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Front Desk</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-blue-600 hover:bg-blue-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="../index.php" class="px-3 py-1 bg-blue-700 hover:bg-blue-800 rounded font-bold text-sm transition">Exit</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-3xl font-black text-gray-800">Receptionist Desk</h2>
                    <p class="text-gray-500 font-semibold mt-1">Patient intake & flow management</p>
                </div>
            </div>

            <!-- Stats Module -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="statsGrid">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Visits Today</p><p class="text-2xl font-black text-blue-600" id="stat_visits">-</p></div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Waiting Now</p><p class="text-2xl font-black text-yellow-500 animate-pulse" id="stat_waiting">-</p></div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Registered Today</p><p class="text-2xl font-black text-purple-600" id="stat_registered">-</p></div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Total Patients</p><p class="text-2xl font-black text-gray-700" id="stat_total">-</p></div>
                </div>
            </div>

            <!-- Dual Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
                
                <!-- Left Column Controls -->
                <div class="col-span-1 space-y-6">
                    
                    <!-- Search Patient -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-t-4 border-blue-500">
                        <h2 class="text-lg font-black mb-4 text-gray-800">1. Select Patient</h2>
                        <input type="text" id="searchInput" class="w-full border-2 border-gray-200 rounded-xl p-3 mb-2 focus:outline-none focus:border-blue-500 focus:ring-0 font-bold" placeholder="Search Name, Phone...">
                        <div id="searchResults" class="rounded-xl w-full max-h-48 overflow-y-auto space-y-1 mt-2 pr-1"></div>
                    </div>

                    <!-- Register New -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-t-4 border-green-500">
                        <h2 class="text-lg font-black mb-4 text-gray-800">2. Pre-Register New</h2>
                        <form id="addPatientForm" class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div><input type="text" id="firstName" placeholder="First Name *" required class="border-2 border-gray-100 rounded-lg p-2 w-full focus:border-green-500 text-sm font-semibold"></div>
                                <div><input type="text" id="lastName" placeholder="Last Name *" required class="border-2 border-gray-100 rounded-lg p-2 w-full focus:border-green-500 text-sm font-semibold"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase">DOB *</label>
                                    <input type="date" id="dob" required class="border-2 border-gray-100 rounded-lg p-2 w-full text-sm font-semibold text-gray-600 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase">Gender *</label>
                                    <select id="gender" class="border-2 border-gray-100 rounded-lg p-2 w-full text-sm font-semibold text-gray-600 focus:border-green-500">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <input type="text" id="phone" placeholder="Phone Number *" required class="border-2 border-gray-100 rounded-lg p-2 w-full text-sm font-semibold focus:border-green-500">
                                <input type="text" id="nic" placeholder="NIC / ID" class="border-2 border-gray-100 rounded-lg p-2 w-full text-sm font-semibold focus:border-green-500">
                            </div>
                            <textarea id="address" placeholder="Residential Address" class="border-2 border-gray-100 rounded-lg p-2 w-full text-sm font-semibold focus:border-green-500" rows="2"></textarea>
                            <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 font-bold transition shadow-md shadow-green-500/20">Register & Send to Queue</button>
                            <div id="regMsg" class="text-sm font-bold text-center hidden mt-2"></div>
                        </form>
                    </div>

                </div>

                <!-- Right Column Queue -->
                <div class="col-span-1 lg:col-span-2 flex flex-col">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-t-4 border-purple-500 flex-1 flex flex-col min-h-[500px]">
                        
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-black text-gray-800">Today's Dispatch Line</h2>
                            <div class="flex items-center gap-2 bg-purple-50 px-3 py-1 rounded-full">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                                </span>
                                <span class="text-xs font-bold text-purple-700 uppercase tracking-widest">Live Syncing</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-gray-400 text-xs uppercase tracking-widest border-b-2 border-gray-100">
                                        <th class="p-3 font-black">Token</th>
                                        <th class="p-3 font-black">Patient Data</th>
                                        <th class="p-3 font-black text-right">Doctor Stage</th>
                                    </tr>
                                </thead>
                                <tbody id="queueTableBody">
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
                msg.className = 'text-green-600 text-sm font-bold mt-2';
                msg.innerText = 'Registed & Queued!';
                
                document.getElementById('addPatientForm').reset();
                setTimeout(()=>msg.classList.add('hidden'), 3000);
            } else {
                msg.className = 'text-red-600 text-sm font-bold mt-2';
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
                resBox.innerHTML = '<div class="p-3 text-red-400 font-bold text-sm text-center">No matching patients.</div>';
                return;
            }

            resBox.innerHTML = patients.map(p => `
                <div class="p-3 border border-gray-100 rounded-xl hover:bg-blue-50 cursor-pointer flex justify-between items-center transition group mb-2 bg-white shadow-sm">
                    <div>
                        <div class="font-black text-gray-800 text-sm">${p.FirstName} ${p.LastName}</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">${p.Phone} &bull; ${p.DOB}</div>
                    </div>
                    <button onclick="addToQueue(${p.PatientID}, true)" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold px-3 py-2 rounded-lg shadow transition transform active:scale-95">Send Queue</button>
                </div>
            `).join('');
        }, 300));

        // Add to Queue
        async function addToQueue(patientId, showAlert) {
            // Optimistic UI Ghost Row
            const tbody = document.getElementById('queueTableBody');
            const ghostRow = `
                <tr class="bg-purple-50 transition border-b border-purple-100 animate-pulse">
                    <td class="p-3 font-black text-2xl text-purple-300 w-16">--</td>
                    <td class="p-3">
                        <div class="font-bold text-gray-400">Syncing to Dispatch Line...</div>
                        <div class="text-[10px] text-gray-300 font-bold uppercase tracking-wider mt-1">Sending to Doctor</div>
                    </td>
                    <td class="p-3 text-right">
                        <span class="px-3 py-1 bg-purple-100 text-purple-500 rounded-full text-[10px] font-bold uppercase tracking-widest border border-purple-200">Dispatching</span>
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
                     const emptyHTML = '<tr><td colspan="3" class="p-10 text-center font-black text-gray-300 text-xl">Waiting line is empty.</td></tr>';
                     if (lastQueueHTML !== emptyHTML) {
                         tbody.innerHTML = emptyHTML;
                         lastQueueHTML = emptyHTML;
                     }
                     return;
                }

                const newHTML = queue.map(q => {
                    let statusBadge = '';
                    if(q.Status === 'waiting') statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-bold uppercase tracking-widest border border-yellow-200">Wait Area</span>';
                    else if(q.Status === 'with_doctor') statusBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold uppercase tracking-widest border border-blue-200 animate-pulse">In Room</span>';
                    else if(q.Status === 'completed') statusBadge = '<span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-green-200">Left Clinic</span>';

                    const rowClass = q.Status === 'completed' ? 'opacity-40' : 'bg-white';

                    return `
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100 ${rowClass}">
                        <td class="p-3 font-black text-2xl text-purple-600 w-16">${q.QueueNumber}</td>
                        <td class="p-3">
                            <div class="font-bold text-gray-800">${q.FirstName} ${q.LastName}</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">
                                ${q.Gender} &bull; ${q.DOB} &bull; 
                                <span class="text-blue-600 font-black ml-1 bg-blue-50 px-1 py-0.5 rounded border border-blue-100">Visits: ${q.PreviousVisits || 0}</span>
                            </div>
                        </td>
                        <td class="p-3 text-right">${statusBadge}</td>
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
