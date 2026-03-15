<?php
// includes/sidebar_doctor.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-teal-900 dark:bg-black text-white flex-col hidden md:flex shadow-2xl z-10 transition-colors">
    <div class="p-6 border-b border-teal-800 dark:border-gray-900">
        <h1 class="text-2xl font-black tracking-tight text-white">Clinic System</h1>
        <p class="text-sm font-semibold text-teal-300 mt-1 uppercase tracking-widest">Medical Staff</p>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center gap-3 p-3 <?= $currentPage === 'dashboard.php' ? 'bg-teal-800 dark:bg-gray-800 text-white' : 'text-teal-200 hover:text-white hover:bg-teal-800 dark:hover:bg-gray-800' ?> rounded-lg font-bold transition">Live Dashboard</a>
        <a href="search.php" class="flex items-center gap-3 p-3 <?= $currentPage === 'search.php' ? 'bg-teal-800 dark:bg-gray-800 text-white' : 'text-teal-200 hover:text-white hover:bg-teal-800 dark:hover:bg-gray-800' ?> rounded-lg font-bold transition">Search Patient</a>
        <a href="inventory.php" class="flex items-center gap-3 p-3 <?= $currentPage === 'inventory.php' ? 'bg-teal-800 dark:bg-gray-800 text-white' : 'text-teal-200 hover:text-white hover:bg-teal-800 dark:hover:bg-gray-800' ?> rounded-lg font-bold transition">Manage Inventory</a>
        <a href="settings.php" class="flex items-center gap-3 p-3 <?= $currentPage === 'settings.php' ? 'bg-teal-800 dark:bg-gray-800 text-white' : 'text-teal-200 hover:text-white hover:bg-teal-800 dark:hover:bg-gray-800' ?> rounded-lg font-bold transition">Print Settings</a>
    </nav>
    
    <div class="p-4 border-t border-teal-800 dark:border-gray-900 space-y-2">
        <!-- Doctor Security Module -->
        <div class="p-3 bg-teal-950 rounded-lg mb-4 hidden" id="passwordModule">
            <input type="password" id="docPass" class="w-full bg-teal-900 text-white border border-teal-700 p-2 mb-2 rounded font-bold text-sm placeholder-teal-600" placeholder="New Password">
            <button onclick="changeDoctorPassword()" class="w-full bg-teal-700 hover:bg-teal-600 text-white font-bold p-2 rounded text-xs transition">Save Password</button>
        </div>
        <button onclick="document.getElementById('passwordModule').classList.toggle('hidden')" class="block w-full text-center p-3 text-teal-300 hover:text-white bg-teal-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm">Security</button>
        <button onclick="toggleTheme()" class="w-full flex justify-center items-center gap-2 p-3 text-teal-300 hover:text-white bg-teal-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            Theme
        </button>
        
        <a href="../index.php" class="block w-full text-center p-3 text-teal-300 hover:text-white bg-teal-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm">Main Menu</a>
        <a href="logout.php" class="block w-full text-center p-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold transition shadow-md">Logout</a>
    </div>
</aside>
