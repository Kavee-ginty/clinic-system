<?php
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patient_id = $data['patient_id'] ?? null;
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $dob = trim($data['dob'] ?? '');
    $gender = trim($data['gender'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');

    if (!$patient_id || empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'error' => 'Missing required data']);
        exit;
    }

    try {
        $age = 0;
        if (!empty($dob)) {
            $dobDate = new DateTime($dob);
            $today = new DateTime('today');
            $age = $dobDate->diff($today)->y;
        }

        $stmt = $pdo->prepare("UPDATE Patients SET FirstName=?, LastName=?, DOB=?, Gender=?, Phone=?, Address=?, Age=? WHERE PatientID=?");
        $stmt->execute([$firstName, $lastName, $dob, $gender, $phone, $address, $age, $patient_id]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
