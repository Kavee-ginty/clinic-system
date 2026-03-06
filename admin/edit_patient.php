<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

$patientId = $_GET['id'] ?? null;
if(!$patientId) die("Invalid Patient ID");

$stmt = $pdo->prepare("SELECT * FROM Patients WHERE PatientID = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if(!$patient) die("Patient not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-gray-800 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Edit Patient Details</h1>
        <a href="dashboard.php" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded font-semibold">Back</a>
    </nav>

    <div class="container mx-auto p-4 max-w-2xl mt-6">
        <div class="bg-white p-8 rounded-xl shadow border-t-4 border-gray-800">
            <h2 class="text-xl font-bold mb-6">Patient ID: #<?= $patient['PatientID'] ?></h2>
            
            <form id="editForm" class="space-y-4">
                <input type="hidden" id="patient_id" value="<?= $patient['PatientID'] ?>">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" id="firstName" value="<?= htmlspecialchars($patient['FirstName']) ?>" class="w-full border rounded p-2 focus:ring-2 focus:ring-gray-400" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" id="lastName" value="<?= htmlspecialchars($patient['LastName']) ?>" class="w-full border rounded p-2 focus:ring-2 focus:ring-gray-400" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">DOB <span class="text-red-500">*</span></label>
                        <input type="date" id="dob" value="<?= htmlspecialchars($patient['DOB']) ?>" class="w-full border rounded p-2 focus:ring-2 focus:ring-gray-400" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select id="gender" class="w-full border rounded p-2 focus:ring-2 focus:ring-gray-400">
                            <option value="Male" <?= $patient['Gender']=='Male' ? 'selected':'' ?>>Male</option>
                            <option value="Female" <?= $patient['Gender']=='Female' ? 'selected':'' ?>>Female</option>
                            <option value="Other" <?= $patient['Gender']=='Other' ? 'selected':'' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" value="<?= htmlspecialchars($patient['Phone']) ?>" class="w-full border rounded p-2 focus:ring-2 focus:ring-gray-400" required>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Address</label>
                    <textarea id="address" class="w-full border rounded p-2 focus:ring-2 focus:ring-gray-400" rows="3"><?= htmlspecialchars($patient['Address']) ?></textarea>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded shadow transition">Save Changes</button>
                    <a href="dashboard.php" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded text-center transition">Cancel</a>
                </div>
                <div id="saveMsg" class="font-bold text-center mt-2 hidden"></div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('editForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                patient_id: document.getElementById('patient_id').value,
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value
            };

            const res = await fetch('../api/update_patient.php', {
                method: 'POST', body: JSON.stringify(data), headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            
            const msg = document.getElementById('saveMsg');
            msg.classList.remove('hidden');
            if (result.success) {
                msg.className = 'text-green-600 font-bold text-center mt-2';
                msg.innerText = 'Patient updated successfully!';
                setTimeout(()=> window.location.href='dashboard.php', 1000);
            } else {
                msg.className = 'text-red-600 font-bold text-center mt-2';
                msg.innerText = 'Error: ' + result.error;
            }
        });
    </script>
</body>
</html>
