<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

include '../global/php/db_connect.php';

$product_id = $_POST['product_id'] ?? '';

if ($product_id) {
    // Toggle the availability status
    $stmt = $conn->prepare("UPDATE products SET is_available = NOT is_available WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        header('Location: product_management.php?success=toggled');
    } else {
        header('Location: product_management.php?error=failed');
    }
    $stmt->close();
} else {
    header('Location: product_management.php?error=invalid_id');
}
$conn->close();
?>
