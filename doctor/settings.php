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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Settings - Doctor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Customize Print Header</h1>
        <div>
            <a href="../index.php" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 rounded font-semibold mr-2 transition">Dashboard</a>
            <a href="dashboard.php" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold transition">Back</a>
        </div>
    </nav>

    <div class="container mx-auto p-4 mt-10 max-w-2xl">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-teal-500">
            <h2 class="text-xl font-bold mb-6 text-gray-800">Edit Clinic Data (Shows on printed reports)</h2>
            <form id="settingsForm" class="space-y-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Clinic Name</label>
                    <input type="text" id="clinic_name" value="<?= htmlspecialchars($settings['clinic_name'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Doctor's Name / Credentials</label>
                    <input type="text" id="doctor_name" value="<?= htmlspecialchars($settings['doctor_name'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Address</label>
                    <input type="text" id="clinic_address" value="<?= htmlspecialchars($settings['clinic_address'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="clinic_phone" value="<?= htmlspecialchars($settings['clinic_phone'] ?? '') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500" required>
                </div>

                <div class="pt-4 border-t mt-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Print Properties</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Page Size</label>
                            <select id="print_page_size" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500">
                                <option value="A4" <?= ($settings['print_page_size'] ?? 'A4') == 'A4' ? 'selected' : '' ?>>A4 (Standard)</option>
                                <option value="A5" <?= ($settings['print_page_size'] ?? 'A4') == 'A5' ? 'selected' : '' ?>>A5 (Half Size)</option>
                                <option value="Letter" <?= ($settings['print_page_size'] ?? 'A4') == 'Letter' ? 'selected' : '' ?>>Letter</option>
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

    <script>
        document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                clinic_name: document.getElementById('clinic_name').value,
                doctor_name: document.getElementById('doctor_name').value,
                clinic_address: document.getElementById('clinic_address').value,
                clinic_phone: document.getElementById('clinic_phone').value,
                print_page_size: document.getElementById('print_page_size').value,
                print_text_size: document.getElementById('print_text_size').value
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
