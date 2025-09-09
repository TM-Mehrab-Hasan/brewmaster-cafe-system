<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Cafe Management</title>
    <link rel="stylesheet" href="../global/css/style.css">
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['customer_id'])) {
        header('Location: customer_login.php');
        exit;
    }
    
    include '../global/php/db_connect.php';
    $customer_id = $_SESSION['customer_id'];
    ?>
    <header>
        <h2>My Orders</h2>
        <nav class="customer-nav">
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="profile.php">Profile</a>
            <a href="customer_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="orders-list">
            <?php
            $stmt = $conn->prepare("SELECT o.*, COUNT(oi.id) as item_count 
                                  FROM orders o 
                                  LEFT JOIN order_items oi ON o.id = oi.order_id 
                                  WHERE o.customer_id = ? 
                                  GROUP BY o.id 
                                  ORDER BY o.order_date DESC");
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $orders = $stmt->get_result();
            ?>
            
            <?php if ($orders->num_rows > 0): ?>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        <div class="order-details">
                            <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                            <p><strong>Items:</strong> <?php echo $order['item_count']; ?></p>
                            <p><strong>Total:</strong> ৳<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="btn btn-primary">View Details</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-orders">
                    <h3>No orders yet</h3>
                    <p>Start by browsing our menu!</p>
                    <a href="menu.php" class="btn btn-primary">View Menu</a>
                </div>
            <?php endif; ?>
            
            <?php
            $stmt->close();
            $conn->close();
            ?>
        </div>
        
        <!-- Order Details Modal -->
        <div id="orderModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <div id="orderDetailsContent"></div>
            </div>
        </div>
    </main>
    <script src="../global/js/main.js"></script>
    <script>
        function viewOrderDetails(orderId) {
            fetch(`order_details.php?order_id=${orderId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderDetailsContent').innerHTML = data;
                    document.getElementById('orderModal').style.display = 'block';
                });
        }
        
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
