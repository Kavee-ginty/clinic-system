<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$stats = [
    'total_patients' => $pdo->query("SELECT COUNT(*) FROM Patients")->fetchColumn(),
    'patients_today' => $pdo->query("SELECT COUNT(*) FROM Patients WHERE RegisteredDate = CURRENT_DATE")->fetchColumn(),
    'visits_today' => $pdo->query("SELECT COUNT(*) FROM Visits v JOIN Queue q ON v.QueueID = q.QueueID WHERE DATE(v.VisitDateTime) = CURRENT_DATE AND q.Status = 'completed'")->fetchColumn(),
    'waiting' => $pdo->query("SELECT COUNT(*) FROM Queue WHERE QueueDate = CURRENT_DATE AND Status='waiting'")->fetchColumn()
];

echo json_encode($stats);
?>
