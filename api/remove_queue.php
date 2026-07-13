<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$queueId = $data['queue_id'] ?? null;

if (!$queueId) {
    echo json_encode(['success' => false, 'error' => 'Queue ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE q FROM Queue q LEFT JOIN Visits v ON v.QueueID = q.QueueID WHERE q.QueueID = ? AND v.VisitID IS NULL");
    $stmt->execute([$queueId]);
    echo json_encode($stmt->rowCount() ? ['success' => true] : ['success' => false, 'error' => 'Cannot remove after a visit is created']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
