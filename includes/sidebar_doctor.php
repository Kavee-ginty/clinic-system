<?php
// includes/sidebar_doctor.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-white/5 backdrop-blur-2xl border-r border-white/10 text-white flex-col hidden md:flex shadow-2xl z-10 transition-colors relative">
    <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>
    <div class="p-6 border-b border-white/10 relative z-10">
        <h1 class="text-2xl font-bold tracking-tight text-white drop-shadow-md">Clinic System</h1>
        <p class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">Medical Staff</p>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto relative z-10">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 <?= $currentPage === 'dashboard.php' ? 'bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5' ?> rounded-xl font-medium transition-all">Live Dashboard</a>
        <a href="search.php" class="flex items-center gap-3 px-4 py-3 <?= $currentPage === 'search.php' ? 'bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5' ?> rounded-xl font-medium transition-all">Search Patient</a>
        <a href="inventory.php" class="flex items-center gap-3 px-4 py-3 <?= $currentPage === 'inventory.php' ? 'bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5' ?> rounded-xl font-medium transition-all">Manage Inventory</a>
        <a href="settings.php" class="flex items-center gap-3 px-4 py-3 <?= $currentPage === 'settings.php' ? 'bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5' ?> rounded-xl font-medium transition-all">Print Settings</a>
    </nav>
    
    <div class="p-4 border-t border-white/10 space-y-3 relative z-10">
        <!-- Doctor Security Module -->
        <div class="p-4 bg-black/20 rounded-xl mb-4 hidden border border-white/5 shadow-inner backdrop-blur-sm" id="passwordModule">
            <input type="password" id="docPass" class="w-full bg-black/40 text-slate-200 border border-white/10 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 p-2.5 mb-3 rounded-lg font-medium text-sm placeholder-slate-500 outline-none transition-all" placeholder="New Password">
            <button onclick="changeDoctorPassword()" class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-200 font-medium py-2 px-4 rounded-lg text-sm transition-all shadow-sm">Save Password</button>
        </div>
        <button onclick="document.getElementById('passwordModule').classList.toggle('hidden')" class="block w-full text-center py-2.5 px-4 text-slate-300 hover:text-white border border-transparent hover:border-white/10 hover:bg-white/5 rounded-lg font-medium transition-all text-sm">Security</button>
        <button onclick="toggleTheme()" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 text-slate-300 hover:text-white border border-transparent hover:border-white/10 hover:bg-white/5 rounded-lg font-medium transition-all text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            Theme
        </button>
        
        <a href="../index.php" class="block w-full text-center py-2.5 px-4 text-slate-300 hover:text-white border border-transparent hover:border-white/10 hover:bg-white/5 rounded-lg font-medium transition-all text-sm">Main Menu</a>
        <a href="logout.php" class="block w-full text-center py-2.5 px-4 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 hover:border-rose-500/40 text-rose-300 hover:text-rose-200 rounded-lg font-medium transition-all shadow-[0_0_10px_rgba(244,63,94,0.1)] hover:shadow-[0_0_15px_rgba(244,63,94,0.3)]">Logout</a>
    </div>
</aside>
