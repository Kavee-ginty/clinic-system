<?php
// includes/sidebar_admin.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-gray-900 dark:bg-black text-white flex-col hidden md:flex shadow-2xl z-10 transition-colors">
    <div class="p-6 border-b border-gray-800 dark:border-gray-900">
        <h1 class="text-2xl font-black tracking-tight text-white">Clinic System</h1>
        <p class="text-sm font-semibold text-teal-400 mt-1 uppercase tracking-widest">Administrator</p>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center gap-3 p-3 rounded-lg font-bold transition <?= $currentPage === 'dashboard.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?>">Dashboard</a>
        <a href="patients.php" class="flex items-center gap-3 p-3 rounded-lg font-bold transition <?= $currentPage === 'patients.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?>">All Patients</a>
        <a href="backup.php" class="flex items-center gap-3 p-3 rounded-lg font-bold transition <?= $currentPage === 'backup.php' ? 'bg-gray-800 text-green-400' : 'text-gray-400 hover:text-green-400 hover:bg-gray-800' ?>">Backup DB</a>
        <a href="import.php" class="flex items-center gap-3 p-3 rounded-lg font-bold transition <?= $currentPage === 'import.php' ? 'bg-gray-800 text-blue-400' : 'text-gray-400 hover:text-blue-400 hover:bg-gray-800' ?>">Import DB</a>
    </nav>
    <div class="p-4 border-t border-gray-800 dark:border-gray-900">
        <button onclick="toggleTheme()" class="w-full flex justify-center items-center gap-2 p-3 text-gray-400 hover:text-yellow-400 bg-gray-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            Toggle Theme
        </button>
        <a href="../index.php" class="block w-full text-center p-3 text-gray-400 hover:text-white bg-gray-800 dark:bg-gray-900 rounded-lg font-bold transition text-sm">Main Menu</a>
        <a href="logout.php" class="block w-full text-center p-3 mt-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition shadow-md">Logout</a>
    </div>
</aside>
