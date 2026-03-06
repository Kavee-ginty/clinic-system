<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$patientId = $_GET['id'] ?? null;

if (!$patientId) {
    echo json_encode(['error' => 'ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM Patients WHERE PatientID = ?");
    $stmt->execute([$patientId]);
    $patient = $stmt->fetch();
    echo json_encode($patient);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
