<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patientId = $data['patient_id'] ?? null;
    $queueId = $data['queue_id'] ?? null;
    
    if (!$patientId || !$queueId) {
        echo json_encode(['success' => false, 'error' => 'Patient ID and Queue ID required']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Visits (PatientID, QueueID, Complaint, Examination, Investigation, Diagnosis, Treatment, Referals, Notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $patientId, 
            $queueId,
            $data['complaint'] ?? '',
            $data['examination'] ?? '',
            $data['investigation'] ?? '',
            $data['diagnosis'] ?? '',
            $data['treatment'] ?? '',
            $data['referals'] ?? '',
            $data['notes'] ?? ''
        ]);

        $visitId = $pdo->lastInsertId();

        // Mark queue as completed
        $updStmt = $pdo->prepare("UPDATE Queue SET Status = 'completed' WHERE QueueID = ?");
        $updStmt->execute([$queueId]);

        $pdo->commit();
        echo json_encode(['success' => true, 'visit_id' => $visitId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
