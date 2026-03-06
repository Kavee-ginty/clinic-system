<?php
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $fee = $data['visit_fee'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO Settings (SettingKey, SettingValue) VALUES ('visit_fee', ?) ON DUPLICATE KEY UPDATE SettingValue = ?");
        $stmt->execute([$fee, $fee]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
