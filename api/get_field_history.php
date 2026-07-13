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
    // Fetch values and split common combined complaints into single suggestions.
    $stmt = $pdo->query("
        SELECT `$field` as value
        FROM Visits 
        WHERE `$field` IS NOT NULL AND TRIM(`$field`) != '' 
        ORDER BY VisitDateTime DESC 
        LIMIT 300
    ");
    
    $counts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        foreach (preg_split('/\R|,|\band\b/i', trim($row['value'])) as $item) {
            $item = trim($item, " \t\n\r\0\x0B-*");
            if ($item !== '') {
                $key = strtolower($item);
                $counts[$key]['value'] = $item;
                $counts[$key]['count'] = ($counts[$key]['count'] ?? 0) + 1;
            }
        }
    }
    usort($counts, fn($a, $b) => $b['count'] <=> $a['count']);
    $items = array_slice(array_column($counts, 'value'), 0, 50);
    
    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
