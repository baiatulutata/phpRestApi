<?php
$host = 'localhost';      // Your database host
$dbname = 'your_db_name'; // Your database name
$username = 'your_user';  // Your database username
$password = 'your_password';  // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
