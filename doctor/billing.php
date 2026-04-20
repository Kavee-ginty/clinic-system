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
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center z-10 relative">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Billing & Prescriptions (Visit #<?= $visitId ?>)</h1>
            <div class="flex gap-2">
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg font-medium text-sm transition-all shadow-sm">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg font-medium text-sm transition-all shadow-sm">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 flex flex-col items-center w-full relative z-0">
            
            <div class="w-full max-w-6xl bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20 flex flex-col h-full">
                
                <h2 class="text-2xl font-bold text-white mb-6 tracking-tight drop-shadow-md">Treatment & Prescription Table</h2>
                
                <!-- Datalist for Auto-suggest -->
                <datalist id="inventoryList"></datalist>

                <!-- Table UI -->
                <div class="overflow-x-auto flex-1 mb-8 rounded-2xl border border-white/10">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white/5 sticky top-0 border-b border-white/10 backdrop-blur-md">
                            <tr class="text-slate-400 text-xs uppercase tracking-wider">
                                <th class="p-4 font-medium w-1/4">Drug Name (Auto-suggest)</th>
                                <th class="p-4 font-medium w-1/6">Dose</th>
                                <th class="p-4 font-medium">Frequency (e.g. 2 0 2)</th>
                                <th class="p-4 font-medium w-24">Days</th>
                                <th class="p-4 font-medium w-28 text-center">Total Pill<br>Count</th>
                                <th class="p-4 font-medium w-20 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="billTableBody" class="divide-y divide-white/5">
                            <!-- Input Row -->
                            <tr class="bg-white/5 border-b border-white/10">
                                <td class="p-4"><input list="inventoryList" id="tDrugName" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 text-sm transition-all shadow-inner backdrop-blur-sm" placeholder="Type drug name..."></td>
                                <td class="p-4"><input type="text" id="tDose" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 text-sm transition-all shadow-inner backdrop-blur-sm" placeholder="500mg"></td>
                                <td class="p-4"><input type="text" id="tFreq" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 text-sm transition-all shadow-inner backdrop-blur-sm" placeholder="e.g. 2 2 2" oninput="calcPillCount()"></td>
                                <td class="p-4"><input type="number" id="tDays" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 text-sm transition-all shadow-inner backdrop-blur-sm" placeholder="Days" min="1" oninput="calcPillCount()"></td>
                                <td class="p-4"><input type="number" id="tTotalQty" class="w-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 font-bold p-2.5 rounded-lg focus:outline-none text-sm text-center shadow-sm backdrop-blur-sm" placeholder="Qty"></td>
                                <td class="p-4 text-center"><button onclick="addTableDrug()" class="bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/30 font-bold p-2.5 text-sm rounded-lg shadow-sm hover:shadow-[0_0_15px_rgba(99,102,241,0.3)] transition-all w-full backdrop-blur-md">+</button></td>
                            </tr>
                            <!-- Added Drugs go here -->
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-red-500 font-bold hidden mb-4 text-center" id="drugErr"></p>

                <!-- Billing Summary Footer -->
                <div class="mt-auto bg-black/20 p-8 rounded-3xl border border-dashed border-white/20 grid grid-cols-1 md:grid-cols-2 gap-8 items-end backdrop-blur-md shadow-inner">
                    
                    <div class="space-y-3">
                        <div class="bg-white/5 p-6 border border-white/10 rounded-2xl shadow-sm w-full md:w-3/4 backdrop-blur-md">
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-2">Drug Cost Subtotal</span>
                            <span class="block text-3xl font-bold text-white drop-shadow-sm">Rs. <span id="billWithoutFee">0.00</span></span>
                            <p class="text-xs text-slate-500 font-medium mt-2">(Bill without Service Charge)</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-center pb-4 border-b border-white/10">
                            <span class="font-bold text-slate-300">Consultation Fee (Rs.)</span>
                            <input type="number" id="visitFee" value="<?= htmlspecialchars($defaultVisitFee) ?>" class="w-24 text-right bg-black/20 text-white border border-white/10 rounded-xl p-2.5 font-bold shadow-inner focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 transition-all backdrop-blur-sm" onchange="updateTotal()">
                        </div>

                        <div class="flex justify-between items-center text-teal-300">
                            <span class="text-lg font-bold drop-shadow-md">Final Gross Total</span>
                            <span class="text-4xl font-bold drop-shadow-md">Rs. <span id="totalBill">0.00</span></span>
                        </div>

                        <button onclick="confirmBill()" class="w-full mt-4 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 border border-indigo-500/30 font-bold text-lg py-4 rounded-xl shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] transition-all backdrop-blur-md">
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
                tr.className = 'hover:bg-white/5 transition-colors bg-transparent dynamic-row border-b border-white/5 group';
                const tagCustom = !d.id ? `<span class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] px-1.5 py-0.5 ml-2 rounded font-semibold uppercase tracking-wider backdrop-blur-sm">Custom</span>` : '';
                tr.innerHTML = `
                    <td class="p-4 font-bold text-white drop-shadow-sm">${d.name} ${tagCustom}<br><span class="text-xs text-slate-400 font-medium mt-1 block">Rs. ${d.unit_price} &times; ${d.qty} = Rs. ${d.cost.toFixed(2)}</span></td>
                    <td class="p-4 text-sm text-slate-300 font-semibold">${d.dose}</td>
                    <td class="p-4 text-sm text-slate-300 font-mono font-semibold">${d.frequency}</td>
                    <td class="p-4 text-sm text-slate-300 font-semibold">${d.duration}</td>
                    <td class="p-4 font-bold text-center text-indigo-300 text-lg bg-indigo-500/10 border-l border-r border-white/5">${d.qty}</td>
                    <td class="p-4 text-center"><button onclick="removeDrug(${i})" class="text-rose-500 hover:text-rose-400 hover:bg-rose-500/10 px-3 py-1.5 rounded-lg font-black transition-all text-xl">&times;</button></td>
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
