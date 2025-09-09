<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Staff Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['staff_id'])) {
        header('Location: staff_login.php');
        exit;
    }
    
    include '../global/php/db_connect.php';
    ?>
    <header>
        <h2>Order Management</h2>
        <nav class="staff-nav">
            <a href="index.php">Dashboard</a>
            <a href="payment_processing.php">Payments</a>
            <a href="sales_report.php">Sales Report</a>
            <a href="staff_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="order-filters">
            <button class="filter-btn active" onclick="filterOrders('all')">All Orders</button>
            <button class="filter-btn" onclick="filterOrders('pending')">Pending</button>
            <button class="filter-btn" onclick="filterOrders('confirmed')">Confirmed</button>
            <button class="filter-btn" onclick="filterOrders('preparing')">Preparing</button>
            <button class="filter-btn" onclick="filterOrders('ready')">Ready</button>
            <button class="filter-btn" onclick="filterOrders('completed')">Completed</button>
        </div>
        
        <div class="orders-container">
            <?php
            $stmt = $conn->prepare("SELECT o.*, c.name as customer_name, c.phone as customer_phone, 
                                   COUNT(oi.id) as item_count 
                                   FROM orders o 
                                   JOIN customers c ON o.customer_id = c.id 
                                   LEFT JOIN order_items oi ON o.id = oi.order_id 
                                   WHERE o.status != 'cancelled'
                                   GROUP BY o.id 
                                   ORDER BY o.order_date DESC");
            $stmt->execute();
            $orders = $stmt->get_result();
            ?>
            
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card" data-status="<?php echo $order['status']; ?>">
                    <div class="order-header">
                        <h3>Order #<?php echo $order['id']; ?></h3>
                        <span class="order-status status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    
                    <div class="order-info">
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                        <p><strong>Items:</strong> <?php echo $order['item_count']; ?></p>
                        <p><strong>Total:</strong> ৳<?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    
                    <div class="order-actions">
                        <button onclick="viewOrderItems(<?php echo $order['id']; ?>)" class="btn btn-info">View Items</button>
                        
                        <?php if ($order['status'] == 'pending'): ?>
                            <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmed')" class="btn btn-success">Confirm</button>
                        <?php elseif ($order['status'] == 'confirmed'): ?>
                            <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')" class="btn btn-warning">Start Preparing</button>
                        <?php elseif ($order['status'] == 'preparing'): ?>
                            <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'ready')" class="btn btn-primary">Mark Ready</button>
                        <?php elseif ($order['status'] == 'ready'): ?>
                            <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')" class="btn btn-success">Complete</button>
                        <?php endif; ?>
                        
                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')" class="btn btn-danger">Cancel</button>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <?php
            $stmt->close();
            $conn->close();
            ?>
        </div>
        
        <!-- Order Items Modal -->
        <div id="orderItemsModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <div id="orderItemsContent"></div>
            </div>
        </div>
    </main>
    
    <script src="../global/js/main.js"></script>
    <script>
        function filterOrders(status) {
            const orders = document.querySelectorAll('.order-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            orders.forEach(order => {
                if (status === 'all' || order.dataset.status === status) {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            });
        }
        
        function updateOrderStatus(orderId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus} this order?`)) {
                fetch('update_order_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating order status');
                    }
                });
            }
        }
        
        function viewOrderItems(orderId) {
            fetch(`../customer/order_details.php?order_id=${orderId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderItemsContent').innerHTML = data;
                    document.getElementById('orderItemsModal').style.display = 'block';
                });
        }
        
        function closeModal() {
            document.getElementById('orderItemsModal').style.display = 'none';
        }
    </script>
</body>
</html>
