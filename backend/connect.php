<?php
$host = 'localhost';
$db = 'floreet';
$user = 'root';
$pass = ''; // Default for XAMPP

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
