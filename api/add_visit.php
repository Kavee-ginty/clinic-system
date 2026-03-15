<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patientId = $data['patient_id'] ?? null;
    $queueId = $data['queue_id'] ?? null;
    
    if (!$patientId || !$queueId) {
        echo json_encode(['success' => false, 'error' => 'Patient ID and Queue ID required']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Visits (PatientID, QueueID, Complaint, Examination, Investigation, Diagnosis, Treatment, Referals, Notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $patientId, 
            $queueId,
            $data['complaint'] ?? '',
            $data['examination'] ?? '',
            $data['investigation'] ?? '',
            $data['diagnosis'] ?? '',
            $data['treatment'] ?? '',
            $data['referals'] ?? '',
            $data['notes'] ?? ''
        ]);

        $visitId = $pdo->lastInsertId();

        // 2) Process Drugs Array
        $drugsPayload = $data['drugs'] ?? [];
        $drugCostSubtotal = 0;

        // Fetch settings for default fee
        $stmtSettings = $pdo->query("SELECT SettingValue FROM Settings WHERE SettingKey = 'visit_fee'");
        $defaultVisitFee = (float)$stmtSettings->fetchColumn() ?: 500;

        if (!empty($drugsPayload)) {
            $vdStmt = $pdo->prepare("INSERT INTO VisitDrugs (VisitID, DrugID, DrugName, Quantity, TotalCost, Frequency, Dose, Duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $invUpdateStmt = $pdo->prepare("UPDATE Drugs SET Quantity = Quantity - ? WHERE DrugID = ?");
            
            foreach ($drugsPayload as $d) {
                $qty = (int)$d['qty'];
                $cost = (float)$d['cost'];
                $drugCostSubtotal += $cost;
                $drugId = !empty($d['id']) ? $d['id'] : null;

                $vdStmt->execute([
                    $visitId,
                    $drugId,
                    $d['name'],
                    $qty,
                    $cost,
                    $d['frequency'] ?? '',
                    $d['dose'] ?? '',
                    $d['duration'] ?? ''
                ]);

                if ($drugId) {
                    $invUpdateStmt->execute([$qty, $drugId]);
                }
            }
        }

        $totalBill = $defaultVisitFee + $drugCostSubtotal;
        $updVisitStmt = $pdo->prepare("UPDATE Visits SET VisitFee = ?, TotalBill = ? WHERE VisitID = ?");
        $updVisitStmt->execute([$defaultVisitFee, $totalBill, $visitId]);

        // 3) Mark queue as completed
        $updStmt = $pdo->prepare("UPDATE Queue SET Status = 'completed' WHERE QueueID = ?");
        $updStmt->execute([$queueId]);

        $pdo->commit();
        echo json_encode(['success' => true, 'visit_id' => $visitId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
