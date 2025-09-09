<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

$order_id = $_POST['order_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$received_amount = $_POST['received_amount'] ?? '';
$notes = $_POST['notes'] ?? '';
$staff_id = $_SESSION['staff_id'];

if (!$order_id || !$amount || !$payment_method || !$received_amount) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert payment record
    $stmt = $conn->prepare("INSERT INTO payments (order_id, staff_id, amount, payment_method, received_amount, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidsds", $order_id, $staff_id, $amount, $payment_method, $received_amount, $notes);
    $stmt->execute();
    $payment_id = $conn->insert_id;
    $stmt->close();
    
    // Update order status to completed
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'payment_id' => $payment_id]);
    
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
