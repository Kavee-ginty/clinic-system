<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $pdo->beginTransaction();
        $stmtUpdate = $pdo->prepare("UPDATE Settings SET SettingValue = ? WHERE SettingKey = ?");
        $stmtInsert = $pdo->prepare("INSERT INTO Settings (SettingKey, SettingValue) VALUES (?, ?)");
        
        foreach(['clinic_name', 'doctor_name', 'doctor_qualifications', 'doctor_slmc', 'clinic_address', 'clinic_phone', 'clinic_email', 'print_page_size', 'print_text_size', 'logo_width'] as $key) {
            if(isset($data[$key])) {
                $stmtUpdate->execute([$data[$key], $key]);
                if ($stmtUpdate->rowCount() === 0) {
                    $check = $pdo->prepare("SELECT 1 FROM Settings WHERE SettingKey = ?");
                    $check->execute([$key]);
                    if (!$check->fetch()) {
                        $stmtInsert->execute([$key, $data[$key]]);
                    }
                }
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
