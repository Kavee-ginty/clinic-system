<?php
// Script to get local IP and Hostname
function getServerLocalIP() {
    $os = strtoupper(substr(PHP_OS, 0, 3));
    if ($os === 'WIN') {
        exec("ipconfig", $output);
        foreach ($output as $line) {
            if (preg_match('/IPv4 Address.*:\s+([0-9\.]+)/i', $line, $matches) || preg_match('/IP Address.*:\s+([0-9\.]+)/i', $line, $matches)) {
                $ip = $matches[1];
                if ($ip !== '127.0.0.1' && strpos($ip, '169.254') !== 0) {
                    return $ip;
                }
            }
        }
    } else {
        exec("ifconfig || ip addr", $output);
        foreach ($output as $line) {
            if (preg_match('/inet\s+([0-9\.]+)/', $line, $matches)) {
                $ip = $matches[1];
                if ($ip !== '127.0.0.1') {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
}

$local_ip = getServerLocalIP();
$hostname = gethostname(); // Get the computer's name

// Get the base folder name to construct the LAN URL dynamically
$script_path = $_SERVER['SCRIPT_NAME'];
$folder_name = dirname($script_path);
if ($folder_name === '\\' || $folder_name === '/') $folder_name = '';

$lan_url_ip = "http://" . $local_ip . $folder_name;
$lan_url_host = "http://" . $hostname . $folder_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Setup - Clinic System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-3xl w-full border-t-8 border-teal-600">
        
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-gray-800 mb-2">Connect Receptionist PC</h1>
            <p class="text-gray-600 text-lg">Your router restarts daily, so please use the <strong>Permanent Link</strong> below.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- OPTION 1: HOSTNAME (Permanent) -->
            <div class="bg-green-50 border-2 border-green-500 p-6 rounded-xl relative shadow-sm">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-1 rounded-full text-xs font-bold shadow">
                    ⭐ RECOMMENDED & PERMANENT
                </div>
                <h2 class="text-lg font-bold text-green-800 mb-2 mt-2 text-center">Use Computer Name</h2>
                <p class="text-green-900 text-sm mb-4 text-center">This link will <strong>never change</strong> even when the router reboots.</p>
                
                <div class="bg-white border-2 border-green-300 rounded-lg p-3 text-center cursor-pointer hover:bg-green-100 transition" onclick="copyToClipboard('hostUrl', 'hostCopyMsg')">
                    <div class="text-xl font-black text-green-700 font-mono break-all" id="hostUrl"><?= $lan_url_host ?></div>
                </div>
                <p id="hostCopyMsg" class="text-center text-green-600 font-bold mt-2 text-sm hidden">Copied!</p>
            </div>

            <!-- OPTION 2: IP ADDRESS (Fallback) -->
            <div class="bg-blue-50 border-2 border-blue-300 p-6 rounded-xl relative opacity-90">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-400 text-white px-4 py-1 rounded-full text-xs font-bold shadow">
                    FALLBACK ONLY
                </div>
                <h2 class="text-lg font-bold text-blue-800 mb-2 mt-2 text-center">Use IP Address</h2>
                <p class="text-blue-900 text-sm mb-4 text-center">This number changes daily. Only use this if the Permanent link fails.</p>
                
                <div class="bg-white border-2 border-blue-200 rounded-lg p-3 text-center cursor-pointer hover:bg-blue-100 transition" onclick="copyToClipboard('ipUrl', 'ipCopyMsg')">
                    <div class="text-xl font-black text-blue-700 font-mono break-all" id="ipUrl"><?= $lan_url_ip ?></div>
                </div>
                <p id="ipCopyMsg" class="text-center text-blue-600 font-bold mt-2 text-sm hidden">Copied!</p>
            </div>
        </div>

        <div class="bg-gray-100 p-6 rounded-lg text-center mb-6">
            <h3 class="font-bold text-gray-700 mb-2">How to connect the Receptionist:</h3>
            <p class="text-gray-600">On the Receptionist's computer, open Google Chrome and type the <strong>Permanent Link</strong> into the very top bar. Once it loads, <strong>Bookmark it</strong> (press Ctrl+D).</p>
        </div>

        <!-- QR Code -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
            <div class="text-center md:text-left">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Mobile / Tablet Setup</h3>
                <p class="text-gray-600 text-sm">Scan this QR to connect instantly via IP.</p>
            </div>
            <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-300">
                <div id="qrcode"></div>
            </div>
        </div>

        <div class="mt-8 text-center pt-6 border-t border-gray-200">
            <a href="index.php" class="inline-block px-10 py-4 bg-gray-800 hover:bg-black text-white font-black text-xl rounded-xl shadow-lg transition">
                Return to Clinic System
            </a>
        </div>

    </div>

    <script>
        // Generate QR Code (Using IP as it's more reliable for isolated mobile devices)
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $lan_url_ip ?>",
            width: 100,
            height: 100,
            colorDark : "#1f2937",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });

        // Copy to clipboard
        function copyToClipboard(elementId, msgId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                const msg = document.getElementById(msgId);
                msg.classList.remove("hidden");
                setTimeout(() => { msg.classList.add("hidden"); }, 2000);
            });
        }
    </script>
</body>
</html>
