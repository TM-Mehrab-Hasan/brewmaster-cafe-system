<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    exit('Not authorized');
}

include '../global/php/db_connect.php';
$customer_id = $_SESSION['customer_id'];
$order_id = $_GET['order_id'] ?? '';

if (!$order_id) {
    exit('Invalid order ID');
}

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    exit('Order not found');
}

// Get order items
$stmt = $conn->prepare("SELECT oi.*, p.name as product_name 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<div class="order-details">
    <h2>Order #<?php echo $order['id']; ?> Details</h2>
    
    <div class="order-info">
        <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
        <p><strong>Status:</strong> <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
        <p><strong>Total Amount:</strong> ৳<?php echo number_format($order['total_amount'], 2); ?></p>
    </div>
    
    <h3>Items Ordered:</h3>
    <div class="order-items">
        <?php while ($item = $items->fetch_assoc()): ?>
            <div class="order-item">
                <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                <span class="item-quantity">Qty: <?php echo $item['quantity']; ?></span>
                <span class="item-price">৳<?php echo number_format($item['price'], 2); ?></span>
                <span class="item-total">৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
?>
