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
<body class="bg-gray-50 flex h-screen overflow-hidden dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_doctor.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-teal-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Customize Print Settings</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-teal-600 hover:bg-teal-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1 bg-teal-700 hover:bg-teal-800 rounded font-bold text-sm transition">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-teal-500">
            <h2 class="text-xl font-bold mb-6 text-gray-800">Edit Clinic Data (Shows on printed reports)</h2>
            <form id="settingsForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Clinic Name</label>
                        <input type="text" id="clinic_name" value="<?= htmlspecialchars($settings['clinic_name'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Clinic Email</label>
                        <input type="text" id="clinic_email" value="<?= htmlspecialchars($settings['clinic_email'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Clinic Address</label>
                        <input type="text" id="clinic_address" value="<?= htmlspecialchars($settings['clinic_address'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Phone Number(s)</label>
                        <input type="text" id="clinic_phone" value="<?= htmlspecialchars($settings['clinic_phone'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Doctor's Name</label>
                        <input type="text" id="doctor_name" value="<?= htmlspecialchars($settings['doctor_name'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Doctor Qualifications</label>
                        <input type="text" id="doctor_qualifications" value="<?= htmlspecialchars($settings['doctor_qualifications'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Doctor SLMC Reg No</label>
                        <input type="text" id="doctor_slmc" value="<?= htmlspecialchars($settings['doctor_slmc'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Logo Width (Print, e.g. w-24 or 100px)</label>
                        <input type="text" id="logo_width" value="<?= htmlspecialchars($settings['logo_width'] ?? 'w-[28%]') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="pt-4 border-t mt-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Print Properties</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Page Size & Orientation</label>
                            <select id="print_page_size" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                                <option value="A5" <?= ($settings['print_page_size'] ?? 'A5') == 'A5' ? 'selected' : '' ?>>A5 Portrait (Strict Layout)</option>
                                <option value="A4" <?= ($settings['print_page_size'] ?? 'A5') == 'A4' ? 'selected' : '' ?>>A4 Portrait</option>
                                <option value="Letter" <?= ($settings['print_page_size'] ?? 'A5') == 'Letter' ? 'selected' : '' ?>>Letter Portrait</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Text Size</label>
                            <select id="print_text_size" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                                <option value="12px" <?= ($settings['print_text_size'] ?? '14px') == '12px' ? 'selected' : '' ?>>Small (12px)</option>
                                <option value="14px" <?= ($settings['print_text_size'] ?? '14px') == '14px' ? 'selected' : '' ?>>Normal (14px)</option>
                                <option value="16px" <?= ($settings['print_text_size'] ?? '14px') == '16px' ? 'selected' : '' ?>>Large (16px)</option>
                                <option value="18px" <?= ($settings['print_text_size'] ?? '14px') == '18px' ? 'selected' : '' ?>>Extra Large (18px)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white p-3 rounded font-bold transition shadow text-lg">Save Settings</button>
                    <div id="saveMsg" class="text-center font-bold mt-3 hidden"></div>
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
                msg.className = 'text-center font-bold mt-3 text-green-600';
                msg.innerText = 'Settings saved successfully!';
                setTimeout(()=>msg.classList.add('hidden'), 3000);
            } else {
                msg.className = 'text-center font-bold mt-3 text-red-600';
                msg.innerText = result.error;
            }
        });
    </script>
</body>
</html>
