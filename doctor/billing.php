<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$visitId = $_GET['visit_id'] ?? null;
if (!$visitId) die("Visit ID required.");

// Get visit default settings
$stmt = $pdo->query("SELECT SettingValue FROM Settings WHERE SettingKey = 'visit_fee'");
$defaultVisitFee = $stmt->fetchColumn() ?: 500;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Prescription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-teal-900 text-white p-4 shadow-md flex justify-between items-center">
            <h1 class="text-xl font-bold">Billing & Prescriptions (Visit #<?= $visitId ?>)</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-teal-600 hover:bg-teal-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1 bg-teal-700 hover:bg-teal-800 rounded font-bold text-sm transition">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 flex flex-col items-center">
            
            <div class="w-full max-w-4xl bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Left: Drug Selection -->
                    <div>
                        <h2 class="text-xl font-black text-gray-800 mb-4">Add Drugs to Bill</h2>
                        <select id="drugSelect" class="w-full border-2 border-gray-200 p-3 rounded-lg mb-4 font-bold focus:border-teal-500">
                            <option value="">-- Select Drug --</option>
                        </select>
                        <div class="flex flex-col gap-2">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" id="drugDose" placeholder="Dose (e.g. 500mg)" class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-teal-500">
                                <input type="text" id="drugDur" placeholder="Duration (e.g. 5 days)" class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-teal-500">
                            </div>
                            <input type="text" id="drugFreq" placeholder="Frequency (e.g. 2 times a day)" class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-teal-500">
                            <div class="flex gap-2">
                                <input type="number" id="drugQty" placeholder="Total Dispense Qty" min="1" value="1" class="w-1/2 border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-teal-500 text-sm">
                                <button onclick="addDrug()" class="w-1/2 bg-teal-600 hover:bg-teal-700 text-white font-bold p-3 rounded-lg shadow-md transition">Add to Prescrip.</button>
                            </div>
                        </div>
                        <p class="text-xs text-red-500 mt-2 font-bold hidden" id="drugErr">Not enough stock!</p>
                    </div>

                    <!-- Right: Bill Summary -->
                    <div class="bg-gray-50 p-6 rounded-xl border-2 border-dashed border-gray-300">
                        <h2 class="text-xl font-black text-gray-800 mb-4 text-center">Final Bill Calculation</h2>
                        
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                            <span class="font-bold text-gray-600">Consultation Fee (Rs.)</span>
                            <input type="number" id="visitFee" value="<?= htmlspecialchars($defaultVisitFee) ?>" class="w-24 text-right border rounded p-1 font-black shadow-inner bg-white" onchange="updateTotal()">
                        </div>

                        <div id="selectedDrugsList" class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                            <!-- Selected drugs appear here -->
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t-2 border-gray-800">
                            <span class="text-lg font-black text-gray-900">Total Bill (Rs.)</span>
                            <span class="text-3xl font-black text-teal-600" id="totalBill">0.00</span>
                        </div>

                        <button onclick="confirmBill()" class="w-full mt-6 bg-gray-900 hover:bg-black text-white font-black text-xl py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                            Confirm & Generate Print
                        </button>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <script>
        const visitId = <?= json_encode($visitId) ?>;
        let inventory = [];
        let billDrugs = [];

        async function loadInventory() {
            const res = await fetch('../api/inventory.php');
            inventory = await res.json();
            
            const sel = document.getElementById('drugSelect');
            sel.innerHTML = '<option value="">-- Select Drug --</option>';
            inventory.forEach(d => {
                sel.innerHTML += `<option value="${d.DrugID}" data-price="${d.UnitPrice}" data-stock="${d.Quantity}">${d.DrugName} (Stock: ${d.Quantity}) - Rs. ${d.UnitPrice}</option>`;
            });
        }

        function addDrug() {
            const sel = document.getElementById('drugSelect');
            const qtyInput = document.getElementById('drugQty');
            const freqInput = document.getElementById('drugFreq');
            const doseInput = document.getElementById('drugDose');
            const durInput = document.getElementById('drugDur');
            const err = document.getElementById('drugErr');
            err.classList.add('hidden');

            if(!sel.value) return;
            const opt = sel.options[sel.selectedIndex];
            const price = parseFloat(opt.getAttribute('data-price'));
            const stock = parseInt(opt.getAttribute('data-stock'));
            const qty = parseInt(qtyInput.value);
            const freq = freqInput.value.trim();
            const dose = doseInput.value.trim();
            const dur = durInput.value.trim();

            if (qty > stock) {
                err.innerText = "Quantity exceeds available stock (" + stock + ")!";
                err.classList.remove('hidden');
                return;
            }

            const existing = billDrugs.find(d => d.id == sel.value);
            if (existing) {
                if (existing.qty + qty > stock) {
                    err.innerText = "Total quantity exceeds stock!";
                    err.classList.remove('hidden');
                    return;
                }
                existing.qty += qty;
                existing.cost = existing.qty * price;
                existing.frequency = freq || existing.frequency;
                existing.dose = dose || existing.dose;
                existing.duration = dur || existing.duration;
            } else {
                billDrugs.push({
                    id: sel.value,
                    name: opt.text.split(' (')[0],
                    qty: qty,
                    unit_price: price,
                    cost: qty * price,
                    frequency: freq,
                    dose: dose,
                    duration: dur
                });
            }

            qtyInput.value = 1;
            freqInput.value = '';
            doseInput.value = '';
            durInput.value = '';
            sel.value = "";
            renderBill();
        }

        function removeDrug(idx) {
            billDrugs.splice(idx, 1);
            renderBill();
        }

        function renderBill() {
            const list = document.getElementById('selectedDrugsList');
            list.innerHTML = billDrugs.map((d, i) => `
                <div class="flex justify-between items-center bg-white p-2 rounded shadow-sm border border-gray-100 text-sm">
                    <div class="flex-1">
                        <span class="font-bold block">${d.name}</span>
                        ${d.frequency || d.dose || d.duration ? `<span class="text-xs text-teal-600 block font-bold mb-1 mt-1 leading-relaxed"><svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${[d.dose, d.frequency, d.duration].filter(Boolean).join(' &bull; ')}</span>` : ''}
                        <span class="text-gray-500 text-xs">${d.qty}x @ Rs.${d.unit_price}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-black text-gray-800">Rs. ${d.cost.toFixed(2)}</span>
                        <button onclick="removeDrug(${i})" class="text-red-500 font-black hover:text-red-700">&times;</button>
                    </div>
                </div>
            `).join('');
            
            updateTotal();
        }

        function updateTotal() {
            const fee = parseFloat(document.getElementById('visitFee').value) || 0;
            const drugTotal = billDrugs.reduce((sum, d) => sum + d.cost, 0);
            const total = fee + drugTotal;
            document.getElementById('totalBill').innerText = total.toFixed(2);
        }

        async function confirmBill() {
            const payload = {
                visit_id: visitId,
                visit_fee: parseFloat(document.getElementById('visitFee').value) || 0,
                total_bill: parseFloat(document.getElementById('totalBill').innerText),
                drugs: billDrugs
            };

            const res = await fetch('../api/save_billing.php', {
                method: 'POST', body: JSON.stringify(payload), headers: {'Content-Type': 'application/json'}
            });
            const data = await res.json();
            
            if(data.success) {
                window.location.href = `print_report.php?visit_id=${visitId}`;
            } else {
                showToast("Error saving bill: " + data.error, "error");
            }
        }

        loadInventory();
        updateTotal(); // initial calculate
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
