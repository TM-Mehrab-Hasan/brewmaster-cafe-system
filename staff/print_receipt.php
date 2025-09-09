<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.4;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .receipt-info {
            margin: 15px 0;
        }
        .receipt-items {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .item-name {
            flex: 1;
        }
        .item-qty {
            width: 30px;
            text-align: center;
        }
        .item-price {
            width: 60px;
            text-align: right;
        }
        .receipt-total {
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: bold;
        }
        .receipt-footer {
            text-align: center;
            font-size: 12px;
            margin-top: 15px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <?php
    include '../global/php/db_connect.php';
    
    $payment_id = $_GET['payment_id'] ?? '';
    
    if (!$payment_id) {
        echo "Invalid payment ID";
        exit;
    }
    
    // Get payment details
    $stmt = $conn->prepare("SELECT p.*, o.order_date, o.total_amount as order_total, 
                           c.name as customer_name, c.phone as customer_phone,
                           s.name as staff_name
                           FROM payments p 
                           JOIN orders o ON p.order_id = o.id 
                           JOIN customers c ON o.customer_id = c.id 
                           JOIN staff s ON p.staff_id = s.id 
                           WHERE p.id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$payment) {
        echo "Payment not found";
        exit;
    }
    
    // Get order items
    $stmt = $conn->prepare("SELECT oi.*, pr.name as product_name 
                           FROM order_items oi 
                           JOIN products pr ON oi.product_id = pr.id 
                           WHERE oi.order_id = ?");
    $stmt->bind_param("i", $payment['order_id']);
    $stmt->execute();
    $items = $stmt->get_result();
    ?>
    
    <div class="receipt-header">
        <h1 class="receipt-title">☕ CAFE RECEIPT</h1>
        <p>Thank you for your visit!</p>
    </div>
    
    <div class="receipt-info">
        <p><strong>Receipt #:</strong> <?php echo str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Order #:</strong> <?php echo $payment['order_id']; ?></p>
        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($payment['payment_date'])); ?></p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($payment['customer_name']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($payment['customer_phone']); ?></p>
        <p><strong>Served by:</strong> <?php echo htmlspecialchars($payment['staff_name']); ?></p>
    </div>
    
    <div class="receipt-items">
        <div class="item-row" style="font-weight: bold; border-bottom: 1px solid #000;">
            <span class="item-name">ITEM</span>
            <span class="item-qty">QTY</span>
            <span class="item-price">PRICE</span>
        </div>
        
        <?php while ($item = $items->fetch_assoc()): ?>
            <div class="item-row">
                <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                <span class="item-qty"><?php echo $item['quantity']; ?></span>
                <span class="item-price">৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
        <?php endwhile; ?>
    </div>
    
    <div class="receipt-total">
        <div class="total-row">
            <span>SUBTOTAL:</span>
            <span>৳<?php echo number_format($payment['amount'], 2); ?></span>
        </div>
        <div class="total-row">
            <span>TOTAL:</span>
            <span>৳<?php echo number_format($payment['amount'], 2); ?></span>
        </div>
        <div class="total-row">
            <span>PAID (<?php echo strtoupper($payment['payment_method']); ?>):</span>
            <span>৳<?php echo number_format($payment['received_amount'], 2); ?></span>
        </div>
        <?php if ($payment['change_amount'] > 0): ?>
            <div class="total-row">
                <span>CHANGE:</span>
                <span>৳<?php echo number_format($payment['change_amount'], 2); ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="receipt-footer">
        <p>*** THANK YOU ***</p>
        <p>Please visit us again!</p>
        <p>Follow us on social media</p>
        <p>-------------------</p>
        <p>Cafe Management System</p>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #6a11cb; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Receipt</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>
    
    <?php
    $stmt->close();
    $conn->close();
    ?>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
