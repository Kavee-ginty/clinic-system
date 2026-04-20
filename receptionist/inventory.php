<?php
$pageTitle = 'Drug Inventory (Receptionist)';
include '../includes/header.php';
?>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] flex h-screen overflow-hidden from-slate-900 via-slate-800 to-zinc-900 text-slate-200 transition-colors font-sans">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_receptionist.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-white/5 backdrop-blur-xl border-b border-white/10 text-white p-4 shadow-lg flex justify-between items-center z-10 relative md:hidden">
            <h1 class="text-xl font-bold tracking-tight drop-shadow-md">Inventory</h1>
            <div>
                <a href="../index.php" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-200 rounded-lg font-bold text-sm mr-2 transition-all shadow-sm">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1.5 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-300 rounded-lg font-bold text-sm transition-all shadow-sm">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 relative z-0">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight drop-shadow-md">Drug Inventory Base</h2>
                    <p class="text-slate-400 font-semibold mt-1">Manage stocks and pricing</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add New Drug -->
                <div class="bg-white/5 backdrop-blur-3xl p-6 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20">
                    <h2 class="text-xl font-black text-white mb-4 border-b border-white/10 pb-2 drop-shadow-sm">Add New Drug</h2>
                    <form id="addDrugForm" class="space-y-4">
                        <input type="text" id="dName" placeholder="Drug Name *" required class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-3 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 shadow-inner backdrop-blur-sm outline-none transition-all">
                        <input type="text" id="dDose" placeholder="Dose (e.g., 500mg) *" required class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-3 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 shadow-inner backdrop-blur-sm outline-none transition-all">
                        <input type="text" id="dBatch" placeholder="Batch Number *" required class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-3 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 shadow-inner backdrop-blur-sm outline-none transition-all">
                        <input type="number" id="dQty" placeholder="Initial Quantity *" required class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-3 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 shadow-inner backdrop-blur-sm outline-none transition-all">
                        <input type="number" step="0.01" id="dPrice" placeholder="Unit Price (Rs.) *" required class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 p-3 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 shadow-inner backdrop-blur-sm outline-none transition-all">
                        <button type="submit" class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-200 p-3.5 rounded-xl font-bold transition-all shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] backdrop-blur-md">Register Drug</button>
                    </form>
                </div>

                <!-- List / Edit Drugs -->
                <div class="md:col-span-2 bg-white/5 backdrop-blur-3xl p-6 rounded-[2rem] shadow-2xl border border-white/10 border-t-white/20 flex flex-col min-h-[500px]">
                    <h2 class="text-xl font-black text-white mb-4 drop-shadow-sm">Stock Overview</h2>
                    <div class="flex-1 overflow-y-auto custom-scrollbar rounded-xl border border-white/10 bg-black/10 backdrop-blur-sm">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-white/5 sticky top-0 backdrop-blur-md z-10 border-b border-white/10">
                                <tr>
                                    <th class="p-4 font-bold text-slate-300 uppercase text-xs tracking-wider">Name & Dose & Batch</th>
                                    <th class="p-4 font-bold text-slate-300 uppercase text-xs tracking-wider mt-1">Stock</th>
                                    <th class="p-4 font-bold text-slate-300 uppercase text-xs tracking-wider">Unit Price</th>
                                    <th class="p-4 font-bold text-slate-300 uppercase text-xs text-right tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryList" class="divide-y divide-white/5">
                                <!-- Loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        async function loadInventory() {
            const res = await fetch('../api/inventory.php');
            const data = await res.json();
            const list = document.getElementById('inventoryList');
            
            list.innerHTML = data.map(d => `
                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                    <td class="p-4">
                        <div class="font-black text-white drop-shadow-sm">${d.DrugName} <span class="text-xs text-indigo-400 drop-shadow ml-1">(${d.Dose || 'N/A'})</span></div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Batch: ${d.BatchNumber}</div>
                    </td>
                    <td class="p-4 font-black text-lg ${parseInt(d.Quantity) < 10 ? 'text-rose-400 drop-shadow-sm' : 'text-emerald-400 drop-shadow-sm'}">${d.Quantity}</td>
                    <td class="p-4 font-bold text-slate-300 tracking-tight">Rs. ${d.UnitPrice}</td>
                    <td class="p-4 text-right space-x-1 outline-none">
                        <button onclick="updateStock(${d.DrugID})" class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/30 text-emerald-300 text-xs font-bold rounded-lg transition-all shadow-sm backdrop-blur-sm">Add Stock</button>
                        <button onclick="updatePrice(${d.DrugID})" class="px-3 py-1.5 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-300 text-xs font-bold rounded-lg transition-all shadow-sm backdrop-blur-sm">Edit Price</button>
                        <button onclick="editDrugDetails(${d.DrugID}, '${(d.DrugName||'').replace(/'/g,`\\'`).replace(/\"/g,`&quot;`)}', '${(d.Dose||'').replace(/'/g,`\\'`).replace(/\"/g,`&quot;`)}', '${(d.BatchNumber||'').replace(/'/g,`\\'`).replace(/\"/g,`&quot;`)}')" class="px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-300 text-xs font-bold rounded-lg transition-all shadow-sm backdrop-blur-sm">Edit Details</button>
                        <button onclick="deleteDrug(${d.DrugID})" class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 border border-rose-500/30 text-rose-300 text-xs font-bold rounded-lg transition-all shadow-sm backdrop-blur-sm">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        document.getElementById('addDrugForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                action: 'add',
                drug_name: document.getElementById('dName').value,
                dose: document.getElementById('dDose').value,
                batch_number: document.getElementById('dBatch').value,
                quantity: document.getElementById('dQty').value,
                unit_price: document.getElementById('dPrice').value
            };
            const res = await fetch('../api/inventory.php', { method: 'POST', body: JSON.stringify(payload) });
            await res.json();
            document.getElementById('addDrugForm').reset();
            loadInventory();
        });

        async function updateStock(id) {
            const qty = prompt("Enter amount of units received to add to existing stock:");
            if(!qty || isNaN(qty)) return;
            await fetch('../api/inventory.php', { method: 'POST', body: JSON.stringify({action: 'update_stock', drug_id: id, add_quantity: qty}) });
            loadInventory();
        }

        async function updatePrice(id) {
            const price = prompt("Enter new unit price (Rs.):");
            if(!price || isNaN(price)) return;
            await fetch('../api/inventory.php', { method: 'POST', body: JSON.stringify({action: 'update_price', drug_id: id, price: price}) });
            loadInventory();
        }

        async function deleteDrug(id) {
            if(!confirm("Are you sure you want to permanently delete this drug from inventory?")) return;
            const res = await fetch('../api/inventory.php', { method: 'POST', body: JSON.stringify({action: 'delete', drug_id: id}) });
            const data = await res.json();
            if(data.success) {
                showToast("Drug successfully deleted.");
                loadInventory();
            } else {
                showToast(data.error || "Cannot delete. May be tied to existing patient records.", "error");
            }
        }

        async function editDrugDetails(id, name, dose, batch) {
            const newName = prompt("Edit Drug Name:", name);
            if(newName === null) return;
            const newDose = prompt("Edit Dose:", dose);
            if(newDose === null) return;
            const newBatch = prompt("Edit Batch:", batch);
            if(newBatch === null) return;

            const payload = { action: 'edit_details', drug_id: id, drug_name: newName, dose: newDose, batch: newBatch };
            await fetch('../api/inventory.php', { method: 'POST', body: JSON.stringify(payload) });
            loadInventory();
        }

        loadInventory();
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
