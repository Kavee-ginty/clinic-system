<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // Fetch unique diagnoses, ordered by frequency
    $stmt = $pdo->query("
        SELECT Diagnosis, COUNT(*) as count 
        FROM Visits 
        WHERE Diagnosis IS NOT NULL AND TRIM(Diagnosis) != '' 
        GROUP BY Diagnosis 
        ORDER BY count DESC 
        LIMIT 50
    ");
    
    $diagnoses = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $diagnoses[] = trim($row['Diagnosis']);
    }
    
    echo json_encode(['success' => true, 'diagnoses' => $diagnoses]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
