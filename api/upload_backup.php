<?php
session_start();

// Allow access if logged in as admin/doctor, OR if running from CLI, OR if called from localhost
$isLocal = (php_sapi_name() === 'cli' || ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' || ($_SERVER['REMOTE_ADDR'] ?? '') === '::1');
$isLoggedIn = isset($_SESSION['admin_logged_in']) || isset($_SESSION['doctor_logged_in']);

if (!$isLoggedIn && !$isLocal) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once '../config/db.php';

// Get Google Apps Script URL
$stmt = $pdo->prepare("SELECT SettingValue FROM Settings WHERE SettingKey = 'backup_drive_url'");
$stmt->execute();
$webAppUrl = $stmt->fetchColumn();

if (!$webAppUrl || trim($webAppUrl) === '') {
    $webAppUrl = 'https://script.google.com/macros/s/AKfycbxQI9gJvcfUrdmmql2JkEk-XJbBJzx1lMO4slAcK44A6oelnB8p6WRQTfkFzzeJo3Q/exec';
}

// Generate the SQL backup content
$tables = ['Settings', 'Patients', 'Queue', 'Visits', 'Drugs', 'VisitDrugs'];
$sqlBuffer = "-- Clinic System Database Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
$sqlBuffer .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
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
        // Skip table
    }
}
$sqlBuffer .= "SET FOREIGN_KEY_CHECKS=1;\n";

$filename = 'clinic_backup_' . date('Y_m_d_H_i_s') . '.sql';

// Post to Google Apps Script using cURL
$payload = json_encode([
    'filename' => $filename,
    'content' => $sqlBuffer
]);

$ch = curl_init($webAppUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Apps Script redirects
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local XAMPP compatibility

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(['success' => false, 'error' => 'cURL Error: ' . $curlError]);
    exit;
}

$resData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON response from Google Script: ' . $response]);
    exit;
}

echo json_encode($resData);
?>
