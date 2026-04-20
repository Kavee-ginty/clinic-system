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

<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex min-h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center w-full z-10 absolute top-0">
        <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Add Visit Record</h1>
        <a href="dashboard.php" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 rounded-lg font-medium transition-all shadow-sm">Back to Dashboard</a>
    </nav>

    <div class="container mx-auto p-4 max-w-4xl mt-24 mb-10 overflow-y-auto z-0 relative">
        <div class="bg-white/5 backdrop-blur-2xl p-8 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20">

            <div id="patientInfo" class="mb-8 p-5 bg-black/20 border border-white/5 rounded-2xl flex justify-between items-center shadow-inner backdrop-blur-sm">
                <!-- Loaded via JS -->
            </div>

            <form id="visitForm" class="space-y-8">
                <!-- Group 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-medium text-slate-300 mb-2">Presenting Complaint <span class="text-rose-400">*</span></label>
                        <textarea id="complaint" rows="3"
                            class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"
                            required></textarea>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-300 mb-2">Examination Findings</label>
                        <textarea id="examination" rows="3"
                            class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"></textarea>
                    </div>
                </div>

                <!-- Group 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-medium text-slate-300 mb-2">Investigations</label>
                        <textarea id="investigation" rows="2"
                            class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"></textarea>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-300 mb-2">Diagnosis <span class="text-rose-400">*</span></label>
                        <textarea id="diagnosis" rows="2"
                            class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"
                            required></textarea>
                    </div>
                </div>

                <!-- Group 3 -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block font-medium text-slate-300">Treatment / Prescription <span class="text-rose-400">*</span></label>
                        <button type="button" onclick="openPrescriptionModal()"
                            class="px-5 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 hover:border-indigo-500/50 backdrop-blur-md rounded-xl font-medium text-sm flex items-center gap-2 shadow-sm transition-all hover:shadow-[0_0_15px_rgba(99,102,241,0.3)]">
                            <span class="text-lg leading-none">+</span> Open Prescription Table
                        </button>
                    </div>
                    <textarea id="treatment" rows="4"
                        class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"
                        required></textarea>
                </div>

                <!-- Group 4 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-medium text-slate-300 mb-2">Referrals</label>
                        <textarea id="referals" rows="2"
                            class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"></textarea>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-300 mb-2">Doctor's Notes</label>
                        <textarea id="notes" rows="2"
                            class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30 transition-all shadow-inner backdrop-blur-sm"></textarea>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-white/10">
                    <div class="text-sm font-medium text-slate-400">
                        Total Table Drugs: <span id="lblDrugCount"
                            class="text-teal-300 bg-teal-500/10 border border-teal-500/20 px-2.5 py-1 rounded-md font-semibold">0</span>
                    </div>
                    <button type="submit"
                        class="px-8 py-3.5 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 border border-indigo-500/30 hover:border-indigo-500/50 font-medium rounded-xl shadow-sm transition-all text-lg backdrop-blur-md hover:shadow-[0_0_20px_rgba(99,102,241,0.4)]">
                        Complete Visit & Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PRESCRIPTION MODAL OVERLAY -->
    <div id="rxModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-md transition-opacity">
        <div class="bg-slate-900/80 backdrop-blur-2xl rounded-[2rem] shadow-2xl border border-white/10 w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-t from-indigo-900/10 to-transparent pointer-events-none"></div>

            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5 relative z-10">
                <h2 class="text-xl font-bold text-white tracking-tight drop-shadow-md">Add Drugs to Prescription</h2>
                <button onclick="closePrescriptionModal()"
                    class="text-slate-400 hover:text-rose-400 font-black px-2 py-1 text-2xl leading-none transition-colors rounded-lg hover:bg-rose-500/10">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 relative z-10">

                <!-- Table UI -->
                <div class="w-full mb-4 overflow-hidden rounded-2xl border border-white/10">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white/5 backdrop-blur-md">
                            <tr>
                                <th class="p-4 font-medium text-slate-400 text-sm uppercase tracking-wider w-1/4 border-b border-white/10">Drug Name</th>
                                <th class="p-4 font-medium text-slate-400 text-sm uppercase tracking-wider w-1/6 border-b border-white/10">Dose</th>
                                <th class="p-4 font-medium text-slate-400 text-sm uppercase tracking-wider border-b border-white/10">Dosage Grid (M-A-E-N)</th>
                                <th class="p-4 font-medium text-slate-400 text-sm uppercase tracking-wider w-24 border-b border-white/10">Duration</th>
                                <th class="p-4 font-medium text-slate-400 text-sm uppercase tracking-wider w-24 text-center border-b border-white/10">Qty</th>
                                <th class="p-4 font-medium text-slate-400 text-sm uppercase tracking-wider w-20 text-center border-b border-white/10">Action</th>
                            </tr>
                        </thead>
                        <tbody id="billTableBody" class="divide-y divide-white/5">
                            <!-- Input Row -->
                            <tr class="bg-white/5">
                                <td class="p-3 relative">
                                    <input type="text" id="tDrugName"
                                        class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 text-sm transition-all"
                                        placeholder="Type drug name..." autocomplete="off">
                                    <div id="drugDropdown"
                                        class="hidden absolute top-full left-0 right-0 mt-1 bg-slate-800/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-white/5">
                                    </div>
                                </td>
                                <td class="p-3"><input type="text" id="tDose"
                                        class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 text-sm transition-all"
                                        placeholder="Optional"></td>
                                <td class="p-3"><input type="text" id="tFreq"
                                        class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 text-sm transition-all"
                                        placeholder="e.g. 1-0-1-0" oninput="calcPillCount()"></td>
                                <td class="p-3"><input type="number" id="tDays"
                                        class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2.5 rounded-lg font-medium focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 text-sm transition-all"
                                        placeholder="Days" min="1" oninput="calcPillCount()"></td>
                                <td class="p-3"><input type="number" id="tTotalQty"
                                        class="w-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 p-2.5 rounded-lg font-bold focus:outline-none text-sm text-center"
                                        placeholder="Qty"></td>
                                <td class="p-3 text-center">
                                    <button type="button" onclick="addTableDrug()"
                                        class="bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/30 font-bold p-2.5 text-sm rounded-lg backdrop-blur-md transition-all w-full shadow-sm hover:shadow-[0_0_15px_rgba(99,102,241,0.3)]">+</button>
                                </td>
                            </tr>
                            <!-- Added Drugs go here -->
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-rose-400 font-bold hidden mt-2 text-center drop-shadow-md" id="drugErr"></p>
            </div>

            <div class="p-6 border-t border-white/10 bg-white/5 flex justify-between items-center relative z-10">
                <div class="text-sm font-medium text-slate-300">
                    Est. Drug Cost: <span class="text-teal-300 font-bold text-lg ml-2">Rs. <span id="billWithoutFee">0.00</span></span>
                </div>
                <button type="button" onclick="confirmPrescriptionModal()"
                    class="px-8 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/40 font-medium rounded-xl shadow-sm transition-all text-base backdrop-blur-md">
                    Confirm & Append
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
                headers: { 'Content-Type': 'application/json' }
            });
            const result = await res.json();

            if (result.success) {
                // Instantly navigate to print preview in a new tab
                window.open(`print_report.php?visit_id=${result.visit_id}`, '_blank');
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

        window.selectDrug = function (name, dose) {
            dInput.value = name;
            document.getElementById('tDose').value = dose;
            dDrop.classList.add('hidden');
            document.getElementById('tFreq').focus();
        };

        function calcPillCount() {
            const freq = document.getElementById('tFreq').value.trim();
            const days = parseInt(document.getElementById('tDays').value) || 0;
            const qtyBox = document.getElementById('tTotalQty');

            if (!freq && !days) return;

            let dailyPills = 0;
            if (freq) {
                const parts = freq.split(/[\s,+-]+/);
                parts.forEach(p => { const num = parseInt(p); if (!isNaN(num)) dailyPills += num; });
            }

            if (dailyPills > 0 && days > 0) qtyBox.value = dailyPills * days;
            else if (dailyPills > 0 && days === 0) qtyBox.value = dailyPills;
        }

        function addTableDrug() {
            const nameInput = document.getElementById('tDrugName');
            const doseInput = document.getElementById('tDose');
            const freqInput = document.getElementById('tFreq');
            const daysInput = document.getElementById('tDays');
            const qtyInput = document.getElementById('tTotalQty');
            const err = document.getElementById('drugErr');
            err.className = 'text-xs text-red-500 font-bold hidden mb-4 text-center';

            const name = nameInput.value.trim();
            if (!name) return;

            const dose = doseInput.value.trim();
            const freq = freqInput.value.trim();
            const dur = daysInput.value.trim();
            const qty = parseInt(qtyInput.value) || 0;

            const invMatch = inventory.find(d => d.DrugName.toLowerCase() === name.toLowerCase());
            const price = invMatch ? parseFloat(invMatch.UnitPrice) : 0;
            const drugId = invMatch ? invMatch.DrugID : null;
            const stock = invMatch ? parseInt(invMatch.Quantity) : 999999;

            let showedWarning = false;
            if (drugId && qty > stock) {
                err.innerText = `Warning: ${name} only has ${stock} units in stock! Added anyway.`;
                err.className = 'text-xs text-orange-500 font-bold mb-4 text-center';
                showedWarning = true;
            }

            const existing = billDrugs.find(d => d.name.toLowerCase() === name.toLowerCase());
            if (existing && existing.id) {
                if (qty > stock) {
                    err.innerText = `Warning: Adding this exceeds available stock (${stock}) for ${name}! Added anyway.`;
                    err.className = 'text-xs text-orange-500 font-bold mb-4 text-center';
                    showedWarning = true;
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
        window.updateField = function (idx, field, val) {
            billDrugs[idx][field] = val;
            if (field === 'qty') billDrugs[idx].cost = parseInt(val) * billDrugs[idx].unit_price;
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
                if (freq) {
                    const parts = freq.split(/[\s,+-]+/);
                    parts.forEach(p => { const num = parseInt(p); if (!isNaN(num)) dailyPills += num; });
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
                tr.className = 'bg-transparent hover:bg-white/5 dynamic-row transition-all border-b border-white/5';
                const tagCustom = !d.id ? `<span class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] px-1.5 py-0.5 ml-2 rounded-md font-bold uppercase tracking-wider backdrop-blur-sm">Custom</span>` : '';
                tr.innerHTML = `
                    <td class="p-3 font-medium text-white drop-shadow-sm">${d.name} ${tagCustom}<br><span class="text-xs text-slate-400 font-normal mt-1 block">Rs. ${d.unit_price} x ${d.qty} = Rs. ${d.cost.toFixed(2)}</span></td>
                    <td class="p-3"><input type="text" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2 rounded-lg font-medium text-sm focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all" value="${d.dose}" onchange="updateInlineValue(${i}, this, 'dose')"></td>
                    <td class="p-3"><input type="text" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2 rounded-lg font-mono font-medium text-sm focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all" value="${d.frequency}" onchange="updateInlineValue(${i}, this, 'frequency')"></td>
                    <td class="p-3"><input type="text" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-2 rounded-lg font-medium text-sm focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all" value="${d.duration}" onchange="updateInlineValue(${i}, this, 'duration')"></td>
                    <td class="p-3"><input type="number" class="w-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 p-2 rounded-lg font-bold text-center text-sm focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all" value="${d.qty}" onchange="updateInlineValue(${i}, this, 'qty')"></td>
                    <td class="p-3 text-center"><button type="button" onclick="removeDrug(${i})" class="text-rose-500 hover:text-rose-400 hover:bg-rose-500/10 px-3 py-1.5 rounded-lg font-black transition-all text-lg">&times;</button></td>
                `;
                tbody.appendChild(tr);
            });

            const drugTotal = billDrugs.reduce((sum, d) => sum + d.cost, 0);
            document.getElementById('billWithoutFee').innerText = drugTotal.toFixed(2);
            document.getElementById('lblDrugCount').innerText = billDrugs.length;
        }

        function openPrescriptionModal() {
            if (inventory.length === 0) loadInventory();
            document.getElementById('rxModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('tDrugName').focus(), 100);
        }

        function closePrescriptionModal() {
            document.getElementById('rxModal').classList.add('hidden');
        }

        function confirmPrescriptionModal() {
            if (billDrugs.length === 0) return closePrescriptionModal();

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
            if (tat.value && !tat.value.endsWith('\n')) tat.value += "\n";
            tat.value += rxString.trim();
            closePrescriptionModal();
            showToast("Prescription injected into notes!");
        }

    </script>
    <script src="../assets/js/toast.js"></script>
</body>

</html>