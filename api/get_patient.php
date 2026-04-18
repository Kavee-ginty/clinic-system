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
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($patient && !empty($patient['DOB'])) {
        try {
            $dobDate = new DateTime($patient['DOB']);
            $today = new DateTime('today');
            $patient['Age'] = $dobDate->diff($today)->y;
        } catch(Exception $e){}
    }
    
    echo json_encode($patient);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
