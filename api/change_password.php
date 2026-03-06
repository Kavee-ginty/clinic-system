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
    $new_password = $data['password'] ?? '';
    
    if(strlen($new_password) < 3) {
        echo json_encode(['success' => false, 'error' => 'Password too short']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE Settings SET SettingValue = ? WHERE SettingKey = 'admin_password'");
        $stmt->execute([$new_password]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
