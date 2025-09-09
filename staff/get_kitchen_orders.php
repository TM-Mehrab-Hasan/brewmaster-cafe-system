<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

// Get orders that need kitchen attention (confirmed, preparing, ready)
$stmt = $conn->prepare("SELECT o.id, o.order_date, o.status, o.total_amount,
                       c.name as customer_name, c.phone as customer_phone
                       FROM orders o 
                       JOIN customers c ON o.customer_id = c.id 
                       WHERE o.status IN ('confirmed', 'preparing', 'ready')
                       ORDER BY o.order_date ASC");
$stmt->execute();
$orders_result = $stmt->get_result();

$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    // Get order items for each order
    $items_stmt = $conn->prepare("SELECT oi.quantity, p.name 
                                 FROM order_items oi 
                                 JOIN products p ON oi.product_id = p.id 
                                 WHERE oi.order_id = ?");
    $items_stmt->bind_param("i", $order['id']);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    $items_stmt->close();
    
    $order['items'] = $items;
    $orders[] = $order;
}

$stmt->close();
$conn->close();

echo json_encode($orders);
?>
