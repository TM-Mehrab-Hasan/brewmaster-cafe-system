<?php
session_start();
include '../global/php/db_connect.php';

$username = $_POST['admin_username'] ?? '';
$password = $_POST['admin_password'] ?? '';

// Simple admin credentials (you can change these)
$admin_username = 'admin';
$admin_password = 'admin123';

if ($username && $password) {
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        echo "Invalid credentials.";
    }
} else {
    echo "Both fields are required.";
}
?>
