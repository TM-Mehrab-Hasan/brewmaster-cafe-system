<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

// Get last 7 days sales data
$dailySales = [];
$labels = [];
$values = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('M j', strtotime($date));
    
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as daily_total FROM orders WHERE DATE(order_date) = ? AND status = 'completed'");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $daily_total = $stmt->get_result()->fetch_assoc()['daily_total'];
    $values[] = floatval($daily_total);
    $stmt->close();
}

$dailySales = ['labels' => $labels, 'values' => $values];

// Get sales by category
$stmt = $conn->prepare("SELECT p.category, SUM(oi.quantity * oi.price) as category_total 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       JOIN orders o ON oi.order_id = o.id 
                       WHERE o.status = 'completed' 
                       GROUP BY p.category 
                       ORDER BY category_total DESC");
$stmt->execute();
$result = $stmt->get_result();

$categories = ['labels' => [], 'values' => []];
while ($row = $result->fetch_assoc()) {
    $categories['labels'][] = $row['category'];
    $categories['values'][] = floatval($row['category_total']);
}
$stmt->close();

// Get order status distribution
$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status ORDER BY count DESC");
$stmt->execute();
$result = $stmt->get_result();

$orderStatus = ['labels' => [], 'values' => []];
while ($row = $result->fetch_assoc()) {
    $orderStatus['labels'][] = ucfirst($row['status']);
    $orderStatus['values'][] = intval($row['count']);
}
$stmt->close();

// Get top selling products
$stmt = $conn->prepare("SELECT p.name, SUM(oi.quantity) as total_quantity 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       JOIN orders o ON oi.order_id = o.id 
                       WHERE o.status = 'completed' 
                       GROUP BY p.id 
                       ORDER BY total_quantity DESC 
                       LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();

$topProducts = ['labels' => [], 'values' => []];
while ($row = $result->fetch_assoc()) {
    $topProducts['labels'][] = $row['name'];
    $topProducts['values'][] = intval($row['total_quantity']);
}
$stmt->close();

$conn->close();

echo json_encode([
    'dailySales' => $dailySales,
    'categories' => $categories,
    'orderStatus' => $orderStatus,
    'topProducts' => $topProducts
]);
?>
