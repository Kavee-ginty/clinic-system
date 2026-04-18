<?php
require 'config/db.php';
$stmt = $pdo->query("DESCRIBE Settings");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $pdo->query("SELECT * FROM Settings");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
?>
