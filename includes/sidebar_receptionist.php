<?php
// includes/sidebar_receptionist.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-blue-900 dark:bg-black text-white flex-col hidden md:flex shadow-2xl z-10 transition-colors">
    <div class="p-6 border-b border-blue-800 dark:border-gray-900">
        <h1 class="text-2xl font-black tracking-tight text-white">Clinic System</h1>
        <p class="text-sm font-semibold text-blue-300 mt-1 uppercase tracking-widest">Front Desk</p>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center gap-3 p-3 rounded-lg font-bold transition <?= $currentPage === 'dashboard.php' ? 'bg-blue-800 dark:bg-gray-800 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-800 dark:hover:bg-gray-800' ?>">Live Register</a>
        <a href="inventory.php" class="flex items-center gap-3 p-3 rounded-lg font-bold transition <?= $currentPage === 'inventory.php' ? 'bg-blue-800 dark:bg-gray-800 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-800 dark:hover:bg-gray-800' ?>">Drug Inventory</a>
    </nav>
    <div class="p-4 border-t border-blue-800 dark:border-gray-900 space-y-2">
        <button onclick="toggleTheme()" class="w-full flex justify-center items-center gap-2 p-3 text-blue-300 hover:text-white bg-blue-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            Toggle Theme
        </button>
        <a href="../index.php" class="block w-full text-center p-3 text-blue-300 hover:text-white bg-blue-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm">Home Menu</a>
    </div>
</aside>
