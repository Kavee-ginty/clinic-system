<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
$patientId = $_GET['patient_id'] ?? null;
if (!$patientId) die("Patient ID required.");
?>
<?php
$pageTitle = 'Patient History';
include '../includes/header.php';
?>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php 
    if (isset($_SESSION['admin_logged_in'])) {
        include '../includes/sidebar_admin.php'; 
    } else {
        include '../includes/sidebar_doctor.php';
    }
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
    <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center z-10 relative">
        <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Patient History</h1>
        <div class="flex gap-2">
            <a href="../index.php" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg font-medium transition-all shadow-sm">Dashboard</a>
            <a href="javascript:void(0)" onclick="if(window.history.length > 1) { window.history.back(); } else { window.close(); }" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg font-medium transition-all shadow-sm">Back / Close</a>
        </div>
    </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 max-w-5xl mx-auto w-full relative z-0">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight drop-shadow-md">Patient History</h2>
                    <p class="text-slate-400 font-medium mt-1">Review longitudinal medical records</p>
                </div>
            </div>
        
        <div id="patientInfo" class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20 mb-8 flex justify-between items-center relative overflow-hidden">
             <!-- Load via JS -->
        </div>

        <h2 class="text-xl font-bold mb-6 text-white tracking-tight drop-shadow-md">Past Visits</h2>
        <div id="historyContainer" class="space-y-6">
             <!-- Load via JS -->
             <p class="text-slate-400 font-medium p-6 bg-white/5 backdrop-blur-md border border-white/10 rounded-[2rem] shadow-sm animate-pulse text-center">Loading history...</p>
        </div>

        </main>
    </div>

    <script>
        const patientId = <?= json_encode($patientId) ?>;

        async function loadData() {
            // Patient Info
            const pRes = await fetch(`../api/get_patient.php?id=${patientId}`);
            const p = await pRes.json();
            document.getElementById('patientInfo').innerHTML = `
                <div class="relative z-10 w-full">
                    <div class="flex justify-between items-center w-full">
                        <h2 class="text-3xl font-bold text-white drop-shadow-sm">${p.FirstName} ${p.LastName}</h2>
                        <span class="bg-indigo-500/20 text-indigo-300 px-3 py-1 rounded-md text-xs font-semibold border border-indigo-500/30 backdrop-blur-sm shadow-[0_0_10px_rgba(99,102,241,0.2)]">PT-${p.PatientID}</span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-2 mt-4 text-sm font-medium text-slate-300">
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>DOB: ${p.DOB}</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Gender: ${p.Gender}</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Phone: ${p.Phone}</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-4 pt-4 border-t border-white/10 w-full">${p.Address || 'No Address Provided'}</p>
                </div>
                <!-- Soft background accent -->
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-48 h-48 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>
            `;

            // History
            const hRes = await fetch(`../api/get_patient_history.php?patient_id=${patientId}`);
            const history = await hRes.json();

            const container = document.getElementById('historyContainer');
            if(history.length === 0) {
                container.innerHTML = `<div class="bg-white/5 backdrop-blur-md p-8 rounded-[2rem] border border-white/10 text-center text-slate-400 font-medium shadow-sm">No past visits recorded.</div>`;
                return;
            }

            container.innerHTML = history.map(v => `
                <div class="bg-white/5 backdrop-blur-xl p-8 rounded-[2rem] shadow-xl border border-white/10 border-t-white/20 transition-all hover:shadow-2xl hover:bg-white/10">
                    <div class="flex justify-between items-center border-b border-white/10 pb-4 mb-6">
                        <div class="text-lg font-bold text-white flex items-center gap-2 drop-shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-indigo-400 shadow-[0_0_8px_rgba(129,140,248,0.8)]"></span>
                            ${new Date(v.VisitDateTime).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'})}
                        </div>
                        <a href="print_report.php?visit_id=${v.VisitID}" target="_blank" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 hover:border-white/20 rounded-lg text-sm font-medium transition-all backdrop-blur-sm">Print Record</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Complaint</p>
                                <p class="text-slate-200 whitespace-pre-line text-sm">${v.Complaint || '—'}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Diagnosis</p>
                                <p class="text-indigo-200 font-medium whitespace-pre-line text-sm bg-indigo-500/10 p-4 rounded-xl border border-indigo-500/20 backdrop-blur-sm">${v.Diagnosis || '—'}</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Treatment</p>
                                <p class="text-slate-200 whitespace-pre-line text-sm">${v.Treatment || '—'}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Notes</p>
                                <p class="text-slate-300 whitespace-pre-line text-sm bg-black/20 p-4 rounded-xl border border-white/5 shadow-inner">${v.Notes || '—'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        loadData();
    </script>
</body>
</html>
