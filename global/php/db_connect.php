<?php
// Database connection for Cafe Management System
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'cafe_management';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
