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
            
            <div class="w-full max-w-6xl bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                
                <h2 class="text-xl font-black text-gray-800 mb-4 border-b pb-2">Treatment & Prescription Table</h2>
                
                <!-- Datalist for Auto-suggest -->
                <datalist id="inventoryList"></datalist>

                <!-- Table UI -->
                <div class="overflow-x-auto flex-1 mb-4">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="p-3 font-bold text-gray-700 w-1/4">Drug Name (Auto-suggest)</th>
                                <th class="p-3 font-bold text-gray-700 w-1/6">Dose</th>
                                <th class="p-3 font-bold text-gray-700">Frequency (e.g. 2 0 2)</th>
                                <th class="p-3 font-bold text-gray-700 w-24">Days</th>
                                <th class="p-3 font-bold text-gray-700 w-24 text-center">Total Pill<br>Count</th>
                                <th class="p-3 font-bold text-gray-700 w-20 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="billTableBody">
                            <!-- Input Row -->
                            <tr class="bg-blue-50/50 border-b-2 border-blue-100">
                                <td class="p-2"><input list="inventoryList" id="tDrugName" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="Type drug name..."></td>
                                <td class="p-2"><input type="text" id="tDose" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="500mg"></td>
                                <td class="p-2"><input type="text" id="tFreq" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="e.g. 2 2 2" oninput="calcPillCount()"></td>
                                <td class="p-2"><input type="number" id="tDays" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="Days" min="1" oninput="calcPillCount()"></td>
                                <td class="p-2"><input type="number" id="tTotalQty" class="w-full border-2 border-teal-500 bg-teal-50 p-2 rounded font-black focus:outline-none text-sm text-center" placeholder="Qty"></td>
                                <td class="p-2 text-center"><button onclick="addTableDrug()" class="bg-teal-600 hover:bg-teal-700 text-white font-bold p-2 text-sm rounded shadow-md w-full">+</button></td>
                            </tr>
                            <!-- Added Drugs go here -->
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-red-500 font-bold hidden mb-4 text-center" id="drugErr"></p>

                <!-- Billing Summary Footer -->
                <div class="mt-auto bg-gray-50 p-6 rounded-xl border-2 border-dashed border-gray-300 grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    
                    <div class="space-y-3">
                        <div class="bg-white p-4 border rounded-lg shadow-sm w-full md:w-3/4 mb-4">
                            <span class="block text-gray-500 text-sm font-bold uppercase mb-1">Drug Cost Subtotal</span>
                            <span class="block text-2xl font-black text-gray-800">Rs. <span id="billWithoutFee">0.00</span></span>
                            <p class="text-xs text-gray-400 font-bold mt-1">(Bill without Service Charge)</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="font-bold text-gray-600">Consultation Fee (Rs.)</span>
                            <input type="number" id="visitFee" value="<?= htmlspecialchars($defaultVisitFee) ?>" class="w-24 text-right border-2 border-gray-300 rounded-lg p-2 font-black shadow-inner bg-white focus:border-teal-500" onchange="updateTotal()">
                        </div>

                        <div class="flex justify-between items-center text-teal-700">
                            <span class="text-lg font-black">Final Gross Total</span>
                            <span class="text-4xl font-black">Rs. <span id="totalBill">0.00</span></span>
                        </div>

                        <button onclick="confirmBill()" class="w-full mt-2 bg-gray-900 hover:bg-black text-white font-black text-lg py-4 rounded-xl shadow-lg transition transform active:scale-95 text-center">
                            Save Record & Generate Print
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
            
            const list = document.getElementById('inventoryList');
            list.innerHTML = '';
            inventory.forEach(d => {
                list.innerHTML += `<option value="${d.DrugName}">${d.Dose ? `[${d.Dose}] ` : ''}Rs. ${d.UnitPrice} (Stock: ${d.Quantity})</option>`;
            });
        }

        // Auto-fill logic when selecting from datalist
        document.getElementById('tDrugName').addEventListener('input', (e) => {
            const val = e.target.value;
            const match = inventory.find(d => d.DrugName.toLowerCase() === val.toLowerCase());
            if(match && match.Dose) {
                document.getElementById('tDose').value = match.Dose;
            }
        });

        // Pill Calculator
        function calcPillCount() {
            const freq = document.getElementById('tFreq').value.trim();
            const days = parseInt(document.getElementById('tDays').value) || 0;
            const qtyBox = document.getElementById('tTotalQty');
            
            if(!freq && !days) return; // Keep manual entry if they don't use frequency
            
            // "2 2 2" -> [2, 2, 2] -> 6 per day
            let dailyPills = 0;
            if(freq) {
                const parts = freq.split(/[\s,+-]+/); // match spaces, commas, plus
                parts.forEach(p => {
                    const num = parseInt(p);
                    if(!isNaN(num)) dailyPills += num;
                });
            }

            if(dailyPills > 0 && days > 0) {
                qtyBox.value = dailyPills * days;
            } else if (dailyPills > 0 && days === 0) {
                qtyBox.value = dailyPills;
            }
        }

        function addTableDrug() {
            const nameInput = document.getElementById('tDrugName');
            const doseInput = document.getElementById('tDose');
            const freqInput = document.getElementById('tFreq');
            const daysInput = document.getElementById('tDays');
            const qtyInput = document.getElementById('tTotalQty');
            const err = document.getElementById('drugErr');
            err.classList.add('hidden');

            const name = nameInput.value.trim();
            if(!name) return;

            const dose = doseInput.value.trim();
            const freq = freqInput.value.trim();
            const dur = daysInput.value.trim();
            const qty = parseInt(qtyInput.value) || 0;

            // Check if it exists in inventory to get price/ID
            const invMatch = inventory.find(d => d.DrugName.toLowerCase() === name.toLowerCase());
            const price = invMatch ? parseFloat(invMatch.UnitPrice) : 0;
            const drugId = invMatch ? invMatch.DrugID : null; // If custom drug, drugId is null/empty
            const stock = invMatch ? parseInt(invMatch.Quantity) : 999999; // Assume unlimited for custom drugs
            
            if (drugId && qty > stock) {
                err.innerText = `${name} only has ${stock} units in stock!`;
                err.classList.remove('hidden');
                return;
            }

            // Always add a new row in a prescription context usually, OR group them
            const existing = billDrugs.find(d => d.name.toLowerCase() === name.toLowerCase());
            if (existing && existing.drug_id) { // Group only if it's a known drug, custom drugs we might want separate lines
                if (existing.qty + qty > stock) {
                    err.innerText = `Adding this exceeds available stock (${stock}) for ${name}!`;
                    err.classList.remove('hidden');
                    return;
                }
                existing.qty += qty;
                existing.cost = existing.qty * price;
                existing.frequency = freq || existing.frequency;
                existing.dose = dose || existing.dose;
                existing.duration = dur ? dur + ' days' : existing.duration;
            } else {
                billDrugs.push({
                    id: drugId, // backend supports null/0 for custom text
                    name: name,
                    qty: qty,
                    unit_price: price,
                    cost: qty * price,
                    frequency: freq,
                    dose: dose,
                    duration: dur ? dur + ' days' : ''
                });
            }

            // clear inputs
            nameInput.value = ''; doseInput.value = ''; freqInput.value = ''; daysInput.value = ''; qtyInput.value = '';
            document.getElementById('tDrugName').focus(); // UX keep focus for rapid entry

            renderBill();
        }

        function removeDrug(idx) {
            billDrugs.splice(idx, 1);
            renderBill();
        }

        function renderBill() {
            // Remove all existing rows except the input row
            const rows = document.querySelectorAll('.dynamic-row');
            rows.forEach(r => r.remove());

            const tbody = document.getElementById('billTableBody');
            
            billDrugs.forEach((d, i) => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100 hover:bg-gray-50 bg-white dynamic-row pt-2';
                const tagCustom = !d.id ? `<span class="bg-gray-200 text-gray-600 text-[10px] px-1 ml-2 rounded font-black uppercase">Custom</span>` : '';
                tr.innerHTML = `
                    <td class="p-3 font-bold text-gray-800">${d.name} ${tagCustom}<br><span class="text-xs text-gray-500 font-normal">Rs. ${d.unit_price} x ${d.qty} = Rs. ${d.cost.toFixed(2)}</span></td>
                    <td class="p-3 text-sm text-gray-600 font-semibold">${d.dose}</td>
                    <td class="p-3 text-sm text-gray-600 font-mono font-bold">${d.frequency}</td>
                    <td class="p-3 text-sm text-gray-600 font-bold">${d.duration}</td>
                    <td class="p-3 font-black text-center text-teal-600 text-lg">${d.qty}</td>
                    <td class="p-3 text-center"><button onclick="removeDrug(${i})" class="text-red-500 hover:bg-red-50 px-3 py-1 rounded font-black transition">&times;</button></td>
                `;
                tbody.appendChild(tr);
            });
            
            updateTotal();
        }

        function updateTotal() {
            const fee = parseFloat(document.getElementById('visitFee').value) || 0;
            const drugTotal = billDrugs.reduce((sum, d) => sum + d.cost, 0);
            const total = fee + drugTotal;
            document.getElementById('billWithoutFee').innerText = drugTotal.toFixed(2);
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
