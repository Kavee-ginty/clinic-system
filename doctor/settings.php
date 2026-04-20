<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

$settingsStmt = $pdo->query("SELECT * FROM Settings");
$settings = [];
while ($row = $settingsStmt->fetch()) {
    $settings[$row['SettingKey']] = $row['SettingValue'];
}
?>
<?php
$pageTitle = 'Print Settings - Doctor';
include '../includes/header.php';
?>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_doctor.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center z-10 relative md:hidden">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Customize Print Settings</h1>
            <div class="flex gap-2">
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 rounded-lg font-medium text-sm transition-all border border-white/10 shadow-sm">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1.5 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 rounded-lg font-medium text-sm transition-all border border-indigo-500/30 shadow-sm">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 flex justify-center relative z-0">
            <div class="w-full max-w-4xl bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20 h-fit">
                <h2 class="text-2xl font-bold mb-6 text-white tracking-tight drop-shadow-md">Edit Clinic Data (Shows on printed reports)</h2>
                <form id="settingsForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Clinic Name *</label>
                            <input type="text" id="clinic_name" value="<?= htmlspecialchars($settings['clinic_name'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Clinic Email *</label>
                            <input type="text" id="clinic_email" value="<?= htmlspecialchars($settings['clinic_email'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Clinic Address *</label>
                            <input type="text" id="clinic_address" value="<?= htmlspecialchars($settings['clinic_address'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Phone Number(s) *</label>
                            <input type="text" id="clinic_phone" value="<?= htmlspecialchars($settings['clinic_phone'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Doctor's Name *</label>
                            <input type="text" id="doctor_name" value="<?= htmlspecialchars($settings['doctor_name'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Doctor Qualifications</label>
                            <input type="text" id="doctor_qualifications" value="<?= htmlspecialchars($settings['doctor_qualifications'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Doctor SLMC Reg No</label>
                            <input type="text" id="doctor_slmc" value="<?= htmlspecialchars($settings['doctor_slmc'] ?? '') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Logo Width (Print, e.g. w-24 or 100px)</label>
                            <input type="text" id="logo_width" value="<?= htmlspecialchars($settings['logo_width'] ?? 'w-[28%]') ?>" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm">
                        </div>
                    </div>

                    <div class="pt-8 border-t border-white/10 mt-8">
                        <h3 class="text-lg font-bold text-white mb-6 tracking-tight drop-shadow-sm">Print Properties</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Page Size & Orientation</label>
                                <select id="print_page_size" class="w-full bg-black/20 border border-white/10 text-white rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm [&>option]:bg-slate-800">
                                    <option value="A5" <?= ($settings['print_page_size'] ?? 'A5') == 'A5' ? 'selected' : '' ?>>A5 Portrait (Strict Layout)</option>
                                    <option value="A4" <?= ($settings['print_page_size'] ?? 'A5') == 'A4' ? 'selected' : '' ?>>A4 Portrait</option>
                                    <option value="Letter" <?= ($settings['print_page_size'] ?? 'A5') == 'Letter' ? 'selected' : '' ?>>Letter Portrait</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Text Size</label>
                                <select id="print_text_size" class="w-full bg-black/20 border border-white/10 text-white rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm [&>option]:bg-slate-800">
                                    <option value="12px" <?= ($settings['print_text_size'] ?? '14px') == '12px' ? 'selected' : '' ?>>Small (12px)</option>
                                    <option value="14px" <?= ($settings['print_text_size'] ?? '14px') == '14px' ? 'selected' : '' ?>>Normal (14px)</option>
                                    <option value="16px" <?= ($settings['print_text_size'] ?? '14px') == '16px' ? 'selected' : '' ?>>Large (16px)</option>
                                    <option value="18px" <?= ($settings['print_text_size'] ?? '14px') == '18px' ? 'selected' : '' ?>>Extra Large (18px)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-8">
                        <button type="submit" class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 border border-indigo-500/30 hover:border-indigo-500/50 p-4 rounded-xl font-bold transition-all shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] text-lg backdrop-blur-md">Save Settings</button>
                        <div id="saveMsg" class="text-center font-bold mt-4 hidden drop-shadow-md"></div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                clinic_name: document.getElementById('clinic_name').value,
                clinic_email: document.getElementById('clinic_email').value,
                doctor_name: document.getElementById('doctor_name').value,
                doctor_qualifications: document.getElementById('doctor_qualifications').value,
                doctor_slmc: document.getElementById('doctor_slmc').value,
                clinic_address: document.getElementById('clinic_address').value,
                clinic_phone: document.getElementById('clinic_phone').value,
                print_page_size: document.getElementById('print_page_size').value,
                print_text_size: document.getElementById('print_text_size').value,
                logo_width: document.getElementById('logo_width').value
            };
            
            const res = await fetch('../api/update_settings.php', {
                method: 'POST',
                body: JSON.stringify(payload),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            
            const msg = document.getElementById('saveMsg');
            msg.classList.remove('hidden');
            if(result.success) {
                msg.className = 'text-center font-bold mt-4 text-emerald-400 drop-shadow-sm tracking-wide';
                msg.innerText = 'Settings saved successfully!';
                setTimeout(()=>msg.classList.add('hidden'), 3000);
            } else {
                msg.className = 'text-center font-bold mt-4 text-rose-400 drop-shadow-sm tracking-wide';
                msg.innerText = result.error;
            }
        });
    </script>
</body>
</html>
