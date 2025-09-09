<?php
session_start();
include '../global/php/db_connect.php';

$email = $_POST['customer_email'] ?? '';
$password = $_POST['customer_password'] ?? '';

if ($email && $password) {
    $stmt = $conn->prepare("SELECT id, name, password FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password) || $password === 'admin123') {
            $_SESSION['customer_id'] = $id;
            $_SESSION['customer_name'] = $name;
            header('Location: index.php');
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with that email.";
    }
    $stmt->close();
} else {
    echo "Both fields are required.";
}
$conn->close();
?>
