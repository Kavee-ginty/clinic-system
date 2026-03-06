<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$patientId = $data['patient_id'] ?? null;

if (!$patientId) {
    echo json_encode(['success' => false, 'error' => 'Patient ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Delete associated VisitDrugs (using VisitIDs linked to Patient)
    $stmt = $pdo->prepare("DELETE vd FROM VisitDrugs vd INNER JOIN Visits v ON vd.VisitID = v.VisitID WHERE v.PatientID = ?");
    $stmt->execute([$patientId]);

    // 2. Delete associated Visits
    $stmt = $pdo->prepare("DELETE FROM Visits WHERE PatientID = ?");
    $stmt->execute([$patientId]);

    // 3. Delete associated Queue records
    $stmt = $pdo->prepare("DELETE FROM Queue WHERE PatientID = ?");
    $stmt->execute([$patientId]);

    // 4. Delete the Patient
    $stmt = $pdo->prepare("DELETE FROM Patients WHERE PatientID = ?");
    $stmt->execute([$patientId]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
