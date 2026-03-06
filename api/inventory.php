<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Drugs ORDER BY DrugName ASC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO Drugs (DrugName, BatchNumber, Quantity, UnitPrice) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['drug_name'], $data['batch_number'], $data['quantity'], $data['unit_price']]);
            echo json_encode(['success' => true]);
        } elseif ($action === 'update_stock') {
            $stmt = $pdo->prepare("UPDATE Drugs SET Quantity = Quantity + ? WHERE DrugID = ?");
            $stmt->execute([$data['add_quantity'], $data['drug_id']]);
            echo json_encode(['success' => true]);
        } elseif ($action === 'update_price') {
            $stmt = $pdo->prepare("UPDATE Drugs SET UnitPrice = ? WHERE DrugID = ?");
            $stmt->execute([$data['price'], $data['drug_id']]);
            echo json_encode(['success' => true]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM Drugs WHERE DrugID = ?");
            $stmt->execute([$data['drug_id']]);
            echo json_encode(['success' => true]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
