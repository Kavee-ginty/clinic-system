<?php
session_start();
$_SESSION['receptionist_view'] = true;
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
                    <h2 class="text-3xl font-black text-gray-800 dark:text-gray-100">Receptionist Desk</h2>
                    <p class="text-gray-500 dark:text-gray-400 font-semibold mt-1">Patient intake & flow management</p>
                </div>
            </div>

            <!-- Stats Module -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="statsGrid">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Visits Today</p><p class="text-2xl font-black text-blue-600" id="stat_visits">-</p></div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Waiting Now</p><p class="text-2xl font-black text-yellow-500 animate-pulse" id="stat_waiting">-</p></div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Registered Today</p><p class="text-2xl font-black text-purple-600" id="stat_registered">-</p></div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Total Patients</p><p class="text-2xl font-black text-gray-700 dark:text-gray-300" id="stat_total">-</p></div>
                </div>
            </div>

            <!-- Dual Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
                
                <!-- Left Column Controls -->
                <div class="col-span-1 space-y-6">
                    
                    <!-- Search Patient -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 border-t-4 border-blue-500">
                        <h2 class="text-lg font-black mb-4 text-gray-800 dark:text-gray-100">1. Select Patient</h2>
                        <input type="text" id="searchInput" class="w-full border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl p-3 mb-2 focus:outline-none focus:border-blue-500 focus:ring-0 font-bold" placeholder="Search Name, Phone...">
                        <div id="searchResults" class="rounded-xl w-full max-h-48 overflow-y-auto space-y-1 mt-2 pr-1"></div>
                    </div>

                    <!-- Register New -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 border-t-4 border-green-500">
                        <h2 class="text-lg font-black mb-4 text-gray-800 dark:text-gray-100">2. Pre-Register New</h2>
                        <form id="addPatientForm" class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div><input type="text" id="firstName" autocomplete="given-name" placeholder="First Name *" required class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full focus:border-green-500 text-sm font-semibold"></div>
                                <div><input type="text" id="lastName" autocomplete="family-name" placeholder="Last Name *" required class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full focus:border-green-500 text-sm font-semibold"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase">DOB *</label>
                                    <input type="text" id="dob" autocomplete="bday" placeholder="yyyy/mm/dd" pattern="\d{4}/\d{2}/\d{2}" required class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full text-sm font-semibold text-gray-600 dark:text-gray-300 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase">Gender *</label>
                                    <select id="gender" autocomplete="sex" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full text-sm font-semibold text-gray-600 dark:text-gray-300 focus:border-green-500">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <input type="text" id="phone" autocomplete="tel" placeholder="Phone Number" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full text-sm font-semibold focus:border-green-500">
                                <input type="text" id="nic" autocomplete="off" placeholder="NIC / ID" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full text-sm font-semibold focus:border-green-500">
                            </div>
                            <textarea id="address" autocomplete="street-address" placeholder="Residential Address" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 w-full text-sm font-semibold focus:border-green-500" rows="2"></textarea>
                            
                            <div class="grid grid-cols-2 gap-2 pt-2">
                                <button type="submit" id="btnRegisterOnly" class="bg-gray-600 text-white p-3 rounded-lg hover:bg-gray-700 font-bold transition shadow-md text-xs">Register Only</button>
                                <button type="submit" id="btnRegisterQueue" class="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 font-bold transition shadow-md shadow-green-500/20 text-xs">Register & Queue</button>
                            </div>
                            <div id="regMsg" class="text-sm font-bold text-center hidden mt-2"></div>
                        </form>
                    </div>

                </div>

                <!-- Right Column Active & Completed Queues -->
                <div class="col-span-1 lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 h-auto">
                    
                    <!-- Active Dispatch Line -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 border-t-4 border-purple-500 flex flex-col min-h-[500px]">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-black text-gray-800 dark:text-gray-100">Dispatch Line</h2>
                            <div class="flex items-center gap-2 bg-purple-50 dark:bg-purple-900/30 px-3 py-1 rounded-full">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                                </span>
                                <span class="text-[9px] font-bold text-purple-700 dark:text-purple-300 uppercase tracking-widest">Live Sync</span>
                            </div>
                        </div>

                        <div class="overflow-y-auto flex-1 max-h-[550px]">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-gray-400 text-xs uppercase tracking-widest border-b-2 border-gray-100 dark:border-gray-700">
                                        <th class="p-3 font-black">Token</th>
                                        <th class="p-3 font-black">Patient Data</th>
                                        <th class="p-3 font-black text-right">Stage</th>
                                    </tr>
                                </thead>
                                <tbody id="queueTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Completed Today -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 border-t-4 border-green-500 flex flex-col min-h-[500px]">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-black text-gray-800 dark:text-gray-100">Completed Today</h2>
                            <span class="px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-[10px] font-bold uppercase tracking-widest border border-green-250">Billing</span>
                        </div>

                        <div class="overflow-y-auto flex-1 max-h-[550px]">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-gray-400 text-xs uppercase tracking-widest border-b-2 border-gray-100 dark:border-gray-700">
                                        <th class="p-3 font-black">Token</th>
                                        <th class="p-3 font-black">Patient</th>
                                        <th class="p-3 font-black text-right">Bill</th>
                                    </tr>
                                </thead>
                                <tbody id="completedTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Edit Patient Modal -->
    <div id="editPatientModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 dark:text-white rounded-2xl shadow-xl w-full max-w-lg p-6 m-4 relative border border-gray-100 dark:border-gray-700">
            <button onclick="closeEditPatientModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-black text-gray-800 dark:text-gray-100 mb-4">Edit Patient Details</h3>
            <form id="editPatientForm" class="space-y-3">
                <input type="hidden" id="editPatientId">
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" id="editFirstName" placeholder="First Name *" required class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold">
                    <input type="text" id="editLastName" placeholder="Last Name *" required class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" id="editDob" placeholder="yyyy/mm/dd" pattern="\d{4}/\d{2}/\d{2}" required class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold">
                    <select id="editGender" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" id="editPhone" placeholder="Phone Number" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold">
                    <input type="text" id="editNic" placeholder="NIC / ID" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold">
                </div>
                <textarea id="editAddress" placeholder="Residential Address" rows="2" class="border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 w-full text-sm font-semibold"></textarea>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl transition">Save Patient</button>
                <div id="editPatientMsg" class="text-sm font-bold text-center hidden"></div>
            </form>
        </div>
    </div>

    <!-- Bill Details Modal -->
    <div id="billModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 dark:text-white rounded-2xl shadow-xl w-full max-w-md p-6 m-4 relative border border-gray-100 dark:border-gray-700">
            <button onclick="closeBillModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-black text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <span>🧾</span> Visit Invoice Details
            </h3>
            <div id="billModalContent" class="space-y-4">
                <!-- Dynamic details -->
            </div>
        </div>
    </div>

    <!-- Patient History Modal -->
    <div id="historyModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 dark:text-white rounded-2xl shadow-xl w-full max-w-4xl h-[85vh] p-6 m-4 relative border border-gray-100 dark:border-gray-700 flex flex-col">
            <button onclick="closeHistoryModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-650 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-black text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <span>📋</span> Patient Medical History
            </h3>
            <div class="flex-1 min-h-0 bg-gray-50 dark:bg-gray-950 rounded-xl overflow-hidden">
                <iframe id="historyIframe" class="w-full h-full border-0" src=""></iframe>
            </div>
        </div>
    </div>

    <script>
        const showDob = (value) => (value || '').replace(/-/g, '/');
        const dbDob = (value) => (value || '').replace(/\//g, '-');

        // Track submit button clicked
        let submitAction = 'queue';
        document.getElementById('btnRegisterOnly').addEventListener('click', () => { submitAction = 'only'; });
        document.getElementById('btnRegisterQueue').addEventListener('click', () => { submitAction = 'queue'; });

        // Registration
        document.getElementById('addPatientForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const action = submitAction;
            
            const data = {
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                dob: dbDob(document.getElementById('dob').value),
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
                if (action === 'queue') {
                    await addToQueue(result.patient_id, false);
                    msg.className = 'text-green-600 text-sm font-bold mt-2';
                    msg.innerText = 'Registered & Queued!';
                } else {
                    msg.className = 'text-green-600 text-sm font-bold mt-2';
                    msg.innerText = 'Registered Successfully!';
                }
                
                document.getElementById('addPatientForm').reset();
                setTimeout(()=>msg.classList.add('hidden'), 3000);
                fetchQueueAndStats();
            } else {
                msg.className = 'text-red-600 text-sm font-bold mt-2';
                msg.innerText = result.error;
            }
        });

        // Search Result Keyboard Selection
        let selectedResultIndex = -1;
        document.getElementById('searchInput').addEventListener('keydown', (e) => {
            const results = document.querySelectorAll('#searchResults > div');
            if (results.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedResultIndex = (selectedResultIndex + 1) % results.length;
                highlightSearchResult(results);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedResultIndex = (selectedResultIndex - 1 + results.length) % results.length;
                highlightSearchResult(results);
            } else if (e.key === 'Enter') {
                if (selectedResultIndex >= 0 && selectedResultIndex < results.length) {
                    e.preventDefault();
                    // Trigger "Send Queue" button click
                    const btn = results[selectedResultIndex].querySelector('button');
                    if (btn) btn.click();
                }
            } else if (e.key === 'Escape') {
                document.getElementById('searchResults').innerHTML = '';
                selectedResultIndex = -1;
            }
        });

        function highlightSearchResult(results) {
            results.forEach((el, idx) => {
                if (idx === selectedResultIndex) {
                    el.classList.add('bg-blue-100', 'border-blue-300', 'dark:bg-blue-900/40');
                    el.classList.remove('bg-white', 'border-gray-100', 'dark:bg-gray-850');
                    el.scrollIntoView({ block: 'nearest' });
                } else {
                    el.classList.remove('bg-blue-100', 'border-blue-300', 'dark:bg-blue-900/40');
                    el.classList.add('bg-white', 'border-gray-100', 'dark:bg-gray-850');
                }
            });
        }

        // Search (Debounced)
        document.getElementById('searchInput').addEventListener('input', debounce(async (e) => {
            const query = e.target.value;
            const resBox = document.getElementById('searchResults');
            selectedResultIndex = -1;
            if(query.length < 2) { resBox.innerHTML = ''; return; }
            
            const res = await fetch(`../api/search_patient.php?q=${encodeURIComponent(query)}`);
            const patients = await res.json();
            
            if(patients.length === 0){
                resBox.innerHTML = '<div class="p-3 text-red-400 font-bold text-sm text-center">No matching patients.</div>';
                return;
            }

            resBox.innerHTML = patients.map((p, idx) => `
                <div class="p-3 border border-gray-100 dark:border-gray-700 rounded-xl hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer flex justify-between items-center transition group mb-2 bg-white dark:bg-gray-750 shadow-sm" data-index="${idx}">
                    <div>
                        <div class="font-black text-gray-800 dark:text-gray-100 text-sm">
                            <a href="#" onclick="openPatientHistoryModal(${p.PatientID}); return false;" class="hover:underline text-blue-600 dark:text-blue-400">${p.FirstName} ${p.LastName}</a>
                        </div>
                        <div class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">${p.Phone || 'No phone'} &bull; ${showDob(p.DOB)}</div>
                    </div>
                    <div class="flex gap-1">
                        <button onclick="openEditPatientModal(${p.PatientID})" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-2 py-2 rounded-lg transition">Edit</button>
                        <button onclick="addToQueue(${p.PatientID}, true)" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold px-3 py-2 rounded-lg shadow transition transform active:scale-95">Send Queue</button>
                    </div>
                </div>
            `).join('');
        }, 300));

        // Add to Queue
        async function addToQueue(patientId, showAlert) {
            // Optimistic UI Ghost Row on active queue
            const tbody = document.getElementById('queueTableBody');
            const ghostRow = `
                <tr class="bg-purple-50 dark:bg-purple-900/10 transition border-b border-purple-100 dark:border-purple-900 animate-pulse">
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

        async function removeFromQueue(queueId) {
            if (!confirm("Remove this patient from today's queue?")) return;
            const res = await fetch('../api/remove_queue.php', {
                method: 'POST',
                body: JSON.stringify({queue_id: queueId}),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            showToast(result.success ? 'Removed from queue.' : `Error: ${result.error}`, result.success ? 'success' : 'error');
            fetchQueueAndStats();
        }

        async function markPaid(visitId) {
            const res = await fetch('../api/mark_paid.php', {
                method: 'POST',
                body: JSON.stringify({visit_id: visitId}),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            showToast(result.success ? 'Marked paid.' : `Error: ${result.error}`, result.success ? 'success' : 'error');
            fetchQueueAndStats();
        }

        async function openEditPatientModal(patientId) {
            const res = await fetch(`../api/get_patient.php?id=${patientId}`);
            const p = await res.json();
            document.getElementById('editPatientId').value = p.PatientID;
            document.getElementById('editFirstName').value = p.FirstName || '';
            document.getElementById('editLastName').value = p.LastName || '';
            document.getElementById('editDob').value = showDob(p.DOB);
            document.getElementById('editGender').value = p.Gender || 'Male';
            document.getElementById('editPhone').value = p.Phone || '';
            document.getElementById('editNic').value = p.NIC || '';
            document.getElementById('editAddress').value = p.Address || '';
            document.getElementById('editPatientMsg').classList.add('hidden');
            document.getElementById('editPatientModal').classList.remove('hidden');
        }

        function closeEditPatientModal() {
            document.getElementById('editPatientModal').classList.add('hidden');
        }

        document.getElementById('editPatientForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                patient_id: document.getElementById('editPatientId').value,
                first_name: document.getElementById('editFirstName').value,
                last_name: document.getElementById('editLastName').value,
                dob: dbDob(document.getElementById('editDob').value),
                gender: document.getElementById('editGender').value,
                phone: document.getElementById('editPhone').value,
                nic: document.getElementById('editNic').value,
                address: document.getElementById('editAddress').value
            };
            const res = await fetch('../api/update_patient.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            const msg = document.getElementById('editPatientMsg');
            msg.classList.remove('hidden');
            msg.className = result.success ? 'text-green-600 text-sm font-bold text-center' : 'text-red-600 text-sm font-bold text-center';
            msg.innerText = result.success ? 'Saved.' : `Error: ${result.error}`;
            if (result.success) {
                setTimeout(closeEditPatientModal, 600);
                fetchQueueAndStats();
                document.getElementById('searchInput').dispatchEvent(new Event('input'));
            }
        });

        // Fetch Queue & Stats Polling
        let lastQueueHTML = '';
        let lastCompletedHTML = '';
        
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
                
                const activeTbody = document.getElementById('queueTableBody');
                const completedTbody = document.getElementById('completedTableBody');

                // Filter Active and Completed
                const activeQueue = queue.filter(q => q.Status !== 'completed');
                const completedQueue = queue.filter(q => q.Status === 'completed');

                // Render Active Queue
                if(activeQueue.length === 0) {
                     const emptyActiveHTML = '<tr><td colspan="3" class="p-10 text-center font-black text-gray-300 text-md">Waiting line is empty.</td></tr>';
                     if (lastQueueHTML !== emptyActiveHTML) {
                         activeTbody.innerHTML = emptyActiveHTML;
                         lastQueueHTML = emptyActiveHTML;
                     }
                } else {
                    const newActiveHTML = activeQueue.map(q => {
                        let statusBadge = '';
                        if(q.Status === 'waiting') statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-bold uppercase tracking-widest border border-yellow-200">Wait Area</span>';
                        else if(q.Status === 'with_doctor') statusBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold uppercase tracking-widest border border-blue-200 animate-pulse">In Room</span>';

                        return `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <td class="p-3 font-black text-2xl text-purple-600 dark:text-purple-400 w-16">${q.QueueNumber}</td>
                            <td class="p-3">
                                <div class="font-bold text-gray-800 dark:text-gray-100">
                                    <a href="#" onclick="openPatientHistoryModal(${q.PatientID}); return false;" class="hover:underline text-blue-600 dark:text-blue-400">${q.FirstName} ${q.LastName}</a>
                                </div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mt-1">
                                    ${q.Gender} &bull; ${showDob(q.DOB)} &bull; 
                                    <span class="text-blue-600 dark:text-blue-400 font-black ml-1 bg-blue-50 dark:bg-blue-900/30 px-1 py-0.5 rounded border border-blue-100 dark:border-blue-900">Visits: ${q.PreviousVisits || 0}</span>
                                </div>
                            </td>
                            <td class="p-3 text-right">
                                ${statusBadge}
                                <div class="mt-2 flex justify-end gap-1">
                                    <button onclick="openEditPatientModal(${q.PatientID})" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-[10px] font-bold">Edit</button>
                                    <button onclick="removeFromQueue(${q.QueueID})" class="px-2 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded text-[10px] font-bold">Remove</button>
                                </div>
                            </td>
                        </tr>
                        `;
                    }).join('');

                    if (lastQueueHTML !== newActiveHTML) {
                        activeTbody.innerHTML = newActiveHTML;
                        lastQueueHTML = newActiveHTML;
                    }
                }

                // Render Completed Queue
                if(completedQueue.length === 0) {
                     const emptyCompletedHTML = '<tr><td colspan="3" class="p-10 text-center font-black text-gray-300 text-md">No completed visits today.</td></tr>';
                     if (lastCompletedHTML !== emptyCompletedHTML) {
                         completedTbody.innerHTML = emptyCompletedHTML;
                         lastCompletedHTML = emptyCompletedHTML;
                     }
                } else {
                    const newCompletedHTML = completedQueue.map(q => {
                        const billVal = parseFloat(q.TotalBill || 0);
                        const billText = `Rs. ${billVal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                        return `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <td class="p-3 font-black text-2xl text-green-600 dark:text-green-400 w-16">${q.QueueNumber}</td>
                            <td class="p-3">
                                <div class="font-bold text-gray-800 dark:text-gray-100">
                                    <a href="#" onclick="openPatientHistoryModal(${q.PatientID}); return false;" class="hover:underline text-blue-600 dark:text-blue-400">${q.FirstName} ${q.LastName}</a>
                                </div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mt-1">
                                    ${q.Gender} &bull; ${showDob(q.DOB)}
                                </div>
                            </td>
                            <td class="p-3 text-right">
                                <button onclick="viewBillDetails(${q.VisitID})" class="text-xs font-black text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 px-2.5 py-1.5 rounded-lg border border-green-200 dark:border-green-800 transition">
                                    ${billText}
                                </button>
                                ${q.IsPaid == 1 ? '<div class="mt-1 text-[10px] font-black text-green-700">Paid</div>' : `<button onclick="markPaid(${q.VisitID})" class="mt-1 text-[10px] font-black text-white bg-teal-600 hover:bg-teal-700 px-2 py-1 rounded">Mark Paid</button>`}
                            </td>
                        </tr>
                        `;
                    }).join('');

                    if (lastCompletedHTML !== newCompletedHTML) {
                        completedTbody.innerHTML = newCompletedHTML;
                        lastCompletedHTML = newCompletedHTML;
                    }
                }
            } catch(e) {}
        }

        // Bill Details Modal handlers
        async function viewBillDetails(visitId) {
            if (!visitId) return;
            const modal = document.getElementById('billModal');
            const content = document.getElementById('billModalContent');
            modal.classList.remove('hidden');
            content.innerHTML = '<div class="text-center font-bold text-gray-500 py-8 animate-pulse">Loading bill details...</div>';
            
            try {
                const res = await fetch(`../api/get_visit_details.php?visit_id=${visitId}`);
                const data = await res.json();
                if (data.success) {
                    const visit = data.visit;
                    const drugs = data.drugs;
                    
                    let drugsHTML = '';
                    if (drugs.length > 0) {
                        drugsHTML = `
                            <div class="border-t dark:border-gray-700 pt-3 mt-3">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Prescribed Items</div>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    ${drugs.map(d => `
                                        <div class="flex justify-between text-sm">
                                            <div>
                                                <span class="font-bold text-gray-800 dark:text-gray-200">${d.DrugName}</span>
                                                <span class="text-gray-500 dark:text-gray-400 text-xs font-semibold block">${d.Dose} &bull; ${d.Frequency} &bull; ${d.Duration}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Qty: ${d.Quantity}</span>
                                                <span class="font-bold text-gray-800 dark:text-gray-200 block">Rs. ${parseFloat(d.TotalCost).toFixed(2)}</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    } else {
                        drugsHTML = '<div class="text-gray-500 italic text-sm mt-2">No drugs prescribed.</div>';
                    }
                    
                    const consultationFee = parseFloat(visit.VisitFee || 0);
                    const totalBill = parseFloat(visit.TotalBill || 0);
                    const pharmacyFee = totalBill - consultationFee;
                    
                    content.innerHTML = `
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Patient Name:</span>
                                <span class="font-black text-gray-800 dark:text-gray-100">${visit.FirstName} ${visit.LastName}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Date & Time:</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">${new Date(visit.VisitDateTime).toLocaleString()}</span>
                            </div>
                        </div>
                        
                        ${drugsHTML}
                        
                        <div class="border-t dark:border-gray-700 pt-3 mt-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Consultation Fee:</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Rs. ${consultationFee.toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Pharmacy Fee:</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Rs. ${pharmacyFee.toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between border-t-2 border-dashed dark:border-gray-700 pt-2 text-lg font-black text-teal-700 dark:text-teal-400">
                                <span>Total Bill:</span>
                                <span>Rs. ${totalBill.toFixed(2)}</span>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            ${visit.IsPaid == 1 ? '<div class="w-full text-center bg-green-50 text-green-700 font-black py-2.5 rounded-xl mb-2">Paid</div>' : `<button onclick="markPaid(${visit.VisitID}); closeBillModal();" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl transition mb-2">Mark Paid</button>`}
                            <button onclick="closeBillModal()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 rounded-xl transition">Close Invoice</button>
                        </div>
                    `;
                } else {
                    content.innerHTML = `<div class="text-red-500 text-center font-bold py-4">Error: ${data.error}</div>`;
                }
            } catch (e) {
                content.innerHTML = `<div class="text-red-500 text-center font-bold py-4">Error fetching details.</div>`;
            }
        }

        function closeBillModal() {
            document.getElementById('billModal').classList.add('hidden');
        }

        function openPatientHistoryModal(patientId) {
            const modal = document.getElementById('historyModal');
            const iframe = document.getElementById('historyIframe');
            iframe.src = `../doctor/history.php?patient_id=${patientId}&embed=true`;
            modal.classList.remove('hidden');
        }

        function closeHistoryModal() {
            const modal = document.getElementById('historyModal');
            const iframe = document.getElementById('historyIframe');
            modal.classList.add('hidden');
            iframe.src = '';
        }

        // Poll every 1 second
        setInterval(fetchQueueAndStats, 1000);
        fetchQueueAndStats();
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
