<?php
$pageTitle = 'Drug Inventory (Receptionist)';
include '../includes/header.php';
?>
<body class="bg-gray-50 flex h-screen overflow-hidden dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <?php include '../includes/sidebar_receptionist.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <nav class="bg-blue-900 text-white p-4 md:hidden flex justify-between items-center shadow-md">
            <h1 class="text-xl font-bold">Inventory</h1>
            <div>
                <a href="../index.php" class="px-3 py-1 bg-blue-600 hover:bg-blue-800 rounded font-bold text-sm mr-2 transition">Dashboard</a>
                <a href="dashboard.php" class="px-3 py-1 bg-blue-700 hover:bg-blue-800 rounded font-bold text-sm transition">Back</a>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-3xl font-black text-gray-800">Drug Inventory Base</h2>
                    <p class="text-gray-500 font-semibold mt-1">Manage stocks and pricing</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add New Drug -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-xl font-black text-gray-800 mb-4 border-b border-gray-200 pb-2">Add New Drug</h2>
                    <form id="addDrugForm" class="space-y-4">
                        <input type="text" id="dName" placeholder="Drug Name *" required class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-blue-500">
                        <input type="text" id="dDose" placeholder="Dose (e.g., 500mg) *" required class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-blue-500">
                        <input type="text" id="dBatch" placeholder="Batch Number *" required class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-blue-500">
                        <input type="number" id="dQty" placeholder="Initial Quantity *" required class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-blue-500">
                        <input type="number" step="0.01" id="dPrice" placeholder="Unit Price (Rs.) *" required class="w-full border-2 border-gray-200 p-3 rounded-lg font-bold focus:border-blue-500">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg font-bold transition shadow-md">Register Drug</button>
                    </form>
                </div>

                <!-- List / Edit Drugs -->
                <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col min-h-[500px]">
                    <h2 class="text-xl font-black text-gray-800 mb-4">Stock Overview</h2>
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="p-3 font-bold text-gray-500 uppercase text-xs">Name & Dose & Batch</th>
                                    <th class="p-3 font-bold text-gray-500 uppercase text-xs">Stock</th>
                                    <th class="p-3 font-bold text-gray-500 uppercase text-xs">Unit Price</th>
                                    <th class="p-3 font-bold text-gray-500 uppercase text-xs text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryList">
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
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="p-3">
                        <div class="font-black text-gray-800">${d.DrugName} <span class="text-xs text-blue-600 ml-1">(${d.Dose || 'N/A'})</span></div>
                        <div class="text-xs text-gray-500 font-bold uppercase mt-1">Batch: ${d.BatchNumber}</div>
                    </td>
                    <td class="p-3 font-bold ${parseInt(d.Quantity) < 10 ? 'text-red-600' : 'text-green-600'}">${d.Quantity}</td>
                    <td class="p-3 font-black text-gray-600">Rs. ${d.UnitPrice}</td>
                    <td class="p-3 text-right space-x-2">
                        <button onclick="updateStock(${d.DrugID})" class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-bold rounded">Add Stock</button>
                        <button onclick="updatePrice(${d.DrugID})" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-bold rounded">Edit Price</button>
                        <button onclick="deleteDrug(${d.DrugID})" class="px-3 py-1 bg-red-100 text-red-600 text-xs hover:bg-red-200 font-bold rounded">Delete</button>
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

        loadInventory();
    </script>
    <script src="../assets/js/toast.js"></script>
</body>
</html>
