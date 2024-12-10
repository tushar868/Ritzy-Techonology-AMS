<?php
$host = "localhost";
$db_name = "attendance_system";
$username = "root"; // Use your Hostinger DB username
$password = "";     // Use your Hostinger DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
