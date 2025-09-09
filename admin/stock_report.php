<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report - Cafe Management</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .summary-card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: admin_login.php');
        exit;
    }
    
    include '../global/php/db_connect.php';
    
    $low_stock_threshold = 10;
    ?>
    
    <div class="no-print" style="text-align: center; margin: 1rem;">
        <button onclick="window.print()" class="btn btn-primary">Print Report</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
    
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <header style="text-align: center; margin-bottom: 2rem;">
            <h1>Stock Report</h1>
            <p>Generated on: <?php echo date('F j, Y g:i A'); ?></p>
        </header>
        
        <!-- Summary Section -->
        <?php
        $stmt = $conn->prepare("SELECT 
                               COUNT(*) as total_items,
                               SUM(CASE WHEN stock_quantity <= ? THEN 1 ELSE 0 END) as low_stock_items,
                               SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                               SUM(stock_quantity * price) as total_inventory_value,
                               AVG(stock_quantity) as avg_stock
                               FROM products");
        $stmt->bind_param("i", $low_stock_threshold);
        $stmt->execute();
        $summary = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        ?>
        
        <div class="summary-cards" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: white; padding: 1rem; border: 1px solid #ddd; border-radius: 8px;">
                <h3>Total Items</h3>
                <p style="font-size: 2rem; margin: 0; color: #6a11cb;"><?php echo $summary['total_items']; ?></p>
            </div>
            <div style="background: white; padding: 1rem; border: 1px solid #ddd; border-radius: 8px;">
                <h3>Low Stock Items</h3>
                <p style="font-size: 2rem; margin: 0; color: #ffc107;"><?php echo $summary['low_stock_items']; ?></p>
            </div>
            <div style="background: white; padding: 1rem; border: 1px solid #ddd; border-radius: 8px;">
                <h3>Out of Stock</h3>
                <p style="font-size: 2rem; margin: 0; color: #dc3545;"><?php echo $summary['out_of_stock']; ?></p>
            </div>
            <div style="background: white; padding: 1rem; border: 1px solid #ddd; border-radius: 8px;">
                <h3>Total Value</h3>
                <p style="font-size: 2rem; margin: 0; color: #28a745;">৳<?php echo number_format($summary['total_inventory_value'], 2); ?></p>
            </div>
        </div>
        
        <!-- Low Stock Items -->
        <div style="margin-bottom: 2rem;">
            <h2>Low Stock Items (≤<?php echo $low_stock_threshold; ?> units)</h2>
            <?php
            $stmt = $conn->prepare("SELECT name, category, stock_quantity, price, (stock_quantity * price) as value 
                                   FROM products 
                                   WHERE stock_quantity <= ? 
                                   ORDER BY stock_quantity ASC");
            $stmt->bind_param("i", $low_stock_threshold);
            $stmt->execute();
            $low_stock = $stmt->get_result();
            
            if ($low_stock->num_rows > 0): ?>
                <table style="width: 100%; border-collapse: collapse; background: white;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: left;">Item</th>
                            <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: left;">Category</th>
                            <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: center;">Stock</th>
                            <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">Unit Price</th>
                            <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $low_stock->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 0.8rem; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td style="padding: 0.8rem; border: 1px solid #ddd;"><?php echo ucfirst($item['category']); ?></td>
                                <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: center; color: <?php echo $item['stock_quantity'] == 0 ? '#dc3545' : '#ffc107'; ?>; font-weight: bold;">
                                    <?php echo $item['stock_quantity']; ?>
                                </td>
                                <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">৳<?php echo number_format($item['price'], 2); ?></td>
                                <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">৳<?php echo number_format($item['value'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #28a745; font-weight: bold;">All items are adequately stocked!</p>
            <?php endif; ?>
            <?php $stmt->close(); ?>
        </div>
        
        <!-- All Items Summary -->
        <div>
            <h2>All Items Summary</h2>
            <?php
            $stmt = $conn->prepare("SELECT name, category, stock_quantity, price, (stock_quantity * price) as value,
                                   CASE 
                                       WHEN stock_quantity = 0 THEN 'Out of Stock'
                                       WHEN stock_quantity <= ? THEN 'Low Stock'
                                       ELSE 'In Stock'
                                   END as status
                                   FROM products 
                                   ORDER BY category, name");
            $stmt->bind_param("i", $low_stock_threshold);
            $stmt->execute();
            $all_items = $stmt->get_result();
            ?>
            
            <table style="width: 100%; border-collapse: collapse; background: white;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: left;">Item</th>
                        <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: left;">Category</th>
                        <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: center;">Stock</th>
                        <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: center;">Status</th>
                        <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">Unit Price</th>
                        <th style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $all_items->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 0.8rem; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td style="padding: 0.8rem; border: 1px solid #ddd;"><?php echo ucfirst($item['category']); ?></td>
                            <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: center;"><?php echo $item['stock_quantity']; ?></td>
                            <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: center;">
                                <span style="padding: 0.3rem 0.6rem; border-radius: 12px; font-size: 0.8rem; font-weight: bold; 
                                      <?php 
                                      if ($item['status'] == 'In Stock') echo 'background: #d4edda; color: #155724;';
                                      elseif ($item['status'] == 'Low Stock') echo 'background: #fff3cd; color: #856404;';
                                      else echo 'background: #f8d7da; color: #721c24;';
                                      ?>">
                                    <?php echo $item['status']; ?>
                                </span>
                            </td>
                            <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">৳<?php echo number_format($item['price'], 2); ?></td>
                            <td style="padding: 0.8rem; border: 1px solid #ddd; text-align: right;">৳<?php echo number_format($item['value'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <?php $stmt->close(); ?>
        </div>
        
        <footer style="margin-top: 2rem; text-align: center; color: #666; border-top: 1px solid #ddd; padding-top: 1rem;">
            <p>Cafe Management System - Stock Report</p>
            <p>Report generated by: Admin (<?php echo date('Y-m-d H:i:s'); ?>)</p>
        </footer>
    </div>
    
    <?php $conn->close(); ?>
</body>
</html>
