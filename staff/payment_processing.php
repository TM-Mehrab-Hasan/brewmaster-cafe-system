<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - Staff Panel</title>
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
        <h2>Payment Processing</h2>
        <nav class="staff-nav">
            <a href="index.php">Dashboard</a>
            <a href="order_management.php">Orders</a>
            <a href="sales_report.php">Sales Report</a>
            <a href="staff_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="payment-container">
            <!-- Orders Ready for Payment -->
            <div class="ready-orders">
                <h3>Orders Ready for Payment</h3>
                <?php
                $stmt = $conn->prepare("SELECT o.*, c.name as customer_name, c.phone as customer_phone 
                                       FROM orders o 
                                       JOIN customers c ON o.customer_id = c.id 
                                       WHERE o.status = 'ready' 
                                       ORDER BY o.order_date ASC");
                $stmt->execute();
                $ready_orders = $stmt->get_result();
                ?>
                
                <div class="orders-grid">
                    <?php while ($order = $ready_orders->fetch_assoc()): ?>
                        <div class="payment-order-card">
                            <div class="order-header">
                                <h4>Order #<?php echo $order['id']; ?></h4>
                                <span class="order-time"><?php echo date('h:i A', strtotime($order['order_date'])); ?></span>
                            </div>
                            
                            <div class="order-info">
                                <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                <p class="order-total"><strong>Total: ৳<?php echo number_format($order['total_amount'], 2); ?></strong></p>
                            </div>
                            
                            <div class="payment-actions">
                                <button onclick="viewOrderItems(<?php echo $order['id']; ?>)" class="btn btn-info">View Items</button>
                                <button onclick="processPayment(<?php echo $order['id']; ?>, <?php echo $order['total_amount']; ?>)" class="btn btn-success">Process Payment</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php if ($ready_orders->num_rows == 0): ?>
                    <div class="no-orders">
                        <p>No orders ready for payment</p>
                    </div>
                <?php endif; ?>
                
                <?php $stmt->close(); ?>
            </div>
            
            <!-- Payment History -->
            <div class="payment-history">
                <h3>Recent Payments</h3>
                <?php
                $stmt = $conn->prepare("SELECT p.*, o.id as order_id, c.name as customer_name 
                                       FROM payments p 
                                       JOIN orders o ON p.order_id = o.id 
                                       JOIN customers c ON o.customer_id = c.id 
                                       ORDER BY p.payment_date DESC 
                                       LIMIT 10");
                $stmt->execute();
                $payments = $stmt->get_result();
                ?>
                
                <div class="payments-list">
                    <?php while ($payment = $payments->fetch_assoc()): ?>
                        <div class="payment-record">
                            <div class="payment-info">
                                <h5>Order #<?php echo $payment['order_id']; ?> - <?php echo htmlspecialchars($payment['customer_name']); ?></h5>
                                <p>Amount: ৳<?php echo number_format($payment['amount'], 2); ?> | Method: <?php echo ucfirst($payment['payment_method']); ?></p>
                                <p class="payment-time"><?php echo date('M d, Y h:i A', strtotime($payment['payment_date'])); ?></p>
                            </div>
                            <button onclick="printReceipt(<?php echo $payment['id']; ?>)" class="btn btn-primary">Print Receipt</button>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php $stmt->close(); $conn->close(); ?>
            </div>
        </div>
        
        <!-- Payment Modal -->
        <div id="paymentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closePaymentModal()">&times;</span>
                <div id="paymentForm">
                    <h3>Process Payment</h3>
                    <form id="paymentProcessForm">
                        <input type="hidden" id="payment_order_id" name="order_id">
                        
                        <div class="form-group">
                            <label for="payment_amount">Amount (৳)</label>
                            <input type="number" id="payment_amount" name="amount" step="0.01" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="received_amount">Amount Received (৳)</label>
                            <input type="number" id="received_amount" name="received_amount" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="change_amount">Change (৳)</label>
                            <input type="number" id="change_amount" name="change_amount" step="0.01" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_notes">Notes (Optional)</label>
                            <textarea id="payment_notes" name="notes" rows="2"></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="closePaymentModal()" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-success">Complete Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Order Items Modal -->
        <div id="orderItemsModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeOrderModal()">&times;</span>
                <div id="orderItemsContent"></div>
            </div>
        </div>
    </main>
    
    <script src="../global/js/main.js"></script>
    <script>
        function processPayment(orderId, amount) {
            document.getElementById('payment_order_id').value = orderId;
            document.getElementById('payment_amount').value = amount.toFixed(2);
            document.getElementById('received_amount').value = amount.toFixed(2);
            calculateChange();
            document.getElementById('paymentModal').style.display = 'block';
        }
        
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentProcessForm').reset();
        }
        
        function viewOrderItems(orderId) {
            fetch(`../customer/order_details.php?order_id=${orderId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderItemsContent').innerHTML = data;
                    document.getElementById('orderItemsModal').style.display = 'block';
                });
        }
        
        function closeOrderModal() {
            document.getElementById('orderItemsModal').style.display = 'none';
        }
        
        // Calculate change amount
        document.getElementById('received_amount').addEventListener('input', calculateChange);
        
        function calculateChange() {
            const amount = parseFloat(document.getElementById('payment_amount').value) || 0;
            const received = parseFloat(document.getElementById('received_amount').value) || 0;
            const change = received - amount;
            document.getElementById('change_amount').value = change.toFixed(2);
        }
        
        // Handle payment form submission
        document.getElementById('paymentProcessForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment processed successfully!');
                    if (confirm('Print receipt?')) {
                        printReceipt(data.payment_id);
                    }
                    location.reload();
                } else {
                    alert('Error processing payment: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing payment');
            });
        });
        
        function printReceipt(paymentId) {
            window.open(`print_receipt.php?payment_id=${paymentId}`, '_blank', 'width=600,height=800');
        }
    </script>
</body>
</html>
