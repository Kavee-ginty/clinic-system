<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-teal-900 text-white flex-col hidden md:flex shadow-2xl z-10">
        <div class="p-6 border-b border-teal-800">
            <h1 class="text-2xl font-black tracking-tight text-white">Clinic System</h1>
            <p class="text-sm font-semibold text-teal-300 mt-1 uppercase tracking-widest">Medical Staff</p>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-teal-800 rounded-lg font-bold text-white transition">Live Dashboard</a>
            <a href="search.php" class="flex items-center gap-3 p-3 text-teal-200 hover:text-white hover:bg-teal-800 rounded-lg font-bold transition">Search Patient</a>
            <a href="inventory.php" class="flex items-center gap-3 p-3 text-teal-200 hover:text-white hover:bg-teal-800 rounded-lg font-bold transition">Manage Inventory</a>
            <a href="settings.php" class="flex items-center gap-3 p-3 text-teal-200 hover:text-white hover:bg-teal-800 rounded-lg font-bold transition">Print Settings</a>
        </nav>
        
        <div class="p-4 border-t border-teal-800 space-y-2">
            <!-- Doctor Security Module -->
            <div class="p-3 bg-teal-950 rounded-lg mb-4 hidden" id="passwordModule">
                <input type="password" id="docPass" class="w-full bg-teal-900 text-white border border-teal-700 p-2 mb-2 rounded font-bold text-sm placeholder-teal-600" placeholder="New Password">
                <button onclick="changeDoctorPassword()" class="w-full bg-teal-700 hover:bg-teal-600 text-white font-bold p-2 rounded text-xs transition">Save Password</button>
            </div>
            <button onclick="document.getElementById('passwordModule').classList.toggle('hidden')" class="block w-full text-center p-3 text-teal-300 hover:text-white bg-teal-800 rounded-lg font-bold transition text-sm">Security / Password</button>
            
            <a href="../index.php" class="block w-full text-center p-3 text-teal-300 hover:text-white bg-teal-800 rounded-lg font-bold transition text-sm">Home Menu</a>
            <a href="logout.php" class="block w-full text-center p-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold transition shadow-md">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-teal-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Doctor Dashboard</h1>
            <a href="logout.php" class="px-3 py-1 bg-red-600 rounded font-bold text-sm">Logout</a>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-3xl font-black text-gray-800">My Dashboard</h2>
                    <p class="text-gray-500 font-semibold mt-1">Real-time daily operations</p>
                </div>
            </div>

            <!-- Stats Module -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" id="statsGrid">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div><p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Visits Today</p><p class="text-2xl font-black text-teal-600" id="stat_visits">-</p></div>
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

            <!-- Live Queue -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-teal-500 border-x border-b border-gray-100 min-h-[500px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Live Queue Window</h2>
                    <div class="flex items-center gap-2 bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                        </span>
                        <span class="text-xs font-bold text-teal-700 uppercase tracking-widest">Live Syncing</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-400 text-xs uppercase tracking-widest border-b-2 border-gray-100">
                                <th class="p-4 font-black">No.</th>
                                <th class="p-4 font-black">Patient Data</th>
                                <th class="p-4 font-black">Current Status</th>
                                <th class="p-4 font-black text-right">Medical Actions</th>
                            </tr>
                        </thead>
                        <tbody id="queueTableBody">
                            <!-- Populated by JS -->
                             <tr><td colspan="4" class="p-10 text-center font-bold text-gray-400 text-lg">Polling local server for patients...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
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
                     tbody.innerHTML = '<tr><td colspan="4" class="p-20 text-center font-black text-gray-300 text-2xl">The queue is currently empty.</td></tr>';
                     return;
                }

                tbody.innerHTML = queue.map(q => {
                    let statusBadge = '';
                    let actionButtons = '';
                    
                    if(q.Status === 'waiting') {
                        statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold uppercase tracking-wide border border-yellow-200">Waiting</span>';
                        actionButtons = `
                            <button onclick="startVisit(${q.QueueID}, ${q.PatientID})" class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold transition shadow-md shadow-blue-500/30 text-sm">Call Patient & Diagnose</button>
                            <a href="history.php?patient_id=${q.PatientID}" target="_blank" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold transition text-sm hidden md:inline-block">History</a>
                        `;
                    }
                    else if(q.Status === 'with_doctor') {
                        statusBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase tracking-wide animate-pulse border border-blue-200">Consulting Now</span>';
                        actionButtons = `
                            <a href="add_visit.php?queue_id=${q.QueueID}&patient_id=${q.PatientID}" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold transition shadow-md shadow-teal-500/30 text-sm">Continue Diagnosis</a>
                            <a href="history.php?patient_id=${q.PatientID}" target="_blank" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold transition text-sm">History</a>
                        `;
                    }
                    else if(q.Status === 'completed') {
                        statusBadge = '<span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold uppercase tracking-wide border border-green-200">Discharged</span>';
                        actionButtons = `
                            <a href="history.php?patient_id=${q.PatientID}" target="_blank" class="px-5 py-2 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-lg font-bold transition text-sm">View Record</a>
                        `;
                    }

                    // Ghosting out completed patients so waiting patients stand out
                    const rowClass = q.Status === 'completed' ? 'opacity-50 hover:opacity-100' : 'bg-white shadow-sm';

                    return `
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100 group ${rowClass}">
                        <td class="p-4 font-black text-3xl text-gray-300 group-hover:text-teal-500 transition">${q.QueueNumber}</td>
                        <td class="p-4">
                            <div class="font-bold text-lg text-gray-800">${q.FirstName} ${q.LastName}</div>
                            <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-1">${q.Gender} &bull; DOB: ${q.DOB}</div>
                        </td>
                        <td class="p-4">${statusBadge}</td>
                        <td class="p-4 text-right space-x-2">
                            ${actionButtons}
                        </td>
                    </tr>
                    `;
                }).join('');
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
