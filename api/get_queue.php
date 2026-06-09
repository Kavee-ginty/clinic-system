<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT q.QueueID, q.QueueNumber, q.Status, q.PatientID, 
               p.FirstName, p.LastName, p.Gender, p.DOB,
               (SELECT COUNT(*) FROM Visits v WHERE v.PatientID = q.PatientID AND DATE(v.VisitDateTime) < CURRENT_DATE) as PreviousVisits,
               v3.VisitID, v3.TotalBill, v3.VisitFee
        FROM Queue q
        JOIN Patients p ON q.PatientID = p.PatientID
        LEFT JOIN Visits v3 ON q.QueueID = v3.QueueID
        WHERE q.QueueDate = CURRENT_DATE
        ORDER BY q.QueueNumber ASC
    ");
    $queue = $stmt->fetchAll();
    echo json_encode($queue);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
