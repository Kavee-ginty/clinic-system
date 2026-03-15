<?php
// config/db.php
$host = '127.0.0.1';
$port = '3307';
$db   = 'clinic_system';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Auto-create Settings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS Settings (SettingKey VARCHAR(50) PRIMARY KEY, SettingValue TEXT)");
    $pdo->exec("INSERT IGNORE INTO Settings (SettingKey, SettingValue) VALUES 
        ('clinic_name', 'Royal Channel Center'),
        ('doctor_name', 'Dr. John Doe, MBBS'),
        ('clinic_address', '123 Health Street, Cityville'),
        ('clinic_phone', 'Tel: +1 234 567 890'),
        ('admin_password', 'Gino'),
        ('doctor_password', 'Gino'),
        ('visit_fee', '500'),
        ('print_page_size', 'A4'),
        ('print_text_size', '14px')
    ");

    $pdo->exec("CREATE TABLE IF NOT EXISTS Drugs (
        DrugID INT AUTO_INCREMENT PRIMARY KEY,
        DrugName VARCHAR(100) NOT NULL,
        BatchNumber VARCHAR(50),
        Quantity INT DEFAULT 0,
        UnitPrice DECIMAL(10,2) DEFAULT 0.00
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS VisitDrugs (
        VisitDrugID INT AUTO_INCREMENT PRIMARY KEY,
        VisitID INT NOT NULL,
        DrugID INT,
        DrugName VARCHAR(100) DEFAULT '',
        Quantity INT DEFAULT 0,
        TotalCost DECIMAL(10,2) DEFAULT 0.00,
        Frequency VARCHAR(100) DEFAULT '',
        Dose VARCHAR(100) DEFAULT '',
        Duration VARCHAR(100) DEFAULT '',
        FOREIGN KEY (VisitID) REFERENCES Visits(VisitID)
    )");

    // Add new columns to Patients table dynamically if they don't exist
    try { $pdo->exec("ALTER TABLE Patients ADD COLUMN PatientNumber VARCHAR(50) DEFAULT ''"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE Patients ADD COLUMN NIC VARCHAR(20) DEFAULT ''"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE Patients ADD COLUMN Age INT DEFAULT 0"); } catch (\Exception $e) {}

    try { $pdo->exec("ALTER TABLE Drugs ADD COLUMN Dose VARCHAR(100) DEFAULT ''"); } catch (\Exception $e) {}

    try { $pdo->exec("ALTER TABLE Visits ADD COLUMN VisitFee DECIMAL(10,2) DEFAULT 0.00"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE Visits ADD COLUMN TotalBill DECIMAL(10,2) DEFAULT 0.00"); } catch (\Exception $e) {}

    try { $pdo->exec("ALTER TABLE VisitDrugs ADD COLUMN Frequency VARCHAR(100) DEFAULT ''"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE VisitDrugs ADD COLUMN Dose VARCHAR(100) DEFAULT ''"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE VisitDrugs ADD COLUMN Duration VARCHAR(100) DEFAULT ''"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE VisitDrugs ADD COLUMN DrugName VARCHAR(100) DEFAULT ''"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE VisitDrugs MODIFY DrugID INT NULL"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE VisitDrugs DROP FOREIGN KEY visitdrugs_ibfk_2"); } catch (\Exception $e) {}

    // Allow Queue to be reset without Cascade Deleting historical Visits
    try { $pdo->exec("ALTER TABLE Visits DROP FOREIGN KEY visits_ibfk_2"); } catch (\Exception $e) {}
    try { $pdo->exec("ALTER TABLE Visits MODIFY QueueID INT NULL"); } catch (\Exception $e) {}

} catch (\PDOException $e) {
    // If database doesn't exist, we send a JSON error or plain text depending on caller
    die(json_encode(['error' => 'Database connection failed. Please ensure MySQL is running and database clinic_system is imported.']));
}
?>
