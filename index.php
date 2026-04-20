<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] from-slate-900 via-slate-800 to-zinc-900 font-sans text-slate-200">
    <div class="bg-white/5 backdrop-blur-2xl p-10 rounded-[2rem] shadow-2xl border border-white/10 max-w-md w-full text-center relative overflow-hidden">
        <!-- Subtle inner glow effect -->
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <h1 class="text-3xl font-bold text-white mb-2 tracking-tight drop-shadow-md relative z-10">Clinic System</h1>
        <p class="text-slate-400 mb-8 relative z-10">Select your role to continue</p>
        
        <div class="space-y-4 relative z-10">
            <a href="receptionist/dashboard.php" class="block w-full py-4 px-6 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-200 border border-indigo-500/30 hover:border-indigo-400/50 backdrop-blur-md rounded-2xl text-lg font-medium transition-all shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.3)]">
                Receptionist
            </a>
            
            <a href="doctor/dashboard.php" class="block w-full py-4 px-6 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-200 border border-indigo-500/30 hover:border-indigo-400/50 backdrop-blur-md rounded-2xl text-lg font-medium transition-all shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.3)]">
                Doctor
            </a>
            
            <a href="admin/dashboard.php" class="block w-full py-4 px-6 bg-white/5 hover:bg-white/10 text-white border border-white/10 hover:border-white/30 backdrop-blur-md rounded-2xl text-lg font-medium transition-all shadow-sm">
                Admin
            </a>

            <div class="pt-6 mt-6 border-t border-white/10">
                <a href="network_setup.php" class="block w-full py-3 px-6 bg-black/20 hover:bg-black/40 text-slate-300 border border-white/5 rounded-xl text-md font-medium transition-all shadow-inner flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    Link a Receptionist PC
                </a>
                <p class="text-xs text-slate-500 mt-3">Click here to get the network address</p>
            </div>
        </div>
    </div>
</body>
</html>
