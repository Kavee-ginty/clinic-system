<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$allowedFields = ['Complaint', 'Examination', 'Investigation', 'Diagnosis', 'Referals', 'Notes'];
$field = $_GET['field'] ?? '';

if (!in_array($field, $allowedFields)) {
    echo json_encode(['success' => false, 'error' => 'Invalid field']);
    exit;
}

try {
    // Fetch unique values for the requested field, ordered by frequency
    $stmt = $pdo->query("
        SELECT `$field` as value, COUNT(*) as count 
        FROM Visits 
        WHERE `$field` IS NOT NULL AND TRIM(`$field`) != '' 
        GROUP BY `$field` 
        ORDER BY count DESC 
        LIMIT 50
    ");
    
    $items = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items[] = trim($row['value']);
    }
    
    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
