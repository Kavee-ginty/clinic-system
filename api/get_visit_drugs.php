<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$patientId = $_GET['patient_id'] ?? null;

if (!$patientId) {
    echo json_encode(['success' => false, 'error' => 'Patient ID is required']);
    exit;
}

try {
    // Get unique drug names for the patient, with their most recent dose/frequency/duration details
    $stmt = $pdo->prepare("
        SELECT vd.DrugName, vd.Dose, vd.Frequency, vd.Duration, vd.Quantity, vd.TotalCost, d.DrugID
        FROM VisitDrugs vd
        JOIN Visits v ON vd.VisitID = v.VisitID
        LEFT JOIN Drugs d ON vd.DrugName = d.DrugName
        WHERE v.PatientID = ? 
          AND vd.DrugName != '' 
          AND vd.VisitDrugID = (
              SELECT vd2.VisitDrugID 
              FROM VisitDrugs vd2 
              JOIN Visits v2 ON vd2.VisitID = v2.VisitID
              WHERE v2.PatientID = v.PatientID AND vd2.DrugName = vd.DrugName
              ORDER BY v2.VisitDateTime DESC, vd2.VisitDrugID DESC 
              LIMIT 1
          )
        ORDER BY vd.DrugName ASC
    ");
    $stmt->execute([$patientId]);
    $drugs = $stmt->fetchAll();

    echo json_encode(['success' => true, 'drugs' => $drugs]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
