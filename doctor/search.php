<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Patient - Doctor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-2xl font-bold">Search Patient Database</h1>
        <div>
            <a href="../index.php" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 rounded font-semibold mr-2 transition">Dashboard</a>
            <a href="dashboard.php" class="px-4 py-2 bg-teal-700 hover:bg-teal-800 rounded font-semibold transition">Back</a>
        </div>
    </nav>

    <div class="container mx-auto p-4 max-w-3xl mt-10">
        <div class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Patient Search</h2>
            <input type="text" id="searchInput" class="w-full border-2 border-gray-300 rounded-lg p-4 text-lg focus:outline-none focus:border-teal-500 mb-6" placeholder="Enter name or phone number...">
            
            <div id="searchResults" class="space-y-2">
                <!-- Results go here -->
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', async (e) => {
            const query = e.target.value;
            const resBox = document.getElementById('searchResults');
            if(query.length < 2) { resBox.innerHTML = ''; return; }
            
            const res = await fetch(`../api/search_patient.php?q=${encodeURIComponent(query)}`);
            const patients = await res.json();
            
            resBox.innerHTML = patients.map(p => `
                <div class="p-4 border rounded-lg hover:shadow-md hover:border-teal-300 bg-gray-50 flex justify-between items-center transition">
                    <div class="flex-1 pr-4">
                        <div class="font-black text-lg text-gray-800 flex items-center gap-2">
                            ${p.PatientNumber || 'PT-N/A'} - ${p.FirstName} ${p.LastName}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="font-bold">NIC:</span> ${p.NIC || 'N/A'} | 
                            <span class="font-bold">Age:</span> ${p.Age || 'N/A'} | 
                            <span class="font-bold">Gender:</span> ${p.Gender} | 
                            <span class="font-bold">DOB:</span> ${p.DOB}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="font-bold">Phone:</span> ${p.Phone} | 
                            <span class="font-bold">Address:</span> ${p.Address || 'N/A'} | 
                            <span class="font-bold">Registered:</span> ${p.RegisteredDate}
                        </div>
                    </div>
                    <a href="history.php?patient_id=${p.PatientID}" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded font-bold shadow">View History</a>
                </div>
            `).join('');
        });
    </script>
</body>
</html>
