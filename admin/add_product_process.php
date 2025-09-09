<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

include '../global/php/db_connect.php';

$name = $_POST['product_name'] ?? '';
$description = $_POST['product_description'] ?? '';
$price = $_POST['product_price'] ?? '';
$category = $_POST['product_category'] ?? '';
$image_url = $_POST['product_image'] ?? '';

if ($name && $price && $category) {
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $description, $price, $category, $image_url);
    
    if ($stmt->execute()) {
        header('Location: product_management.php?success=added');
    } else {
        header('Location: product_management.php?error=failed');
    }
    $stmt->close();
} else {
    header('Location: product_management.php?error=missing_fields');
}
$conn->close();
?>
