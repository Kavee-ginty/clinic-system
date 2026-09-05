<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT q.QueueID, q.QueueNumber, q.Status, q.PatientID, 
               p.FirstName, p.LastName, p.Gender, p.DOB, p.PatientNumber,
               (SELECT COUNT(*) FROM Visits v WHERE v.PatientID = q.PatientID AND DATE(v.VisitDateTime) < CURRENT_DATE) as PreviousVisits,
               v3.VisitID, v3.TotalBill, v3.VisitFee, COALESCE(v3.IsPaid, 0) AS IsPaid
        FROM Queue q
        JOIN Patients p ON q.PatientID = p.PatientID
        LEFT JOIN (
            SELECT QueueID, MAX(VisitID) AS VisitID
            FROM Visits
            WHERE QueueID IS NOT NULL
            GROUP BY QueueID
        ) lv ON q.QueueID = lv.QueueID
        LEFT JOIN Visits v3 ON lv.VisitID = v3.VisitID
        WHERE q.QueueDate = CURRENT_DATE
        ORDER BY CASE q.Status WHEN 'waiting' THEN 1 WHEN 'with_doctor' THEN 2 ELSE 3 END,
                 COALESCE(v3.IsPaid, 0) ASC,
                 q.QueueNumber ASC
    ");
    $queue = $stmt->fetchAll();
    echo json_encode($queue);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
