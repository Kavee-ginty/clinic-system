<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$visitId = $_GET['visit_id'] ?? null;
if (!$visitId) die("Visit ID required.");

// Fetch Data
$stmt = $pdo->prepare("SELECT * FROM Visits v JOIN Patients p ON v.PatientID = p.PatientID WHERE v.VisitID = ?");
$stmt->execute([$visitId]);
$visit = $stmt->fetch();

$drugStmt = $pdo->prepare("SELECT d.DrugName, vd.Quantity, vd.TotalCost, vd.Frequency, vd.Dose, vd.Duration FROM VisitDrugs vd JOIN Drugs d ON vd.DrugID = d.DrugID WHERE vd.VisitID = ?");
$drugStmt->execute([$visitId]);
$drugs = $drugStmt->fetchAll();

if (!$visit) die("Record not found.");

// Fetch Settings
$settingsStmt = $pdo->query("SELECT * FROM Settings");
$settings = [];
while ($row = $settingsStmt->fetch()) {
    $settings[$row['SettingKey']] = $row['SettingValue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: <?= htmlspecialchars($settings['print_page_size'] ?? 'A4') ?>;
            margin: 15mm;
        }
        body {
            font-size: <?= htmlspecialchars($settings['print_text_size'] ?? '14px') ?>;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-border { border: 1px solid #000; box-shadow: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-3xl mx-auto bg-white p-10 shadow-lg print-border">
        
        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-6 mb-6">
            <div>
                <h1 class="text-4xl font-black text-gray-800"><?= htmlspecialchars($settings['clinic_name']) ?></h1>
                <p class="text-gray-600 font-semibold mt-1"><?= htmlspecialchars($settings['doctor_name']) ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($settings['clinic_address']) ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($settings['clinic_phone']) ?></p>
            </div>
            <div class="text-right">
                <p class="font-bold">Date: <?= date('d M Y', strtotime($visit['VisitDateTime'])) ?></p>
                <p class="font-bold text-gray-500">Time: <?= date('h:i A', strtotime($visit['VisitDateTime'])) ?></p>
                <p class="text-xl font-bold mt-2">Visit ID: #<?= str_pad($visit['VisitID'], 5, '0', STR_PAD_LEFT) ?></p>
            </div>
        </div>

        <div class="border-b border-gray-300 pb-4 mb-6">
            <h2 class="text-lg font-bold border-b inline-block mb-2">Patient Details</h2>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <p><span class="font-bold">Name:</span> <?= htmlspecialchars($visit['FirstName'] . ' ' . $visit['LastName']) ?></p>
                <p><span class="font-bold">Age/DOB:</span> <?= htmlspecialchars($visit['DOB']) ?></p>
                <p><span class="font-bold">Gender:</span> <?= htmlspecialchars($visit['Gender']) ?></p>
                <p><span class="font-bold">Phone:</span> <?= htmlspecialchars($visit['Phone']) ?></p>
            </div>
        </div>

        <div class="space-y-6">
            <?php if (!empty($visit['Complaint'])): ?>
            <div>
                <h3 class="font-bold text-gray-800 text-lg border-l-4 border-gray-800 pl-2 mb-2">Presenting Complaint</h3>
                <p class="pl-3 whitespace-pre-line"><?= htmlspecialchars($visit['Complaint']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($visit['Examination'])): ?>
            <div>
                <h3 class="font-bold text-gray-800 text-lg border-l-4 border-gray-800 pl-2 mb-2">Examination</h3>
                <p class="pl-3 whitespace-pre-line"><?= htmlspecialchars($visit['Examination']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($visit['Diagnosis'])): ?>
            <div>
                <h3 class="font-bold text-gray-800 text-lg border-l-4 border-gray-800 pl-2 mb-2">Diagnosis</h3>
                <p class="pl-3 whitespace-pre-line font-bold"><?= htmlspecialchars($visit['Diagnosis']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($visit['Treatment'])): ?>
            <div class="bg-gray-50 p-4 border rounded">
                <h3 class="font-bold text-gray-800 text-lg border-b-2 border-gray-800 pb-1 mb-2 inline-block">Rx / Treatment</h3>
                <p class="whitespace-pre-line mt-2 font-mono text-lg"><?= htmlspecialchars($visit['Treatment']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($visit['Referals']) || !empty($visit['Advice'])): ?>
            <div>
                <h3 class="font-bold text-gray-800 text-lg border-l-4 border-gray-800 pl-2 mb-2">Referrals & Advice</h3>
                <p class="pl-3 whitespace-pre-line"><?= htmlspecialchars($visit['Referals'] . "\n" . ($visit['Notes']??'')) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Billed Items Table -->
        <div class="mt-8 border-t-2 border-gray-800 pt-6">
            <h3 class="font-black text-lg mb-2 uppercase tracking-wide">Pharmacy & Billing</h3>
            <table class="w-full text-left text-sm mb-4">
                <tr class="border-b border-gray-300 bg-gray-50">
                    <th class="p-2 font-bold">Item Description</th>
                    <th class="p-2 font-bold text-center">Qty</th>
                    <th class="p-2 font-bold text-right">Cost (Rs.)</th>
                </tr>
                <?php foreach($drugs as $d): ?>
                    <tr class="border-b border-gray-100">
                        <td class="p-2">
                            <span class="font-bold"><?= htmlspecialchars($d['DrugName']) ?></span>
                            <?php 
                                $instructions = array_filter([$d['Dose'], $d['Frequency'], $d['Duration']]);
                                if(!empty($instructions)): 
                            ?>
                                <span class="block text-xs text-teal-700 italic mt-1 font-bold leading-relaxed mb-1">
                                    <?= htmlspecialchars(implode(' • ', $instructions)) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-2 text-center align-top"><?= $d['Quantity'] ?></td>
                        <td class="p-2 text-right align-top"><?= number_format($d['TotalCost'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="border-b border-gray-100">
                    <td class="p-2 font-bold" colspan="2">Professional Visit / Consultation Fee</td>
                    <td class="p-2 text-right font-bold"><?= number_format($visit['VisitFee'] ?? 0, 2) ?></td>
                </tr>
            </table>
            <div class="flex justify-between items-center bg-gray-100 p-4 rounded mt-4">
                <span class="font-black text-xl">TOTAL BILL</span>
                <span class="font-black text-2xl tracking-tight">Rs. <?= number_format($visit['TotalBill'] ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="mt-20 pt-10 border-t border-gray-400 flex justify-between">
            <p class="text-sm text-gray-500">Printed on: <?= date('Y-m-d H:i') ?></p>
            <div class="text-center">
                <p class="border-b border-black w-48 mb-1"></p>
                <p class="font-bold text-sm">Doctor's Signature</p>
            </div>
        </div>
    </div>

    <!-- Actions (Hidden during print) -->
    <div class="max-w-3xl mx-auto mt-6 flex justify-center gap-4 no-print pb-10">
        <button onclick="window.print()" class="bg-gray-900 hover:bg-black text-white px-8 py-3 rounded shadow-lg font-black transition">Print / Save as PDF</button>
        <a href="dashboard.php" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded shadow-lg font-bold transition">Done / Back</a>
    </div>

    <script>
        // Optional: Auto-print dialog on load
        window.onload = function() {
            // setTimeout(() => window.print(), 500); 
        }
    </script>
</body>
</html>
