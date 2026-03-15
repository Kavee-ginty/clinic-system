<?php
session_start();
if (!isset($_SESSION['doctor_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
$patientId = $_GET['patient_id'] ?? null;
$queueId = $_GET['queue_id'] ?? null;

if (!$patientId || !$queueId) {
    die("Invalid request. Patient ID and Queue ID required.");
}
?>
<?php
$pageTitle = 'Visit Record Form';
include '../includes/header.php';
?>
<body class="bg-gray-50 min-h-screen dark:bg-gray-900 transition-colors">
    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Add Visit Record</h1> <br>
        <a href="dashboard.php" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold">Back to Dashboard</a>
    </nav>

    <div class="container mx-auto p-4 max-w-4xl mt-6">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-teal-500">
            
            <div id="patientInfo" class="mb-6 p-4 bg-gray-100 rounded-lg flex justify-between items-center">
                <!-- Loaded via JS -->
            </div>

            <form id="visitForm" class="space-y-6">
                <!-- Group 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Presenting Complaint <span class="text-red-500">*</span></label>
                        <textarea id="complaint" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Examination Findings</label>
                        <textarea id="examination" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                </div>

                <!-- Group 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Investigations</label>
                        <textarea id="investigation" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Diagnosis <span class="text-red-500">*</span></label>
                        <textarea id="diagnosis" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
                    </div>
                </div>

                <!-- Group 3 -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block font-bold text-gray-700">Treatment / Prescription <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openPrescriptionModal()" class="px-4 py-1.5 bg-teal-100 text-teal-800 hover:bg-teal-200 border border-teal-300 rounded font-bold text-sm flex items-center gap-2 shadow-sm transition">
                            <span class="text-lg leading-none">+</span> Open Prescription Table
                        </button>
                    </div>
                    <textarea id="treatment" rows="4" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
                </div>

                <!-- Group 4 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Referrals</label>
                        <textarea id="referals" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Doctor's Notes</label>
                        <textarea id="notes" rows="2" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm font-bold text-gray-500">
                        Total Table Drugs: <span id="lblDrugCount" class="text-teal-600 outline outline-1 outline-teal-300 px-2 py-0.5 rounded">0</span>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg shadow-md transition text-lg">
                        Complete Visit & Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PRESCRIPTION MODAL OVERLAY -->
    <div id="rxModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">
            
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-2xl font-black text-gray-800">Add Drugs to Prescription</h2>
                <button onclick="closePrescriptionModal()" class="text-gray-400 hover:text-red-500 font-bold p-2 text-2xl leading-none">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-white">

                <!-- Table UI -->
                <div class="w-full mb-4">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100">
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
                                <td class="p-2 relative">
                                    <input type="text" id="tDrugName" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="Type drug name..." autocomplete="off">
                                    <div id="drugDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100"></div>
                                </td>
                                <td class="p-2"><input type="text" id="tDose" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="Optional"></td>
                                <td class="p-2"><input type="text" id="tFreq" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="e.g. 2 2 2" oninput="calcPillCount()"></td>
                                <td class="p-2"><input type="number" id="tDays" class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm" placeholder="Days" min="1" oninput="calcPillCount()"></td>
                                <td class="p-2"><input type="number" id="tTotalQty" class="w-full border-2 border-teal-500 bg-teal-50 p-2 rounded font-black focus:outline-none text-sm text-center" placeholder="Qty"></td>
                                <td class="p-2 text-center"><button type="button" onclick="addTableDrug()" class="bg-teal-600 hover:bg-teal-700 text-white font-bold p-2 text-sm rounded shadow-md w-full">+</button></td>
                            </tr>
                            <!-- Added Drugs go here -->
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-red-500 font-bold hidden mb-4 text-center" id="drugErr"></p>
            </div>

            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                <div class="text-sm font-bold text-gray-500">
                    Est. Drug Cost: <span class="text-teal-700 text-lg">Rs. <span id="billWithoutFee">0.00</span></span>
                </div>
                <button type="button" onclick="confirmPrescriptionModal()" class="px-8 py-3 bg-gray-900 hover:bg-black text-white font-black rounded-lg shadow-lg transition text-lg">
                    Confirm & Append to Notes
                </button>
            </div>
            
        </div>
    </div>

    <script>
        const patientId = <?= json_encode($patientId) ?>;
        const queueId = <?= json_encode($queueId) ?>;

        // Load Patient Info
        async function loadPatient() {
            const res = await fetch(`../api/get_patient.php?id=${patientId}`);
            const p = await res.json();
            document.getElementById('patientInfo').innerHTML = `
                <div>
                    <h3 class="text-xl font-bold text-gray-800">${p.FirstName} ${p.LastName} <span class="text-gray-500 text-sm">(${p.PatientNumber || 'PT-N/A'})</span></h3>
                    <p class="text-gray-600 mt-1">
                        <span class="font-bold">Age:</span> ${p.Age || 'N/A'} | 
                        <span class="font-bold">NIC:</span> ${p.NIC || 'N/A'} | 
                        <span class="font-bold">Gender:</span> ${p.Gender} | 
                        <span class="font-bold">DOB:</span> ${p.DOB}
                    </p>
                    <p class="text-gray-500 text-sm"><span class="font-bold">Phone:</span> ${p.Phone}</p>
                </div>
                <a href="history.php?patient_id=${patientId}" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 rounded font-semibold hover:bg-blue-200">View History</a>
            `;
        }
        loadPatient();

        // Submit Form
        document.getElementById('visitForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                patient_id: patientId,
                queue_id: queueId,
                complaint: document.getElementById('complaint').value,
                examination: document.getElementById('examination').value,
                investigation: document.getElementById('investigation').value,
                diagnosis: document.getElementById('diagnosis').value,
                treatment: document.getElementById('treatment').value,
                referals: document.getElementById('referals').value,
                notes: document.getElementById('notes').value,
                drugs: billDrugs
            };

            const res = await fetch('../api/add_visit.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {'Content-Type': 'application/json'}
            });
            const result = await res.json();
            
            if(result.success) {
                // Instantly navigate to print preview
                window.location.href = `print_report.php?visit_id=${result.visit_id}`;
            } else {
                showToast("Error saving record: " + result.error, "error");
            }
        });

        // ----------------------------------------------------
        // Prescription Modal Logic
        // ----------------------------------------------------
        let inventory = [];
        let billDrugs = [];

        async function loadInventory() {
            const res = await fetch('../api/inventory.php');
            inventory = await res.json();
        }

        const dInput = document.getElementById('tDrugName');
        const dDrop = document.getElementById('drugDropdown');

        dInput.addEventListener('focus', () => {
            if (inventory.length === 0) loadInventory();
            dInput.dispatchEvent(new Event('input')); // trigger dropdown
        });

        dInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            dDrop.innerHTML = '';
            
            // If empty, show full inventory, otherwise filter
            const matches = val ? inventory.filter(d => (d.DrugName || '').toLowerCase().includes(val)) : inventory;
            
            if (matches.length > 0) {
                dDrop.innerHTML = matches.map(d => `
                    <div class="p-3 cursor-pointer hover:bg-teal-50 transition flex justify-between items-center group" 
                         onclick="selectDrug('${(d.DrugName || '').replace(/'/g, "\\'")}', '${d.Dose || ''}')">
                        <div class="font-bold text-gray-700 group-hover:text-teal-700 text-sm">${d.DrugName} <span class="text-xs text-gray-400 font-normal ml-1">${d.Dose ? `[${d.Dose}]` : ''}</span></div>
                        <div class="text-[10px] font-bold ${d.Quantity < 10 ? 'text-red-500' : 'text-gray-400'}">Stock: ${d.Quantity}</div>
                    </div>
                `).join('');
                if (val) {
                    dDrop.innerHTML += `<div class="p-2 cursor-pointer bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-500 text-center border-t border-gray-100" onclick="dDrop.classList.add('hidden')">Use "${e.target.value}" as Custom Drug</div>`;
                }
                dDrop.classList.remove('hidden');
            } else {
                 dDrop.innerHTML = `<div class="p-3 cursor-pointer bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-500 text-center" onclick="dDrop.classList.add('hidden')">No matches. Use "${e.target.value}" as Custom Drug</div>`;
                 dDrop.classList.remove('hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!dInput.contains(e.target) && !dDrop.contains(e.target)) {
                dDrop.classList.add('hidden');
            }
        });

        window.selectDrug = function(name, dose) {
            dInput.value = name;
            document.getElementById('tDose').value = dose;
            dDrop.classList.add('hidden');
            document.getElementById('tFreq').focus();
        };

        function calcPillCount() {
            const freq = document.getElementById('tFreq').value.trim();
            const days = parseInt(document.getElementById('tDays').value) || 0;
            const qtyBox = document.getElementById('tTotalQty');
            
            if(!freq && !days) return; 
            
            let dailyPills = 0;
            if(freq) {
                const parts = freq.split(/[\s,+-]+/);
                parts.forEach(p => { const num = parseInt(p); if(!isNaN(num)) dailyPills += num; });
            }

            if(dailyPills > 0 && days > 0) qtyBox.value = dailyPills * days;
            else if (dailyPills > 0 && days === 0) qtyBox.value = dailyPills;
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

            const invMatch = inventory.find(d => d.DrugName.toLowerCase() === name.toLowerCase());
            const price = invMatch ? parseFloat(invMatch.UnitPrice) : 0;
            const drugId = invMatch ? invMatch.DrugID : null; 
            const stock = invMatch ? parseInt(invMatch.Quantity) : 999999; 
            
            if (drugId && qty > stock) {
                err.innerText = `${name} only has ${stock} units in stock!`;
                err.classList.remove('hidden'); return;
            }

            const existing = billDrugs.find(d => d.name.toLowerCase() === name.toLowerCase());
            if (existing && existing.drug_id) {
                if (existing.qty + qty > stock) {
                    err.innerText = `Adding this exceeds available stock (${stock}) for ${name}!`;
                    err.classList.remove('hidden'); return;
                }
                existing.qty = qty; // Override in modal edit scenario
                existing.cost = existing.qty * price;
                existing.frequency = freq || existing.frequency;
                existing.dose = dose || existing.dose;
                existing.duration = dur ? dur + ' days' : existing.duration;
            } else {
                billDrugs.push({
                    id: drugId,
                    name: name,
                    qty: qty,
                    unit_price: price,
                    cost: qty * price,
                    frequency: freq,
                    dose: dose,
                    duration: dur ? dur + ' days' : ''
                });
            }

            nameInput.value = ''; doseInput.value = ''; freqInput.value = ''; daysInput.value = ''; qtyInput.value = '';
            document.getElementById('tDrugName').focus(); 
            renderBill();
        }

        function removeDrug(idx) {
            billDrugs.splice(idx, 1);
            renderBill();
        }

        // Exposing globally for inline edits
        window.updateField = function(idx, field, val) {
            billDrugs[idx][field] = val;
            if(field === 'qty') billDrugs[idx].cost = parseInt(val) * billDrugs[idx].unit_price;
            renderBill(); // Note: rapid firing this on text inputs drops focus, so we let the user edit and blur
        }

        function updateInlineValue(idx, el, field) {
            const val = el.value;
            billDrugs[idx][field] = val;
            
            if (field === 'qty') {
                billDrugs[idx].qty = parseInt(val) || 0;
                billDrugs[idx].cost = billDrugs[idx].qty * billDrugs[idx].unit_price;
            } else if (field === 'frequency' || field === 'duration') {
                const freq = (billDrugs[idx].frequency || '').trim();
                const durStr = (billDrugs[idx].duration || '').toString().trim();
                let days = 0;
                const durMatch = durStr.match(/\d+/);
                if (durMatch) days = parseInt(durMatch[0]);

                let dailyPills = 0;
                if(freq) {
                    const parts = freq.split(/[\s,+-]+/);
                    parts.forEach(p => { const num = parseInt(p); if(!isNaN(num)) dailyPills += num; });
                }

                if (dailyPills > 0) {
                    billDrugs[idx].qty = dailyPills * (days > 0 ? days : 1);
                    billDrugs[idx].cost = billDrugs[idx].qty * billDrugs[idx].unit_price;
                }
            }
            renderBill();
        }

        function renderBill() {
            const rows = document.querySelectorAll('.dynamic-row');
            rows.forEach(r => r.remove());

            const tbody = document.getElementById('billTableBody');
            
            billDrugs.forEach((d, i) => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100 hover:bg-gray-50 bg-white dynamic-row pt-2';
                const tagCustom = !d.id ? `<span class="bg-gray-200 text-gray-600 text-[10px] px-1 ml-2 rounded font-black uppercase">Custom</span>` : '';
                tr.innerHTML = `
                    <td class="p-2 font-bold text-gray-800">${d.name} ${tagCustom}<br><span class="text-xs text-gray-500 font-normal">Rs. ${d.unit_price} x ${d.qty} = Rs. ${d.cost.toFixed(2)}</span></td>
                    <td class="p-2"><input type="text" class="w-full border p-1 rounded font-semibold text-sm focus:border-blue-500" value="${d.dose}" onchange="updateInlineValue(${i}, this, 'dose')"></td>
                    <td class="p-2"><input type="text" class="w-full border p-1 rounded font-mono font-bold text-sm focus:border-blue-500" value="${d.frequency}" onchange="updateInlineValue(${i}, this, 'frequency')"></td>
                    <td class="p-2"><input type="text" class="w-full border p-1 rounded font-bold text-sm focus:border-blue-500" value="${d.duration}" onchange="updateInlineValue(${i}, this, 'duration')"></td>
                    <td class="p-2"><input type="number" class="w-full border-2 border-teal-200 p-1 rounded font-black text-center text-teal-600 text-lg focus:border-teal-500" value="${d.qty}" onchange="updateInlineValue(${i}, this, 'qty')"></td>
                    <td class="p-2 text-center"><button type="button" onclick="removeDrug(${i})" class="text-red-500 hover:bg-red-50 px-3 py-1 rounded font-black transition">&times;</button></td>
                `;
                tbody.appendChild(tr);
            });
            
            const drugTotal = billDrugs.reduce((sum, d) => sum + d.cost, 0);
            document.getElementById('billWithoutFee').innerText = drugTotal.toFixed(2);
            document.getElementById('lblDrugCount').innerText = billDrugs.length;
        }

        function openPrescriptionModal() {
            if(inventory.length === 0) loadInventory();
            document.getElementById('rxModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('tDrugName').focus(), 100);
        }

        function closePrescriptionModal() {
            document.getElementById('rxModal').classList.add('hidden');
        }

        function confirmPrescriptionModal() {
            if(billDrugs.length === 0) return closePrescriptionModal();

            // Append formatting block
            let rxString = "\n\n--- PRESCRIPTION ---\n";
            billDrugs.forEach((d, index) => {
                let line = `${index + 1}. ${d.name.toUpperCase()}`;
                if (d.dose) line += ` (${d.dose})`;
                
                let instructions = [];
                if (d.frequency) instructions.push(`Sig: ${d.frequency}`);
                if (d.duration) {
                    const durStr = d.duration.toString().toLowerCase();
                    const durText = durStr.includes('day') || durStr.includes('week') || durStr.includes('month') ? d.duration : d.duration + ' days';
                    instructions.push(`for ${durText}`);
                }
                
                if (instructions.length > 0) line += ` - ${instructions.join(' ')}`;
                line += ` [Dispense: ${d.qty}]\n`;
                
                rxString += line;
            });
            
            const tat = document.getElementById('treatment');
            if(tat.value && !tat.value.endsWith('\n')) tat.value += "\n";
            tat.value += rxString.trim();
            closePrescriptionModal();
            showToast("Prescription injected into notes!");
        }

    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
