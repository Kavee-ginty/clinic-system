<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$visitId = $_GET['visit_id'] ?? null;
if (!$visitId)
    die("Visit ID required.");

// Fetch Data
$stmt = $pdo->prepare("SELECT * FROM Visits v JOIN Patients p ON v.PatientID = p.PatientID WHERE v.VisitID = ?");
$stmt->execute([$visitId]);
$visit = $stmt->fetch();

$drugStmt = $pdo->prepare("SELECT COALESCE(vd.DrugName, d.DrugName) AS DrugName, vd.Quantity, vd.TotalCost, vd.Frequency, vd.Dose, vd.Duration FROM VisitDrugs vd LEFT JOIN Drugs d ON vd.DrugID = d.DrugID WHERE vd.VisitID = ?");
$drugStmt->execute([$visitId]);
$drugs = $drugStmt->fetchAll();

if (!$visit)
    die("Record not found.");

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
            size: A5 portrait;
            margin: 5mm;
        }

        body {
            font-size: 13px;
            font-family: Arial, sans-serif;
            color: #333;
            background-color: transparent;
            margin: 0;
            padding: 0;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            html,
            body {
                height: 99%;
                margin: 0;
                padding: 0;
                overflow: hidden;
                background-color: white !important;
                justify-content: flex-start !important;
                align-items: flex-start !important;
            }

            * {
                background-color: transparent !important;
            }

            .print-area {
                box-shadow: none !important;
                max-width: 100% !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                min-height: unset !important;
                height: auto !important;
            }
        }

        .header-text-large {
            font-size: 20px;
            font-weight: 800;
            color: #4b5563;
            /* text-gray-600 */
        }
    </style>
</head>

<body class="p-4 flex flex-col md:flex-row items-center md:items-start justify-center gap-6">

    <div class="max-w-[148mm] w-full bg-white p-6 shadow-xl print-area rounded-md shrink-0" style="min-height: 210mm;">

        <!-- Header -->
        <div class="flex justify-between items-start border-b-[2px] border-gray-500 pb-3 mb-4">
            <div style="max-width: 50%; width: <?= htmlspecialchars(str_replace(['w-', '[', ']'], '', $settings['logo_width'] ?? '28%')) ?>;"
                class="pr-2">
                <img src="../logo.jpeg" alt="Logo" class="w-full object-contain mix-blend-multiply max-w-20">
            </div>
            <div class="w-[55%] text-center px-1">
                <h1 class="header-text-large leading-tight font-sans">
                    <?= htmlspecialchars($settings['clinic_name'] ?? 'Royal Channel Center') ?>
                </h1>
                <p class="text-gray-700 text-[10px] mt-1">
                    <?= htmlspecialchars($settings['clinic_address'] ?? 'No48/1, Muruthalawa, Kandy') ?>
                </p>
                <p class="text-gray-700 text-[10px]">
                    <?= htmlspecialchars($settings['clinic_phone'] ?? '0812 412 400 , 0776 020 964') ?>
                </p>
                <p class="text-gray-700 text-[10px] font-medium break-all">
                    <?= htmlspecialchars($settings['clinic_email'] ?? 'royalchannelcenter@gmail.com') ?>
                </p>
            </div>
            <div class="w-[28%] text-right pl-2">
                <br>
                <p class="text-[12px] font-bold text-gray-700">
                    <?= htmlspecialchars($settings['doctor_name'] ?? 'Dr. Mangala Kumara') ?>
                </p>
                <p class="text-[11px] text-gray-700">
                    <?= htmlspecialchars($settings['doctor_qualifications'] ?? 'MBBS Peradeniya') ?>
                </p>
                <p class="text-[11px] text-gray-700">SLMC - <?= htmlspecialchars($settings['doctor_slmc'] ?? '21307') ?>
                </p>
            </div>
        </div>

        <!-- Patient & Visit Info -->
        <div class="flex justify-between items-start mb-5 border-b border-gray-300 pb-2">
            <div class="w-[60%]">
                <p class="text-[12px] font-bold text-gray-700 leading-tight">ID:
                    <?= htmlspecialchars($visit['VisitID']) ?> -
                    <?= htmlspecialchars(strtoupper($visit['FirstName'] . ' ' . $visit['LastName'])) ?>
                    (<?= substr($visit['Gender'], 0, 1) ?>) / <?= htmlspecialchars($visit['Age']) ?> Y
                </p>
            </div>
            <div class="w-[40%] text-right">
                <p class="text-[12px] font-bold text-gray-700 leading-tight">Date of Visit:
                    <br><?= date('d-M-Y, h:i A', strtotime($visit['VisitDateTime'])) ?>
                </p>
            </div>
        </div>

        <!-- Complaints & Diagnosis -->
        <div class="space-y-3 mb-5">
            <?php if (!empty($visit['Complaint'])): ?>
                <div>
                    <h3 class="font-bold text-gray-700 text-[13px]">Chief Complaints:</h3>
                    <?php foreach (explode("\n", trim($visit['Complaint'])) as $line): ?>
                        <?php if (trim($line)): ?>
                            <p class="font-medium text-gray-700 text-[12px] ml-3 uppercase">* <?= htmlspecialchars(trim($line)) ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($visit['Examination'])): ?>
                <div>
                    <h3 class="font-bold text-gray-700 text-[13px]">Examination Findings:</h3>
                    <?php foreach (explode("\n", trim($visit['Examination'])) as $line): ?>
                        <?php if (trim($line)): ?>
                            <p class="font-medium text-gray-700 text-[12px] ml-3 uppercase">* <?= htmlspecialchars(trim($line)) ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($visit['Investigation'])): ?>
                <div>
                    <h3 class="font-bold text-gray-700 text-[13px]">Investigations:</h3>
                    <?php foreach (explode("\n", trim($visit['Investigation'])) as $line): ?>
                        <?php if (trim($line)): ?>
                            <p class="font-medium text-gray-700 text-[12px] ml-3 uppercase">* <?= htmlspecialchars(trim($line)) ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($visit['Diagnosis'])): ?>
                <div>
                    <h3 class="font-bold text-gray-700 text-[13px]">Diagnosis:</h3>
                    <?php foreach (explode("\n", trim($visit['Diagnosis'])) as $line): ?>
                        <?php if (trim($line)): ?>
                            <p class="font-medium text-gray-700 text-[12px] ml-3 uppercase">* <?= htmlspecialchars(trim($line)) ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rx Table -->
        <div class="w-full mt-3">
            <table class="w-full text-left align-top border-collapse">
                <thead>
                    <tr class="border-b-[1.5px] border-t-[1.5px] border-gray-600 text-gray-800 bg-gray-50/50">
                        <th class="py-1 text-[12px] font-bold pl-1">Drug Name</th>
                        <th class="py-1 text-[12px] font-bold border-l border-gray-300 text-center w-6">M</th>
                        <th class="py-1 text-[12px] font-bold border-l border-gray-300 text-center w-6">A</th>
                        <th class="py-1 text-[12px] font-bold border-l border-gray-300 text-center w-6">E</th>
                        <th class="py-1 text-[12px] font-bold border-l border-gray-300 text-center w-6">N</th>
                        <th class="py-1 text-[12px] font-bold border-l border-gray-300 pl-1">Duration</th>
                        <th class="py-1 text-[12px] font-bold text-center border-l border-gray-300">Total Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($drugs)): ?>
                        <?php foreach ($drugs as $index => $drug): ?>
                            <tr class="border-b-[1px] border-gray-300">
                                <td class="py-2 pl-1 pr-1 text-gray-700 font-bold text-[12px]">
                                    <?= htmlspecialchars(ucfirst(strtolower($drug['DrugName']))) ?>
                                    <?= htmlspecialchars(strtolower($drug['Dose'])) ?>
                                </td>
                                <?php
                                $freq = trim($drug['Frequency']);
                                $parts = preg_split('/[\s,\-]+/', $freq, -1, PREG_SPLIT_NO_EMPTY);
                                $parts = array_pad($parts, 4, '-');
                                ?>
                                <td class="py-2 text-center text-gray-700 text-[12px] font-medium border-l border-gray-200">
                                    <?= htmlspecialchars($parts[0]) ?>
                                </td>
                                <td class="py-2 text-center text-gray-700 text-[12px] font-medium border-l border-gray-200">
                                    <?= htmlspecialchars($parts[1]) ?>
                                </td>
                                <td class="py-2 text-center text-gray-700 text-[12px] font-medium border-l border-gray-200">
                                    <?= htmlspecialchars($parts[2]) ?>
                                </td>
                                <td class="py-2 text-center text-gray-700 text-[12px] font-medium border-l border-gray-200">
                                    <?= htmlspecialchars($parts[3]) ?>
                                </td>
                                <td class="py-2 pl-1 pr-1 text-gray-700 text-[12px] font-medium border-l border-gray-200">
                                    <?php
                                    $dur = trim($drug['Duration']);
                                    if (is_numeric($dur))
                                        $dur .= ' Days';
                                    echo htmlspecialchars(ucwords(strtolower($dur)));
                                    ?>
                                </td>
                                <td class="py-2 pr-1 text-right text-gray-700 text-[12px] font-bold border-l border-gray-200">
                                    <?= htmlspecialchars($drug['Quantity']) ?>
                                    <?php if ($drug['Quantity'] == 0): ?> - <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php if (!empty($visit['Treatment'])): ?>
                            <tr>
                                <td colspan="7" class="py-3 whitespace-pre-line text-[12px] pl-1 font-mono">
                                    <span class="font-bold text-gray-700 block mb-1">Treatment / Prescription:</span>
                                    <?= htmlspecialchars($visit['Treatment']) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($visit['Referals']) || !empty($visit['Notes'])): ?>
            <div class="mt-6 border-t border-gray-300 pt-3">
                <?php if (!empty($visit['Referals'])): ?>
                    <div class="mb-2">
                        <h3 class="font-bold text-gray-700 text-[13px]">Referrals:</h3>
                        <p class="mt-1 whitespace-pre-line text-[12px] text-gray-700 ml-3">
                            <?= htmlspecialchars($visit['Referals']) ?>
                        </p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($visit['Notes'])): ?>
                    <div>
                        <h3 class="font-bold text-gray-700 text-[13px]">Notes / Advice:</h3>
                        <p class="mt-1 whitespace-pre-line text-[12px] text-gray-700 ml-3">
                            <?= htmlspecialchars($visit['Notes']) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- Actions (Hidden during print) -->
    <div class="w-full md:w-auto flex flex-col gap-3 no-print sticky top-4 shrink-0">
        <button onclick="window.print()"
            class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-3 rounded shadow-md font-bold text-sm transition">Preview
            Print</button>
        <button onclick="directPrint()"
            class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded shadow-md font-bold text-sm transition">Direct
            Print</button>
        <button onclick="if(window.opener) window.opener.location.href='dashboard.php'; window.close();"
            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded shadow-md font-bold text-sm transition mt-4">Close
            Tab</button>
    </div>

    <script>
        function directPrint() {
            // Browsers don't support silent printing without kiosk mode or special plugins.
            // This button triggers window.print() and can work as Quick Print immediately closing the tab after (or preserving workflow).
            window.print();
        }
    </script>
</body>

</html>