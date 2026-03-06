<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $patientId = $_GET['patient_id'] ?? null;

    if (!$patientId) {
        echo json_encode(['error' => 'Patient ID required']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT * FROM Visits 
        WHERE PatientID = ? 
        ORDER BY VisitDateTime DESC
    ");
    $stmt->execute([$patientId]);
    $history = $stmt->fetchAll();
    
    echo json_encode($history);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
