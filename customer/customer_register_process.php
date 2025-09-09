<?php
include '../global/php/db_connect.php';

$name = $_POST['customer_name'] ?? '';
$email = $_POST['customer_email'] ?? '';
$password = $_POST['customer_password'] ?? '';
$phone = $_POST['customer_phone'] ?? '';

if ($name && $email && $password && $phone) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO customers (name, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
    if ($stmt->execute()) {
        echo "Registration successful! You can now <a href='customer_login.php'>login</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "All fields are required.";
}
$conn->close();
?>
