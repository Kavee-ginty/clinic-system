<?php
require_once 'config/db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS Settings (
            SettingKey VARCHAR(50) PRIMARY KEY,
            SettingValue TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $stmt = $pdo->query("SELECT COUNT(*) FROM Settings");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO Settings (SettingKey, SettingValue) VALUES 
            ('clinic_name', 'Royal Channel Center'),
            ('doctor_name', 'Dr. John Doe, MBBS'),
            ('clinic_address', '123 Health Street, Cityville'),
            ('clinic_phone', 'Tel: +1 234 567 890'),
            ('admin_password', 'Gino')
        ");
        echo "Settings inserted.\n";
    } else {
        echo "Settings already exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
