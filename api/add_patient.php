<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $dob = trim($data['dob'] ?? '');
    $gender = trim($data['gender'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $nic = trim($data['nic'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($dob) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Required fields missing']);
        exit;
    }

    try {
        // Calculate age
        $dobDate = new DateTime($dob);
        $today = new DateTime('today');
        $age = $dobDate->diff($today)->y;

        $stmt = $pdo->prepare("INSERT INTO Patients (FirstName, LastName, DOB, Gender, Phone, Address, RegisteredDate, NIC, Age) VALUES (?, ?, ?, ?, ?, ?, CURRENT_DATE, ?, ?)");
        $stmt->execute([$firstName, $lastName, $dob, $gender, $phone, $address, $nic, $age]);
        $patientId = $pdo->lastInsertId();
        
        // Generate Patient Number PT-0000X
        $patientNumber = 'PT-' . str_pad($patientId, 5, '0', STR_PAD_LEFT);
        $updateStmt = $pdo->prepare("UPDATE Patients SET PatientNumber = ? WHERE PatientID = ?");
        $updateStmt->execute([$patientNumber, $patientId]);

        echo json_encode(['success' => true, 'patient_id' => $patientId, 'patient_number' => $patientNumber]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
