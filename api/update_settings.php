<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE Settings SET SettingValue = ? WHERE SettingKey = ?");
        
        foreach(['clinic_name', 'doctor_name', 'clinic_address', 'clinic_phone', 'print_page_size', 'print_text_size'] as $key) {
            if(isset($data[$key])) {
                $stmt->execute([$data[$key], $key]);
            }
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
