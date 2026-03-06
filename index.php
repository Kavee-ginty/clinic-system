<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f0fdfa; } /* Light teal background */
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-xl shadow-xl max-w-md w-full text-center">
        <h1 class="text-3xl font-bold text-teal-700 mb-2">Clinic System</h1>
        <p class="text-gray-500 mb-8">Select your role to continue</p>
        
        <div class="space-y-4">
            <a href="receptionist/dashboard.php" class="block w-full py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-lg font-semibold transition shadow-md">
                Receptionist
            </a>
            
            <a href="doctor/dashboard.php" class="block w-full py-4 px-6 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-lg font-semibold transition shadow-md">
                Doctor
            </a>
            
            <a href="admin/dashboard.php" class="block w-full py-4 px-6 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-lg font-semibold transition shadow-md">
                Admin
            </a>

            <div class="pt-6 mt-4 border-t border-gray-200">
                <a href="network_setup.php" class="block w-full py-3 px-6 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border-2 border-indigo-200 rounded-lg text-md font-bold transition shadow-sm flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    Link a Receptionist PC
                </a>
                <p class="text-xs text-gray-400 mt-2">Click here to get the network address</p>
            </div>
        </div>
    </div>
</body>
</html>
