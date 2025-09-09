<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? '';
$status = $input['status'] ?? '';

if (!$order_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
