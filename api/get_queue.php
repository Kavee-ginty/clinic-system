<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT q.QueueID, q.QueueNumber, q.Status, q.PatientID, 
               p.FirstName, p.LastName, p.Gender, p.DOB 
        FROM Queue q
        JOIN Patients p ON q.PatientID = p.PatientID
        WHERE q.QueueDate = CURRENT_DATE
        ORDER BY q.QueueNumber ASC
    ");
    $queue = $stmt->fetchAll();
    echo json_encode($queue);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
