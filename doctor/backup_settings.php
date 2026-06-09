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

$backupDir = __DIR__ . '/../backups';
$localBackups = [];
if (file_exists($backupDir)) {
    $files = glob($backupDir . '/*.sql');
    if ($files) {
        array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
        foreach ($files as $f) {
            $localBackups[] = [
                'name' => basename($f),
                'size' => round(filesize($f) / 1024, 2) . ' KB',
                'date' => date('Y-m-d H:i:s', filemtime($f))
            ];
        }
    }
}
?>
<?php
$pageTitle = 'Backup Settings - Doctor';
include '../includes/header.php';
?>
<body class="bg-gray-50 flex h-screen overflow-hidden dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_doctor.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-teal-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Database Backup Settings</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-teal-600 hover:bg-teal-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1 bg-teal-700 hover:bg-teal-800 rounded font-bold text-sm transition">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg border-t-4 border-teal-500">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Database Backup & Recovery</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Create copies of your system database to prevent data loss. You can automate this locally or upload files manually.</p>
                
                <form id="backupConfigForm" class="space-y-4 mb-8">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Google Drive Apps Script Web App URL</label>
                        <input type="url" id="backup_drive_url" value="<?= htmlspecialchars($settings['backup_drive_url'] ?? 'https://script.google.com/macros/s/AKfycbxQI9gJvcfUrdmmql2JkEk-XJbBJzx1lMO4slAcK44A6oelnB8p6WRQTfkFzzeJo3Q/exec') ?>" class="w-full border rounded p-3 focus:ring-2 focus:ring-teal-500 font-mono text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="https://script.google.com/macros/s/.../exec">
                    </div>
                    <div>
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded font-bold transition text-sm">Save Backup Config</button>
                        <span id="backupSaveMsg" class="font-bold ml-3 text-sm hidden"></span>
                    </div>
                </form>

                <div class="border-t dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Manual Operations</h3>
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="../admin/backup.php" target="_blank" class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-6 py-3 rounded shadow transition flex items-center gap-2 text-sm">
                            <span>📥</span> Download Backup (SQL)
                        </a>
                        <button id="btnSaveBackup" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded shadow transition flex items-center gap-2 text-sm">
                            <span>💾</span> Save Backup on Server
                        </button>
                        <button id="btnUploadDrive" class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-6 py-3 rounded shadow transition flex items-center gap-2 text-sm">
                            <span>☁️</span> Upload to Google Drive
                        </button>
                    </div>
                </div>

                <div class="border-t dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Backups Stored on Server</h3>
                    <?php if (empty($localBackups)): ?>
                        <p class="text-gray-500 italic dark:text-gray-400">No local backups saved on server yet.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold border-b text-sm">
                                        <th class="p-3">Backup File</th>
                                        <th class="p-3">Created Date</th>
                                        <th class="p-3">Size</th>
                                        <th class="p-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($localBackups as $backup): ?>
                                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 text-sm">
                                            <td class="p-3 font-mono text-gray-800 dark:text-gray-250"><?= htmlspecialchars($backup['name']) ?></td>
                                            <td class="p-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($backup['date']) ?></td>
                                            <td class="p-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($backup['size']) ?></td>
                                            <td class="p-3 text-right">
                                                <a href="../admin/backup.php?download_file=<?= urlencode($backup['name']) ?>" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 font-bold">Download</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t dark:border-gray-700 pt-6 mt-6 bg-yellow-50 dark:bg-yellow-950/20 p-4 rounded-lg border border-yellow-250 dark:border-yellow-900">
                    <h3 class="text-md font-bold text-yellow-800 dark:text-yellow-450 mb-2 flex items-center gap-2">
                        <span>💡</span> Periodic Auto-Backup setup (Windows / XAMPP)
                    </h3>
                    <p class="text-sm text-yellow-900 dark:text-yellow-350 leading-relaxed mb-3">
                        To schedule automated daily backups:
                    </p>
                    <ol class="list-decimal list-inside text-sm text-yellow-900 dark:text-yellow-350 space-y-1">
                        <li>Open <strong>Windows Task Scheduler</strong>.</li>
                        <li>Create a Basic Task named <code>Clinic Backup</code>.</li>
                        <li>Set trigger to <strong>Daily</strong>.</li>
                        <li>Set action to <strong>Start a Program</strong>, and choose your PHP executable: <code>C:\xampp\php\php.exe</code></li>
                        <li>Add arguments: <code>-f "d:\XAMPP\htdocs\clinic-system\admin\backup.php"</code></li>
                    </ol>
                    <p class="text-sm text-yellow-900 dark:text-yellow-350 mt-3 font-semibold">
                        ☁️ Google Drive Cloud Backup Automation:
                    </p>
                    <p class="text-sm text-yellow-900 dark:text-yellow-350 mt-1">
                        To schedule automated uploads directly to your Google Drive instead, change the Task scheduler program argument in step 5 to:
                        <code class="block bg-yellow-100 dark:bg-yellow-900/40 p-2 rounded mt-1 font-mono text-xs dark:text-yellow-250">-f "d:\XAMPP\htdocs\clinic-system\api\upload_backup.php"</code>
                    </p>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('btnSaveBackup')?.addEventListener('click', async () => {
            const btn = document.getElementById('btnSaveBackup');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Saving...';
            try {
                const res = await fetch('../admin/backup.php?action=save_local');
                const result = await res.json();
                if (result.success) {
                    alert('Backup saved successfully: ' + result.filename);
                    window.location.reload();
                } else {
                    alert('Failed to save backup.');
                }
            } catch (e) {
                alert('Error: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        });

        document.getElementById('btnUploadDrive')?.addEventListener('click', async () => {
            const btn = document.getElementById('btnUploadDrive');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Uploading to Drive...';
            try {
                const res = await fetch('../api/upload_backup.php');
                const result = await res.json();
                if (result.success) {
                    alert('Successfully backed up and uploaded to Google Drive!\nFile URL: ' + result.url);
                } else {
                    alert('Failed to upload backup: ' + (result.error || 'Unknown error'));
                }
            } catch (e) {
                alert('Error: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        });

        // Backup Config Form Submission
        document.getElementById('backupConfigForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                backup_drive_url: document.getElementById('backup_drive_url').value
            };
            const res = await fetch('../api/update_settings.php', {
                method: 'POST',
                body: JSON.stringify(payload),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            const msg = document.getElementById('backupSaveMsg');
            msg.classList.remove('hidden');
            if (result.success) {
                msg.className = 'font-bold ml-3 text-sm text-green-600';
                msg.innerText = 'Backup configuration saved!';
                setTimeout(() => msg.classList.add('hidden'), 3000);
            } else {
                msg.className = 'font-bold ml-3 text-sm text-red-600';
                msg.innerText = result.error;
            }
        });
    </script>
</body>
</html>
