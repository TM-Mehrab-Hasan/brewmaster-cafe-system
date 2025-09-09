<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

$customer_id = $_SESSION['customer_id'];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

if (!$name || !$email || !$phone) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Check if email is already taken by another customer
$stmt = $conn->prepare("SELECT id FROM customers WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $customer_id);
$stmt->execute();
$existing = $stmt->get_result();

if ($existing->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already taken']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Update customer profile
$stmt = $conn->prepare("UPDATE customers SET name = ?, email = ?, phone = ? WHERE id = ?");
$stmt->bind_param("sssi", $name, $email, $phone, $customer_id);

if ($stmt->execute()) {
    $_SESSION['customer_name'] = $name; // Update session
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
