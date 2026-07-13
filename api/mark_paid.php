<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$visitId = $data['visit_id'] ?? null;

if (!$visitId) {
    echo json_encode(['success' => false, 'error' => 'Visit ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE Visits SET IsPaid = 1 WHERE VisitID = ?");
    $stmt->execute([$visitId]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
