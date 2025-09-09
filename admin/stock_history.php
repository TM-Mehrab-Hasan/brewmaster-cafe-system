<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock History</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .history-container { max-width: 800px; margin: 0 auto; }
        .history-header { text-align: center; margin-bottom: 20px; }
        .movement-card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            margin: 10px 0; 
            background: #f9f9f9; 
        }
        .movement-header { display: flex; justify-content: space-between; align-items: center; }
        .movement-type { padding: 5px 10px; border-radius: 20px; color: white; font-size: 12px; }
        .movement-type.restock { background: #28a745; }
        .movement-type.damaged { background: #dc3545; }
        .movement-type.expired { background: #fd7e14; }
        .movement-type.adjustment { background: #17a2b8; }
        .movement-type.other { background: #6c757d; }
        .stock-change { font-weight: bold; font-size: 18px; }
        .stock-change.positive { color: #28a745; }
        .stock-change.negative { color: #dc3545; }
        .print-btn { margin: 10px 0; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        echo '<script>window.close();</script>';
        exit;
    }
    
    include '../global/php/db_connect.php';
    
    $product_id = $_GET['product_id'] ?? 0;
    
    // Get product info
    $stmt = $conn->prepare("SELECT name, stock_quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$product) {
        echo '<h2>Product not found</h2>';
        exit;
    }
    ?>
    
    <div class="history-container">
        <div class="history-header">
            <h2>Stock History: <?php echo htmlspecialchars($product['name']); ?></h2>
            <p>Current Stock: <strong><?php echo $product['stock_quantity']; ?> units</strong></p>
            <button onclick="window.print()" class="print-btn">Print History</button>
        </div>
        
        <?php
        // Get stock movement history
        $stmt = $conn->prepare("SELECT sm.*, a.username as admin_name 
                               FROM stock_movements sm 
                               LEFT JOIN admins a ON sm.admin_id = a.id 
                               WHERE sm.product_id = ? 
                               ORDER BY sm.created_at DESC");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $movements = $stmt->get_result();
        
        if ($movements->num_rows === 0) {
            echo '<p>No stock movements found for this product.</p>';
        } else {
            while ($movement = $movements->fetch_assoc()):
        ?>
            <div class="movement-card">
                <div class="movement-header">
                    <div>
                        <span class="movement-type <?php echo $movement['reason']; ?>">
                            <?php echo ucfirst($movement['reason']); ?>
                        </span>
                        <span style="margin-left: 10px; color: #666;">
                            <?php echo date('M j, Y g:i A', strtotime($movement['created_at'])); ?>
                        </span>
                    </div>
                    <div class="stock-change <?php echo $movement['change_amount'] >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $movement['change_amount'] >= 0 ? '+' : ''; ?><?php echo $movement['change_amount']; ?>
                    </div>
                </div>
                
                <div style="margin-top: 10px;">
                    <strong>Stock Change:</strong> 
                    <?php echo $movement['previous_stock']; ?> → <?php echo $movement['new_stock']; ?> units
                </div>
                
                <?php if ($movement['notes']): ?>
                    <div style="margin-top: 8px;">
                        <strong>Notes:</strong> <?php echo htmlspecialchars($movement['notes']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($movement['admin_name']): ?>
                    <div style="margin-top: 8px; color: #666; font-size: 12px;">
                        Updated by: <?php echo htmlspecialchars($movement['admin_name']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php 
            endwhile;
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
