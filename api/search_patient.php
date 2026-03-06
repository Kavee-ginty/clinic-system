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
    $stmt = $pdo->prepare("SELECT * FROM Patients WHERE FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR NIC LIKE ? OR PatientNumber LIKE ? ORDER BY FirstName ASC LIMIT 50");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
