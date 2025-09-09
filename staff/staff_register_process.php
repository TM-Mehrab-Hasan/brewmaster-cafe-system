<?php
include '../global/php/db_connect.php';

$name = $_POST['staff_name'] ?? '';
$email = $_POST['staff_email'] ?? '';
$password = $_POST['staff_password'] ?? '';
$phone = $_POST['staff_phone'] ?? '';

if ($name && $email && $password && $phone) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // status: pending (requires admin approval)
    $status = 'pending';
    $stmt = $conn->prepare("INSERT INTO staff (name, email, password, phone, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $status);
    if ($stmt->execute()) {
        echo "Registration submitted! Await admin approval.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "All fields are required.";
}
$conn->close();
?>
