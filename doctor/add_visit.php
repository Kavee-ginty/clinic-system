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
        <a href="dashboard.php" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold">Back to
            Dashboard</a>
    </nav>

    <div class="container mx-auto p-4 max-w-4xl mt-6">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-teal-500">

            <div id="patientInfo" class="mb-6 p-4 bg-gray-100 rounded-lg flex justify-between items-center">
                <!-- Loaded via JS -->
            </div>

            <form id="visitForm" class="space-y-6">
                <!-- Group 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block font-bold text-gray-700 mb-2">Presenting Complaint <span
                                class="text-red-500">*</span></label>
                        <textarea id="complaint" rows="3"
                            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"
                            required autocomplete="off"></textarea>
                        <div id="complaintDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100"></div>
                    </div>
                    <div class="relative">
                        <label class="block font-bold text-gray-700 mb-2">Examination Findings</label>
                        <textarea id="examination" rows="3"
                            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" autocomplete="off"></textarea>
                        <div id="examinationDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100"></div>
                    </div>
                </div>

                <!-- Group 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block font-bold text-gray-700 mb-2">Investigations</label>
                        <textarea id="investigation" rows="2"
                            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" autocomplete="off"></textarea>
                        <div id="investigationDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100"></div>
                    </div>
                    <div class="relative">
                        <label class="block font-bold text-gray-700 mb-2">Diagnosis <span
                                class="text-red-500">*</span></label>
                        <textarea id="diagnosis" rows="2"
                            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"
                            required autocomplete="off"></textarea>
                        <div id="diagnosisDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100">
                        </div>
                    </div>
                </div>

                <!-- Group 3 -->
                <div>
                    <div class="flex justify-between items-center mb-2 flex-wrap gap-2">
                        <label class="block font-bold text-gray-700">Treatment / Prescription <span
                                class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <button type="button" onclick="saveCurrentAsTemplate()"
                                class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded font-bold text-xs flex items-center gap-1 shadow-sm transition">
                                <span>💾</span> Save Template
                            </button>
                            <button type="button" onclick="openTemplatesModal()"
                                class="px-3 py-1.5 bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200 rounded font-bold text-xs flex items-center gap-1 shadow-sm transition">
                                <span>⚙️</span> Templates
                            </button>
                            <button type="button" onclick="openPrescriptionModal()"
                                class="px-4 py-1.5 bg-teal-100 text-teal-800 hover:bg-teal-200 border border-teal-300 rounded font-bold text-sm flex items-center gap-2 shadow-sm transition">
                                <span class="text-lg leading-none">+</span> Open Prescription Table
                            </button>
                        </div>
                    </div>
                    <textarea id="treatment" rows="4"
                        class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500"
                        required></textarea>
                </div>

                <!-- Group 4 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block font-bold text-gray-700 mb-2">Referrals</label>
                        <textarea id="referals" rows="2"
                            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" autocomplete="off"></textarea>
                        <div id="referalsDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100"></div>
                    </div>
                    <div class="relative">
                        <label class="block font-bold text-gray-700 mb-2">Doctor's Notes</label>
                        <textarea id="notes" rows="2"
                            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal-500" autocomplete="off"></textarea>
                        <div id="notesDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100"></div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm font-bold text-gray-500">
                        Total Table Drugs: <span id="lblDrugCount"
                            class="text-teal-600 outline outline-1 outline-teal-300 px-2 py-0.5 rounded">0</span>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" id="btnCompletePreview"
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg shadow-md transition text-lg">
                            Complete & Preview
                        </button>
                        <button type="submit" id="btnCompletePrint"
                            class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg shadow-md transition text-lg flex items-center gap-2">
                            <span>🖨️</span> Save & Direct Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- PRESCRIPTION MODAL OVERLAY -->
    <div id="rxModal"
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">

            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-2xl font-black text-gray-800">Add Drugs to Prescription</h2>
                <button onclick="closePrescriptionModal()"
                    class="text-gray-400 hover:text-red-500 font-bold p-2 text-2xl leading-none">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-white">

                <!-- Table UI -->
                <div class="w-full mb-4">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 font-bold text-gray-700 w-1/4">Drug Name</th>
                                <th class="p-3 font-bold text-gray-700 w-1/6">Dose</th>
                                <th class="p-3 font-bold text-gray-700">Dosage Grid (M-A-E-N)</th>
                                <th class="p-3 font-bold text-gray-700 w-24">Duration</th>
                                <th class="p-3 font-bold text-gray-700 w-24 text-center">Total Qty</th>
                                <th class="p-3 font-bold text-gray-700 w-20 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="billTableBody">
                            <!-- Input Row -->
                            <tr class="bg-blue-50/50 border-b-2 border-blue-100">
                                <td class="p-2 relative">
                                    <input type="text" id="tDrugName"
                                        class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm"
                                        placeholder="Type drug name...">
                                    <div id="drugDropdown"
                                        class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-48 overflow-y-auto z-[60] divide-y divide-gray-100">
                                    </div>
                                </td>
                                <td class="p-2"><input type="text" id="tDose"
                                        class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm"
                                        placeholder="Optional"></td>
                                <td class="p-2"><input type="text" id="tFreq"
                                        class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm"
                                        placeholder="e.g. 1-0-1-0" oninput="calcPillCount()"></td>
                                <td class="p-2"><input type="number" id="tDays"
                                        class="w-full border-2 border-gray-200 p-2 rounded font-bold focus:border-teal-500 text-sm"
                                        placeholder="Days" min="1" oninput="calcPillCount()"></td>
                                <td class="p-2"><input type="number" id="tTotalQty"
                                        class="w-full border-2 border-teal-500 bg-teal-50 p-2 rounded font-black focus:outline-none text-sm text-center"
                                        placeholder="Qty"></td>
                                <td class="p-2 text-center"><button type="button" onclick="addTableDrug()"
                                        class="bg-teal-600 hover:bg-teal-700 text-white font-bold p-2 text-sm rounded shadow-md w-full">+</button>
                                </td>
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
                <button type="button" onclick="confirmPrescriptionModal()"
                    class="px-8 py-3 bg-gray-900 hover:bg-black text-white font-black rounded-lg shadow-lg transition text-lg">
                    Confirm & Append to Notes
                </button>
            </div>

        </div>
    </div>

    <!-- Templates Modal -->
    <div id="templatesModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 dark:text-white rounded-2xl shadow-xl w-full max-w-lg p-6 m-4 relative border border-gray-100 dark:border-gray-700">
            <button onclick="closeTemplatesModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-650 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-black text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <span>📋</span> Treatment Templates
            </h3>
            <div id="templatesList" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                <!-- Populated via JS -->
            </div>
            <div class="pt-4 border-t dark:border-gray-700 mt-4 flex justify-end">
                <button onclick="closeTemplatesModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold px-4 py-2 rounded-lg transition text-sm">Close</button>
            </div>
        </div>
    </div>

    <!-- Template Confirm Modal -->
    <div id="templateConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-850 dark:text-white rounded-2xl shadow-xl w-full max-w-md p-6 m-4 relative border border-gray-100 dark:border-gray-750">
            <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 mb-2 flex items-center gap-2">
                <span>💡</span> Pre-saved Treatment Found
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                Pre saved Treatment for <strong id="templateMatchName" class="text-teal-700 dark:text-teal-400"></strong> saved, do u want to apply?
            </p>
            <div class="flex justify-end gap-3">
                <button id="btnCancelTemplate" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold rounded-lg transition text-sm">No</button>
                <button id="btnApplyTemplate" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg transition text-sm">Yes, Apply</button>
            </div>
        </div>
    </div>

    <script>
        const patientId = <?= json_encode($patientId) ?>;
        const queueId = <?= json_encode($queueId) ?>;

        // ----------------------------------------------------
        // Generic Field Autocomplete
        // ----------------------------------------------------
        function setupAutocomplete(inputId, dropdownId, dbField) {
            const input = document.getElementById(inputId);
            const drop = document.getElementById(dropdownId);
            if (!input || !drop) return;

            let historyData = [];
            let selectedIndex = -1;
            let loaded = false;

            async function loadHistory() {
                if (loaded) return;
                try {
                    const res = await fetch(`../api/get_field_history.php?field=${dbField}`);
                    const data = await res.json();
                    if (data.success) {
                        historyData = data.items;
                        loaded = true;
                    }
                } catch (e) {
                    console.error(`Failed to load history for ${dbField}`, e);
                }
            }

            input.addEventListener('focus', async () => {
                await loadHistory();
                input.dispatchEvent(new Event('input'));
            });

            input.addEventListener('input', (e) => {
                const val = e.target.value.toLowerCase();
                drop.innerHTML = '';
                selectedIndex = -1;

                if (!val) {
                    drop.classList.add('hidden');
                    return;
                }

                const matches = historyData.filter(d => d.toLowerCase().includes(val));

                if (matches.length > 0) {
                    let dropdownHTML = `<div class="p-2 bg-teal-100 text-teal-800 font-black text-xs uppercase tracking-wider sticky top-0">📋 Suggestions</div>`;
                    dropdownHTML += matches.map((d, idx) => `
                        <div class="p-3 cursor-pointer hover:bg-teal-50 transition flex justify-between items-center group autocomplete-item" data-val="${d.replace(/"/g, '&quot;')}">
                            <div class="font-bold text-gray-700 group-hover:text-teal-700 text-sm">${d}</div>
                        </div>
                    `).join('');
                    drop.innerHTML = dropdownHTML;
                    drop.classList.remove('hidden');

                    drop.querySelectorAll('.autocomplete-item').forEach(item => {
                        item.addEventListener('click', () => {
                            input.value = item.getAttribute('data-val');
                            drop.classList.add('hidden');
                            input.dispatchEvent(new Event('blur')); // Trigger blur (useful for template auto-check on diagnosis)
                        });
                    });
                } else {
                    drop.classList.add('hidden');
                }
            });

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !drop.contains(e.target)) {
                    drop.classList.add('hidden');
                }
            });

            input.addEventListener('keydown', (e) => {
                const items = drop.querySelectorAll('.autocomplete-item');
                if (items.length === 0 || drop.classList.contains('hidden')) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % items.length;
                    highlightItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    highlightItem(items);
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && selectedIndex < items.length) {
                        e.preventDefault();
                        items[selectedIndex].click();
                        selectedIndex = -1;
                    }
                } else if (e.key === 'Escape') {
                    drop.classList.add('hidden');
                    selectedIndex = -1;
                }
            });

            function highlightItem(items) {
                items.forEach((item, idx) => {
                    if (idx === selectedIndex) {
                        item.classList.add('bg-teal-150', 'text-teal-900', 'font-black');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('bg-teal-150', 'text-teal-900', 'font-black');
                    }
                });
            }
        }

        setupAutocomplete('complaint', 'complaintDropdown', 'Complaint');
        setupAutocomplete('examination', 'examinationDropdown', 'Examination');
        setupAutocomplete('investigation', 'investigationDropdown', 'Investigation');
        setupAutocomplete('diagnosis', 'diagnosisDropdown', 'Diagnosis');
        setupAutocomplete('referals', 'referalsDropdown', 'Referals');
        setupAutocomplete('notes', 'notesDropdown', 'Notes');

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
        document.getElementById('complaint').focus();

        // Track action button clicked
        let submitAction = 'preview';
        document.getElementById('btnCompletePreview')?.addEventListener('click', () => { submitAction = 'preview'; });
        document.getElementById('btnCompletePrint')?.addEventListener('click', () => { submitAction = 'print'; });

        // Submit Form
        document.getElementById('visitForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const action = submitAction;
            
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
                if (action === 'print') {
                    // Direct print via iframe
                    let iframe = document.getElementById('printIframe');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = 'printIframe';
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                    }
                    iframe.src = `print_report.php?visit_id=${result.visit_id}`;
                    iframe.onload = function() {
                        setTimeout(() => {
                            try {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                            } catch(err) {
                                console.error('Failed to trigger print: ', err);
                            }
                            // Redirect back to dashboard after a delay
                            setTimeout(() => {
                                window.location.href = 'dashboard.php';
                            }, 1000);
                        }, 500);
                    };
                } else {
                    // Instantly navigate to print preview in a new tab
                    window.open(`print_report.php?visit_id=${result.visit_id}`, '_blank');
                    window.location.href = 'dashboard.php';
                }
            } else {
                showToast("Error saving record: " + result.error, "error");
            }
        });

        // ----------------------------------------------------
        // Prescription Modal Logic
        // ----------------------------------------------------
        let inventory = [];
        let billDrugs = [];
        let patientHistoryDrugs = [];

        async function loadInventory() {
            const res = await fetch('../api/inventory.php');
            inventory = await res.json();
        }

        async function loadPatientHistoryDrugs() {
            const res = await fetch(`../api/get_visit_drugs.php?patient_id=${patientId}`);
            const data = await res.json();
            if (data.success) {
                patientHistoryDrugs = data.drugs;
            }
        }

        const dInput = document.getElementById('tDrugName');
        const dDrop = document.getElementById('drugDropdown');

        dInput.addEventListener('focus', async () => {
            if (inventory.length === 0) loadInventory();
            if (patientHistoryDrugs.length === 0) await loadPatientHistoryDrugs();
            dInput.dispatchEvent(new Event('input')); // trigger dropdown
        });

        dInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            dDrop.innerHTML = '';
            dropdownSelectedIndex = -1;

            // Filter history matching term
            const historyMatches = val 
                ? patientHistoryDrugs.filter(d => (d.DrugName || '').toLowerCase().includes(val))
                : patientHistoryDrugs;

            // Filter inventory matching term
            const inventoryMatches = val 
                ? inventory.filter(d => (d.DrugName || '').toLowerCase().includes(val))
                : inventory;

            let dropdownHTML = '';

            if (historyMatches.length > 0) {
                dropdownHTML += `<div class="p-2 bg-teal-100 text-teal-800 font-black text-xs uppercase tracking-wider sticky top-0">📋 From Patient History</div>`;
                dropdownHTML += historyMatches.map(d => `
                    <div class="p-3 cursor-pointer hover:bg-teal-50 transition flex justify-between items-center group" 
                         onclick="selectDrugFromHistory('${(d.DrugName || '').replace(/'/g, "\\'")}', '${d.Dose || ''}', '${d.Frequency || ''}', '${d.Duration || ''}', '${d.Quantity || ''}')">
                        <div class="font-bold text-gray-700 group-hover:text-teal-700 text-sm">
                            ${d.DrugName} <span class="text-xs text-teal-650 font-normal ml-1">${d.Dose ? `[${d.Dose}]` : ''} (${d.Frequency} &bull; ${d.Duration})</span>
                        </div>
                        <div class="text-[10px] font-bold text-teal-500">History</div>
                    </div>
                `).join('');
            }

            if (inventoryMatches.length > 0) {
                dropdownHTML += `<div class="p-2 bg-gray-100 text-gray-700 font-black text-xs uppercase tracking-wider sticky top-0">📦 Clinic Inventory</div>`;
                dropdownHTML += inventoryMatches.map(d => `
                    <div class="p-3 cursor-pointer hover:bg-teal-50 transition flex justify-between items-center group" 
                         onclick="selectDrug('${(d.DrugName || '').replace(/'/g, "\\'")}', '${d.Dose || ''}')">
                        <div class="font-bold text-gray-700 group-hover:text-teal-700 text-sm">
                            ${d.DrugName} <span class="text-xs text-gray-400 font-normal ml-1">${d.Dose ? `[${d.Dose}]` : ''}</span>
                        </div>
                        <div class="text-[10px] font-bold ${d.Quantity < 10 ? 'text-red-500' : 'text-gray-400'}">Stock: ${d.Quantity}</div>
                    </div>
                `).join('');
            }

            if (dropdownHTML) {
                dDrop.innerHTML = dropdownHTML;
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

        window.selectDrugFromHistory = function (name, dose, frequency, duration, qty) {
            dInput.value = name;
            document.getElementById('tDose').value = dose;
            document.getElementById('tFreq').value = frequency;
            document.getElementById('tDays').value = duration.replace(/\s*days?/i, '').trim();
            document.getElementById('tTotalQty').value = qty;
            dDrop.classList.add('hidden');
            document.getElementById('tTotalQty').focus();
        };

        // Keydown handling for dropdown selection
        let dropdownSelectedIndex = -1;
        dInput.addEventListener('keydown', (e) => {
            const items = dDrop.querySelectorAll('div[onclick]');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                dDrop.classList.remove('hidden');
                dropdownSelectedIndex = (dropdownSelectedIndex + 1) % items.length;
                highlightDropdownItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                dropdownSelectedIndex = (dropdownSelectedIndex - 1 + items.length) % items.length;
                highlightDropdownItem(items);
            } else if (e.key === 'Enter') {
                if (dropdownSelectedIndex >= 0 && dropdownSelectedIndex < items.length) {
                    e.preventDefault();
                    items[dropdownSelectedIndex].click();
                    dropdownSelectedIndex = -1;
                }
            } else if (e.key === 'Escape') {
                dDrop.classList.add('hidden');
                dropdownSelectedIndex = -1;
            }
        });

        function highlightDropdownItem(items) {
            items.forEach((item, idx) => {
                if (idx === dropdownSelectedIndex) {
                    item.classList.add('bg-teal-150', 'text-teal-900', 'font-black');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('bg-teal-150', 'text-teal-900', 'font-black');
                }
            });
        }

        // Enter submits in drug row
        ['tDrugName', 'tDose', 'tFreq', 'tDays', 'tTotalQty'].forEach(id => {
            document.getElementById(id).addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    if (id === 'tDrugName' && !dDrop.classList.contains('hidden') && dropdownSelectedIndex >= 0) {
                        return; // Let dropdown keydown handle it
                    }
                    e.preventDefault();
                    addTableDrug();
                }
            });
        });

        // Frequency format helper (e.g. 4 -> 0-0-0-4)
        function formatFrequency(val) {
            let cleanVal = val.trim();
            if (!cleanVal) return '';
            const parts = cleanVal.split(/[\s,+-]+/);
            if (parts.length === 0 || (parts.length === 1 && parts[0] === '')) return '';
            
            const maxParts = 4;
            const padded = Array(maxParts).fill('0');
            
            let fillIdx = maxParts - 1;
            for (let i = parts.length - 1; i >= 0; i--) {
                if (fillIdx >= 0) {
                    padded[fillIdx] = parts[i] || '0';
                    fillIdx--;
                }
            }
            return padded.join('-');
        }

        document.getElementById('tFreq')?.addEventListener('blur', (e) => {
            const formatted = formatFrequency(e.target.value);
            if (formatted) {
                e.target.value = formatted;
                calcPillCount();
            }
        });

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
            if (inventory.length === 0) loadInventory();
            if (patientHistoryDrugs.length === 0) loadPatientHistoryDrugs();
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

        // ----------------------------------------------------
        // Diagnosis Treatment Template System
        // ----------------------------------------------------
        let pendingTemplate = null;

        document.getElementById('diagnosis').addEventListener('blur', (e) => {
            const diagVal = e.target.value.trim().toLowerCase();
            if (!diagVal) return;
            
            const templates = JSON.parse(localStorage.getItem('clinic_treatment_templates') || '[]');
            const match = templates.find(t => t.name.toLowerCase() === diagVal || t.diagnosis.toLowerCase() === diagVal);
            
            if (match) {
                pendingTemplate = match;
                document.getElementById('templateMatchName').innerText = match.name;
                document.getElementById('templateConfirmModal').classList.remove('hidden');
            }
        });

        document.getElementById('btnApplyTemplate')?.addEventListener('click', () => {
            if (pendingTemplate) {
                applyTemplate(pendingTemplate);
            }
            document.getElementById('templateConfirmModal').classList.add('hidden');
            pendingTemplate = null;
        });

        document.getElementById('btnCancelTemplate')?.addEventListener('click', () => {
            document.getElementById('templateConfirmModal').classList.add('hidden');
            pendingTemplate = null;
        });

        window.saveCurrentAsTemplate = function() {
            const diag = document.getElementById('diagnosis').value.trim();
            const name = prompt("Enter a name/diagnosis for this template:", diag || "Dengue");
            if (!name) return;
            
            const treatment = document.getElementById('treatment').value;
            const notes = document.getElementById('notes').value;
            
            const templates = JSON.parse(localStorage.getItem('clinic_treatment_templates') || '[]');
            const existingIdx = templates.findIndex(t => t.name.toLowerCase() === name.toLowerCase());
            
            const newTemplate = {
                name: name,
                diagnosis: name,
                treatment: treatment,
                drugs: billDrugs,
                notes: notes
            };
            
            if (existingIdx >= 0) {
                if (confirm(`Template for "${name}" already exists. Overwrite?`)) {
                    templates[existingIdx] = newTemplate;
                } else {
                    return;
                }
            } else {
                templates.push(newTemplate);
            }
            
            localStorage.setItem('clinic_treatment_templates', JSON.stringify(templates));
            showToast(`Template "${name}" saved successfully!`);
        };

        window.openTemplatesModal = function() {
            const modal = document.getElementById('templatesModal');
            const list = document.getElementById('templatesList');
            modal.classList.remove('hidden');
            
            const templates = JSON.parse(localStorage.getItem('clinic_treatment_templates') || '[]');
            if (templates.length === 0) {
                list.innerHTML = `<p class="text-gray-500 italic text-center py-6">No saved templates yet. Fill a prescription and click "Save Template" to create one.</p>`;
                return;
            }
            
            list.innerHTML = templates.map((t, idx) => `
                <div class="p-3 border border-gray-150 rounded-xl flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition mb-2 text-sm text-gray-800 dark:text-gray-200">
                    <div>
                        <div class="font-bold">${t.name}</div>
                        <div class="text-[10px] text-gray-500 font-semibold mt-1">
                            Drugs: ${t.drugs && t.drugs.length > 0 ? t.drugs.map(d => d.name).join(', ') : 'None'}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="applyTemplateByIndex(${idx})" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold px-2.5 py-1.5 rounded transition">Apply</button>
                        <button onclick="deleteTemplateByIndex(${idx})" class="bg-red-500 hover:bg-red-650 text-white text-xs font-bold px-2.5 py-1.5 rounded transition">Delete</button>
                    </div>
                </div>
            `).join('');
        };

        window.closeTemplatesModal = function() {
            document.getElementById('templatesModal').classList.add('hidden');
        };

        window.applyTemplateByIndex = function(idx) {
            const templates = JSON.parse(localStorage.getItem('clinic_treatment_templates') || '[]');
            if (templates[idx]) {
                applyTemplate(templates[idx]);
                closeTemplatesModal();
            }
        };

        window.deleteTemplateByIndex = function(idx) {
            const templates = JSON.parse(localStorage.getItem('clinic_treatment_templates') || '[]');
            if (confirm(`Are you sure you want to delete template "${templates[idx].name}"?`)) {
                templates.splice(idx, 1);
                localStorage.setItem('clinic_treatment_templates', JSON.stringify(templates));
                openTemplatesModal();
            }
        };

        function applyTemplate(t) {
            document.getElementById('treatment').value = t.treatment || '';
            document.getElementById('notes').value = t.notes || '';
            if (t.drugs && t.drugs.length > 0) {
                billDrugs = [...t.drugs];
                renderBill();
            }
            showToast(`Template "${t.name}" applied!`);
        }

    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>