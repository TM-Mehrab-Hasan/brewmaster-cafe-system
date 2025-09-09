<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

include '../global/php/db_connect.php';

$product_id = $_POST['product_id'] ?? '';

if ($product_id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        header('Location: product_management.php?success=deleted');
    } else {
        header('Location: product_management.php?error=failed');
    }
    $stmt->close();
} else {
    header('Location: product_management.php?error=invalid_id');
}
$conn->close();
?>
