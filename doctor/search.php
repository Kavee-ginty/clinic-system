<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
?>
<?php
$pageTitle = 'Search Patient - Doctor';
include '../includes/header.php';
?>
<body class="bg-gray-50 flex h-screen overflow-hidden dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_doctor.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-teal-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Search Database</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-teal-600 hover:bg-teal-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1 bg-teal-700 hover:bg-teal-800 rounded font-bold text-sm transition">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Patient Search</h2>
            <input type="text" id="searchInput" class="w-full border-2 border-gray-300 rounded-lg p-4 text-lg focus:outline-none focus:border-teal-500 mb-6" placeholder="Enter name or phone number...">
            
            <div id="searchResults" class="space-y-2">
                <!-- Results go here -->
            </div>
        </div>
        </div>
    </main>

    <script>
        async function performSearch(query) {
            const resBox = document.getElementById('searchResults');
            if(query.length > 0 && query.length < 2) { return; }
            
            const res = await fetch(`../api/search_patient.php?q=${encodeURIComponent(query)}`);
            const patients = await res.json();
            
            if(patients.length === 0) {
                resBox.innerHTML = '<div class="p-6 text-center text-gray-400 font-bold">No patients found.</div>';
                return;
            }
            
            resBox.innerHTML = patients.map(p => `
                <div class="p-4 border rounded-lg hover:shadow-md hover:border-teal-300 bg-gray-50 flex justify-between items-center transition">
                    <div class="flex-1 pr-4">
                        <div class="font-black text-lg text-gray-800 flex items-center justify-between">
                            <div>${p.PatientNumber || 'PT-N/A'} - ${p.FirstName} ${p.LastName}</div>
                            <span class="text-sm bg-teal-100 text-teal-800 px-2 py-1 rounded-full border border-teal-200 shadow-sm ml-4">
                                Visits: ${p.PreviousVisits || 0}
                            </span>
                        </div>
                        <details class="text-sm text-gray-600 mt-2 bg-white border border-gray-200 rounded p-2 shadow-inner group">
                            <summary class="font-bold text-teal-600 cursor-pointer outline-none marker:text-teal-400 group-open:mb-2 text-xs uppercase tracking-wide">
                                ➕ Patient Demographics
                            </summary>
                            <div class="grid grid-cols-2 gap-2 pl-4 border-l-2 border-teal-100 mt-1">
                                <div><span class="font-bold text-gray-400 text-xs uppercase">NIC</span><br>${p.NIC || 'N/A'}</div>
                                <div><span class="font-bold text-gray-400 text-xs uppercase">Age / DOB</span><br>${p.Age || 'N/A'} (${p.DOB})</div>
                                <div><span class="font-bold text-gray-400 text-xs uppercase">Gender</span><br>${p.Gender}</div>
                                <div><span class="font-bold text-gray-400 text-xs uppercase">Phone</span><br>${p.Phone}</div>
                                <div class="col-span-2"><span class="font-bold text-gray-400 text-xs uppercase">Address</span><br>${p.Address || 'N/A'}</div>
                                <div class="col-span-2"><span class="font-bold text-gray-400 text-xs uppercase">Registered On</span><br>${p.RegisteredDate}</div>
                            </div>
                        </details>
                    </div>
                    <a href="history.php?patient_id=${p.PatientID}" target="_blank" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-lg font-black shadow-md border-b-4 border-teal-700 active:border-b-0 active:mt-1 transition">Review History</a>
                </div>
            `).join('');
        }

        document.getElementById('searchInput').addEventListener('input', debounce((e) => {
            performSearch(e.target.value.trim());
        }, 300));
        
        // Initial load of all patients
        performSearch('');
    </script>
</body>
</html>
