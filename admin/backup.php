<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

$tables = ['Settings', 'Patients', 'Queue', 'Visits'];
$sqlBuffer = "-- Clinic System Database Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
$sqlBuffer .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT * FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($rows) > 0) {
        foreach ($rows as $row) {
            $keys = array_keys($row);
            $values = array_map(function($v) use ($pdo) {
                return $v === null ? 'NULL' : $pdo->quote($v);
            }, array_values($row));
            $sqlBuffer .= "INSERT IGNORE INTO $table (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n";
        }
        $sqlBuffer .= "\n";
    }
}
$sqlBuffer .= "SET FOREIGN_KEY_CHECKS=1;\n";

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="clinic_backup_' . date('Y_m_d_H_i') . '.sql"');
echo $sqlBuffer;
exit;
?>
