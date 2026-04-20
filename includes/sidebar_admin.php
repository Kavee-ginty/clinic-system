<?php
// includes/sidebar_admin.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-white/5 backdrop-blur-2xl border-r border-white/10 text-white flex-col hidden md:flex shadow-2xl z-10 transition-colors relative">
    <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>
    <div class="p-6 border-b border-white/10 relative z-10">
        <h1 class="text-2xl font-bold tracking-tight text-white drop-shadow-md">Clinic System</h1>
        <p class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">Administrator</p>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto relative z-10">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $currentPage === 'dashboard.php' ? 'bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">Dashboard</a>
        <a href="patients.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $currentPage === 'patients.php' ? 'bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">All Patients</a>
        <a href="backup.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $currentPage === 'backup.php' ? 'bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 shadow-[0_0_15px_rgba(16,185,129,0.2)]' : 'border border-transparent text-slate-400 hover:text-emerald-300 hover:bg-white/5' ?>">Backup DB</a>
        <a href="import.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $currentPage === 'import.php' ? 'bg-amber-500/20 border border-amber-500/30 text-amber-300 shadow-[0_0_15px_rgba(245,158,11,0.2)]' : 'border border-transparent text-slate-400 hover:text-amber-300 hover:bg-white/5' ?>">Import DB</a>
    </nav>
    <div class="p-4 border-t border-white/10 space-y-3 relative z-10">
        <button onclick="toggleTheme()" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 text-slate-300 hover:text-white border border-transparent hover:border-white/10 hover:bg-white/5 rounded-lg font-medium transition-all text-sm mb-2 hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            Toggle Theme
        </button>
        <a href="../index.php" class="block w-full text-center py-2.5 px-4 text-slate-300 hover:text-white border border-transparent hover:border-white/10 hover:bg-white/5 rounded-lg font-medium transition-all text-sm">Main Menu</a>
        <a href="logout.php" class="block w-full text-center py-2.5 px-4 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 hover:border-rose-500/40 text-rose-300 hover:text-rose-200 rounded-lg font-medium transition-all shadow-[0_0_10px_rgba(244,63,94,0.1)] hover:shadow-[0_0_15px_rgba(244,63,94,0.3)]">Logout</a>
    </div>
</aside>
