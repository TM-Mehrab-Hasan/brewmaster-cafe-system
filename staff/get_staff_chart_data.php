<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

$staff_id = $_SESSION['staff_id'];
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get hourly sales data
$hourlySales = ['labels' => [], 'values' => []];
for ($hour = 0; $hour < 24; $hour++) {
    $hourLabel = sprintf('%02d:00', $hour);
    $hourlySales['labels'][] = $hourLabel;
    
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as hourly_total 
                           FROM payments 
                           WHERE staff_id = ? 
                           AND DATE(payment_date) BETWEEN ? AND ? 
                           AND HOUR(payment_date) = ?");
    $stmt->bind_param("issi", $staff_id, $start_date, $end_date, $hour);
    $stmt->execute();
    $hourly_total = $stmt->get_result()->fetch_assoc()['hourly_total'];
    $hourlySales['values'][] = floatval($hourly_total);
    $stmt->close();
}

// Get payment methods data
$stmt = $conn->prepare("SELECT payment_method, COUNT(*) as count 
                       FROM payments 
                       WHERE staff_id = ? 
                       AND DATE(payment_date) BETWEEN ? AND ? 
                       GROUP BY payment_method 
                       ORDER BY count DESC");
$stmt->bind_param("iss", $staff_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$paymentMethods = ['labels' => [], 'values' => []];
while ($row = $result->fetch_assoc()) {
    $paymentMethods['labels'][] = ucfirst($row['payment_method']);
    $paymentMethods['values'][] = intval($row['count']);
}
$stmt->close();

$conn->close();

echo json_encode([
    'hourlySales' => $hourlySales,
    'paymentMethods' => $paymentMethods
]);
?>
