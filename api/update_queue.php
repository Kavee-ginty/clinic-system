<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $queueId = $data['queue_id'] ?? null;
    $status = $data['status'] ?? null;

    if (!$queueId || !in_array($status, ['waiting', 'with_doctor', 'completed'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE Queue SET Status = ? WHERE QueueID = ?");
        $stmt->execute([$status, $queueId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
