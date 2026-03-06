<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $visitId = $data['visit_id'] ?? null;
    $visitFee = $data['visit_fee'] ?? 0;
    $totalBill = $data['total_bill'] ?? 0;
    $drugs = $data['drugs'] ?? [];

    if (!$visitId) {
        echo json_encode(['success' => false, 'error' => 'Visit ID missing']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Update visit with total bill and fee
        $stmt = $pdo->prepare("UPDATE Visits SET VisitFee = ?, TotalBill = ? WHERE VisitID = ?");
        $stmt->execute([$visitFee, $totalBill, $visitId]);

        // Insert drugs, deduct stock and save dosage instructions
        $drugStmt = $pdo->prepare("INSERT INTO VisitDrugs (VisitID, DrugID, Quantity, TotalCost, Frequency, Dose, Duration) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stockStmt = $pdo->prepare("UPDATE Drugs SET Quantity = Quantity - ? WHERE DrugID = ?");

        foreach ($drugs as $drug) {
            $freq = $drug['frequency'] ?? '';
            $dose = $drug['dose'] ?? '';
            $dur = $drug['duration'] ?? '';
            $drugStmt->execute([$visitId, $drug['id'], $drug['qty'], $drug['cost'], $freq, $dose, $dur]);
            $stockStmt->execute([$drug['qty'], $drug['id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
