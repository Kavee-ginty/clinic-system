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
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_doctor.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 md:hidden flex justify-between items-center shadow-lg">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Search Database</h1>
            <div class="flex gap-2">
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 rounded-lg font-medium text-sm transition-all border border-white/10">Main Menu</a>
                <a href="dashboard.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 rounded-lg font-medium text-sm transition-all border border-white/10">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 max-w-5xl mx-auto w-full relative z-0">
            <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20">
                <h2 class="text-2xl font-bold text-white tracking-tight mb-6 drop-shadow-md">Patient Search</h2>
                <input type="text" id="searchInput" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-4 text-lg font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 mb-6 shadow-inner backdrop-blur-sm transition-all" placeholder="Enter name or phone number...">
                
                <div id="searchResults" class="space-y-4">
                    <!-- Results go here -->
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
                resBox.innerHTML = '<div class="p-8 text-center text-slate-400 font-medium">No patients found.</div>';
                return;
            }
            
            resBox.innerHTML = patients.map(p => `
                <div class="p-6 border border-white/10 rounded-2xl hover:bg-white/10 bg-white/5 backdrop-blur-md flex justify-between items-center transition-all group shadow-sm hover:shadow-xl hover:border-white/20">
                    <div class="flex-1 pr-6">
                        <div class="font-bold text-lg text-white flex items-center justify-between">
                            <div>${p.PatientNumber || 'PT-N/A'} - ${p.FirstName} ${p.LastName}</div>
                            <span class="text-sm bg-indigo-500/20 text-indigo-300 font-semibold px-2.5 py-1 rounded-md border border-indigo-500/30 ml-4 backdrop-blur-sm">
                                Visits: ${p.PreviousVisits || 0}
                            </span>
                        </div>
                        <details class="text-sm text-slate-300 mt-3 bg-black/20 border border-white/5 rounded-xl p-4 shadow-inner backdrop-blur-sm">
                            <summary class="font-semibold text-indigo-400 cursor-pointer outline-none marker:text-indigo-400 hover:text-indigo-300 text-xs uppercase tracking-widest transition-colors">
                                Patient Demographics
                            </summary>
                            <div class="grid grid-cols-2 gap-4 pl-4 border-l-2 border-indigo-500/30 mt-4">
                                <div><span class="font-medium text-slate-500 text-[10px] uppercase tracking-wider block mb-1">NIC</span><span class="font-medium text-slate-300">${p.NIC || 'N/A'}</span></div>
                                <div><span class="font-medium text-slate-500 text-[10px] uppercase tracking-wider block mb-1">Age / DOB</span><span class="font-medium text-slate-300">${p.Age || 'N/A'} (${p.DOB})</span></div>
                                <div><span class="font-medium text-slate-500 text-[10px] uppercase tracking-wider block mb-1">Gender</span><span class="font-medium text-slate-300">${p.Gender}</span></div>
                                <div><span class="font-medium text-slate-500 text-[10px] uppercase tracking-wider block mb-1">Phone</span><span class="font-medium text-slate-300">${p.Phone}</span></div>
                                <div class="col-span-2"><span class="font-medium text-slate-500 text-[10px] uppercase tracking-wider block mb-1">Address</span><span class="font-medium text-slate-300">${p.Address || 'N/A'}</span></div>
                                <div class="col-span-2"><span class="font-medium text-slate-500 text-[10px] uppercase tracking-wider block mb-1">Registered On</span><span class="font-medium text-slate-300">${p.RegisteredDate}</span></div>
                            </div>
                        </details>
                    </div>
                    <a href="history.php?patient_id=${p.PatientID}" target="_blank" class="bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 border border-indigo-500/30 px-6 py-3 rounded-xl font-medium shadow-sm hover:shadow-[0_0_15px_rgba(99,102,241,0.3)] transition-all backdrop-blur-md">Review History</a>
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
