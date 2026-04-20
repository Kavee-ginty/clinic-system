<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->query("SELECT SettingValue FROM Settings WHERE SettingKey = 'admin_password'");
    $correctPassword = $stmt->fetchColumn();

    if ($password === $correctPassword) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Incorrect Password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[conic-gradient(at_bottom_right,_var(--tw-gradient-stops))] from-slate-900 via-slate-800 to-zinc-900 flex items-center justify-center min-h-screen text-slate-200 font-sans">
    <div class="bg-white/5 backdrop-blur-3xl p-8 rounded-[2rem] shadow-2xl border border-white/10 w-full max-w-sm relative">
        <h2 class="text-2xl font-bold mb-6 text-center text-white drop-shadow-md tracking-tight">Admin Login</h2>
        <?php if($error): ?>
            <p class="text-rose-300 bg-rose-500/20 border-rose-500/30 shadow-sm backdrop-blur-sm text-center mb-4 font-bold border rounded-xl p-2 drop-shadow-sm"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <input type="password" name="password" placeholder="Enter Admin Password" class="w-full bg-black/20 border border-white/10 text-white placeholder-slate-500 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/50 font-medium transition-all outline-none shadow-inner backdrop-blur-sm" required>
            </div>
            <button type="submit" class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-200 font-bold p-3.5 rounded-xl transition-all shadow-sm hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] backdrop-blur-md">Login</button>
            <a href="../index.php" class="block text-center text-sm text-slate-400 mt-4 hover:text-white transition-colors">Back to Home</a>
        </form>
    </div>
</body>
</html>
