<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

$patientId = $_GET['id'] ?? null;
if(!$patientId) die("Invalid Patient ID");

$stmt = $pdo->prepare("SELECT * FROM Patients WHERE PatientID = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if(!$patient) die("Patient not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] min-h-screen from-slate-900 via-slate-800 to-zinc-900 text-slate-200 font-sans">
    <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center z-10 relative">
        <h1 class="text-2xl font-bold tracking-tight drop-shadow-md">Edit Patient Details</h1>
        <a href="dashboard.php" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-slate-200 rounded-lg font-bold transition-all shadow-sm">Back</a>
    </nav>

    <div class="container mx-auto p-4 max-w-3xl mt-8">
        <div class="bg-white/5 backdrop-blur-3xl p-8 md:p-10 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20">
            <h2 class="text-2xl font-bold mb-8 text-white tracking-tight drop-shadow-md">Patient ID: <span class="text-indigo-400 drop-shadow">#<?= $patient['PatientID'] ?></span></h2>
            
            <form id="editForm" class="space-y-6">
                <input type="hidden" id="patient_id" value="<?= $patient['PatientID'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2">First Name <span class="text-rose-400">*</span></label>
                        <input type="text" id="firstName" value="<?= htmlspecialchars($patient['FirstName']) ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2">Last Name <span class="text-rose-400">*</span></label>
                        <input type="text" id="lastName" value="<?= htmlspecialchars($patient['LastName']) ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2">DOB <span class="text-rose-400">*</span></label>
                        <input type="date" id="dob" value="<?= htmlspecialchars($patient['DOB']) ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm [color-scheme:dark]" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2">Gender <span class="text-rose-400">*</span></label>
                        <select id="gender" class="w-full bg-black/20 border border-white/10 text-white rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm [&>option]:bg-slate-800">
                            <option value="Male" <?= $patient['Gender']=='Male' ? 'selected':'' ?>>Male</option>
                            <option value="Female" <?= $patient['Gender']=='Female' ? 'selected':'' ?>>Female</option>
                            <option value="Other" <?= $patient['Gender']=='Other' ? 'selected':'' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2">Phone Number <span class="text-rose-400">*</span></label>
                    <input type="text" id="phone" value="<?= htmlspecialchars($patient['Phone']) ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                </div>

                <div>
                    <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2">Address</label>
                    <textarea id="address" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" rows="3"><?= htmlspecialchars($patient['Address']) ?></textarea>
                </div>

                <div class="pt-6 flex flex-col md:flex-row gap-4 border-t border-white/10">
                    <button type="submit" class="w-full md:w-2/3 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-200 font-bold py-3.5 rounded-xl shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] transition-all backdrop-blur-md">Save Changes</button>
                    <a href="dashboard.php" class="w-full md:w-1/3 bg-white/5 border border-white/10 hover:bg-white/10 text-slate-200 font-bold py-3.5 rounded-xl text-center shadow-sm hover:shadow-lg transition-all backdrop-blur-md">Cancel</a>
                </div>
                <div id="saveMsg" class="font-bold text-center mt-4 hidden drop-shadow-sm"></div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('editForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                patient_id: document.getElementById('patient_id').value,
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value
            };

            const res = await fetch('../api/update_patient.php', {
                method: 'POST', body: JSON.stringify(data), headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            
            const msg = document.getElementById('saveMsg');
            msg.classList.remove('hidden');
            if (result.success) {
                msg.className = 'text-emerald-400 font-bold text-center mt-3 drop-shadow-sm';
                msg.innerText = 'Patient updated successfully!';
                setTimeout(()=> window.location.href='dashboard.php', 1000);
            } else {
                msg.className = 'text-rose-400 font-bold text-center mt-3 drop-shadow-sm';
                msg.innerText = 'Error: ' + result.error;
            }
        });
    </script>
</body>
</html>
