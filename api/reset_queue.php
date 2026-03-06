<?php
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM Queue WHERE QueueDate = CURRENT_DATE AND Status = 'waiting'");
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
