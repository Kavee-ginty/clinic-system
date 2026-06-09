<?php
session_start();

// Allow access if logged in as admin/doctor, OR if running from CLI, OR if called from localhost
$isLocal = (php_sapi_name() === 'cli' || ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' || ($_SERVER['REMOTE_ADDR'] ?? '') === '::1');
$isLoggedIn = isset($_SESSION['admin_logged_in']) || isset($_SESSION['doctor_logged_in']);

if (!$isLoggedIn && !$isLocal) {
    header('Location: ../doctor/login.php');
    exit;
}

require_once '../config/db.php';

// Securely download a specific backup file
if (isset($_GET['download_file'])) {
    $filename = basename($_GET['download_file']);
    $filePath = __DIR__ . '/../backups/' . $filename;
    if (file_exists($filePath)) {
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        echo "Backup file not found.";
        exit;
    }
}

$tables = ['Settings', 'Patients', 'Queue', 'Visits', 'Drugs', 'VisitDrugs'];
$sqlBuffer = "-- Clinic System Database Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
$sqlBuffer .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    // Check if table exists first (in case database.sql wasn't fully run or tables modified)
    try {
        $stmt = $pdo->query("SELECT * FROM $table");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $keys = array_keys($row);
                $values = array_map(function($v) use ($pdo) {
                    return $v === null ? 'NULL' : $pdo->quote($v);
                }, array_values($row));
                $sqlBuffer .= "INSERT IGNORE INTO $table (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
            $sqlBuffer .= "\n";
        }
    } catch (Exception $e) {
        // Table might not exist, skip
    }
}
$sqlBuffer .= "SET FOREIGN_KEY_CHECKS=1;\n";

// Check if we want to save locally
$saveLocal = isset($_GET['action']) && $_GET['action'] === 'save_local';

if ($saveLocal || $isLocal) {
    $backupDir = __DIR__ . '/../backups';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true);
    }
    
    $filename = 'clinic_backup_' . date('Y_m_d_H_i_s') . '.sql';
    $filePath = $backupDir . '/' . $filename;
    file_put_contents($filePath, $sqlBuffer);
    
    // Clean old backups (keep last 30 backups)
    $files = glob($backupDir . '/*.sql');
    if (count($files) > 30) {
        array_multisort(array_map('filemtime', $files), SORT_ASC, $files);
        for ($i = 0; $i < count($files) - 30; $i++) {
            unlink($files[$i]);
        }
    }

    if ($saveLocal) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'filename' => $filename]);
        exit;
    } else {
        echo "Backup successfully created: " . $filename . "\n";
        exit;
    }
}

// Otherwise, force download
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="clinic_backup_' . date('Y_m_d_H_i') . '.sql"');
echo $sqlBuffer;
exit;
?>
