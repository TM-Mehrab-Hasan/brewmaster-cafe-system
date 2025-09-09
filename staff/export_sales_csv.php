<?php
session_start();

if (!isset($_SESSION['staff_id'])) {
    header('Location: staff_login.php');
    exit;
}

include '../global/php/db_connect.php';

$staff_id = $_SESSION['staff_id'];
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report_' . $start_date . '_to_' . $end_date . '.csv"');

// Create file pointer
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, [
    'Date',
    'Time', 
    'Order ID',
    'Customer Name',
    'Amount (৳)',
    'Payment Method',
    'Transaction ID'
]);

// Get transactions data
$stmt = $conn->prepare("SELECT p.*, o.id as order_id, c.name as customer_name 
                       FROM payments p 
                       JOIN orders o ON p.order_id = o.id 
                       JOIN customers c ON o.customer_id = c.id 
                       WHERE p.staff_id = ? 
                       AND DATE(p.payment_date) BETWEEN ? AND ? 
                       ORDER BY p.payment_date ASC");
$stmt->bind_param("iss", $staff_id, $start_date, $end_date);
$stmt->execute();
$transactions = $stmt->get_result();

// Add data rows
while ($transaction = $transactions->fetch_assoc()) {
    fputcsv($output, [
        date('Y-m-d', strtotime($transaction['payment_date'])),
        date('H:i:s', strtotime($transaction['payment_date'])),
        $transaction['order_id'],
        $transaction['customer_name'],
        number_format($transaction['amount'], 2),
        ucfirst($transaction['payment_method']),
        $transaction['id']
    ]);
}

// Add summary row
fputcsv($output, []); // Empty row
fputcsv($output, ['=== SUMMARY ===']);

// Get summary data
$stmt = $conn->prepare("SELECT 
                       COUNT(p.id) as total_transactions,
                       COALESCE(SUM(p.amount), 0) as total_sales,
                       COALESCE(AVG(p.amount), 0) as avg_transaction
                       FROM payments p 
                       WHERE p.staff_id = ? 
                       AND DATE(p.payment_date) BETWEEN ? AND ?");
$stmt->bind_param("iss", $staff_id, $start_date, $end_date);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();

fputcsv($output, ['Total Transactions', $summary['total_transactions']]);
fputcsv($output, ['Total Sales (৳)', number_format($summary['total_sales'], 2)]);
fputcsv($output, ['Average Transaction (৳)', number_format($summary['avg_transaction'], 2)]);

$stmt->close();
$conn->close();

fclose($output);
exit;
?>
