<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $patientId = $data['patient_id'] ?? null;

    if (!$patientId) {
        echo json_encode(['success' => false, 'error' => 'Patient ID required']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Get max queue number for today
        $maxStmt = $pdo->query("SELECT MAX(QueueNumber) as max_num FROM Queue WHERE QueueDate = CURRENT_DATE");
        $maxResult = $maxStmt->fetch();
        $nextNum = ($maxResult['max_num'] ?? 0) + 1;

        // Insert into Queue
        $insertStmt = $pdo->prepare("INSERT INTO Queue (PatientID, QueueNumber, QueueDate, Status) VALUES (?, ?, CURRENT_DATE, 'waiting')");
        $insertStmt->execute([$patientId, $nextNum]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'queue_number' => $nextNum]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
