<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['doctor_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->query("SELECT SettingValue FROM Settings WHERE SettingKey = 'doctor_password'");
    $correctPassword = $stmt->fetchColumn();

    if ($password === $correctPassword) {
        $_SESSION['doctor_logged_in'] = true;
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
    <title>Doctor Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm border-t-4 border-teal-600">
        <h2 class="text-2xl font-bold mb-6 text-center text-teal-800">Doctor Login</h2>
        <?php if($error): ?>
            <p class="text-red-500 text-center mb-4 font-bold border border-red-200 bg-red-50 rounded p-2"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <input type="password" name="password" placeholder="Enter Doctor Password" class="w-full border p-3 rounded focus:outline-none focus:ring-2 focus:ring-teal-600" required>
            </div>
            <button type="submit" class="w-full bg-teal-600 text-white p-3 rounded font-bold hover:bg-teal-700 transition">Login</button>
            <a href="../index.php" class="block text-center text-sm text-gray-500 mt-4 hover:underline">Back to Home</a>
        </form>
    </div>
</body>
</html>
