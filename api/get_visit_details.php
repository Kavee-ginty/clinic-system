<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$visitId = $_GET['visit_id'] ?? null;

if (!$visitId) {
    echo json_encode(['success' => false, 'error' => 'Visit ID is required']);
    exit;
}

try {
    // Fetch visit details
    $vStmt = $pdo->prepare("
        SELECT v.VisitID, v.VisitDateTime, v.VisitFee, v.TotalBill, COALESCE(v.IsPaid, 0) AS IsPaid,
               p.FirstName, p.LastName, p.PatientID
        FROM Visits v
        JOIN Patients p ON v.PatientID = p.PatientID
        WHERE v.VisitID = ?
    ");
    $vStmt->execute([$visitId]);
    $visit = $vStmt->fetch();

    if (!$visit) {
        echo json_encode(['success' => false, 'error' => 'Visit not found']);
        exit;
    }

    // Fetch prescribed drugs
    $dStmt = $pdo->prepare("
        SELECT COALESCE(vd.DrugName, d.DrugName) AS DrugName, 
               vd.Quantity, vd.TotalCost, vd.Frequency, vd.Dose, vd.Duration
        FROM VisitDrugs vd
        LEFT JOIN Drugs d ON vd.DrugID = d.DrugID
        WHERE vd.VisitID = ?
    ");
    $dStmt->execute([$visitId]);
    $drugs = $dStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'visit' => $visit,
        'drugs' => $drugs
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
