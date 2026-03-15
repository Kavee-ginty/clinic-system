<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$query = trim($_GET['q'] ?? '');

if (empty($query)) {
    echo json_encode([]);
    exit;
}

try {
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare("
        SELECT p.*,
               (SELECT COUNT(*) FROM Visits v WHERE v.PatientID = p.PatientID) as PreviousVisits
        FROM Patients p
        WHERE p.FirstName LIKE ? OR p.LastName LIKE ? OR p.Phone LIKE ? OR p.NIC LIKE ? OR p.PatientNumber LIKE ? 
        ORDER BY p.FirstName ASC LIMIT 50
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
