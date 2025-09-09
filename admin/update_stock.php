<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

include '../global/php/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];
    $quantity = intval($_POST['quantity']);
    $reason = $_POST['reason'];
    $notes = $_POST['notes'] ?? '';
    $admin_id = $_SESSION['admin_id'];
    
    try {
        $conn->begin_transaction();
        
        // Get current stock
        $stmt = $conn->prepare("SELECT name, stock_quantity FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        $current_stock = $product['stock_quantity'];
        $new_stock = 0;
        
        // Calculate new stock based on action
        switch ($action) {
            case 'add':
                $new_stock = $current_stock + $quantity;
                break;
            case 'remove':
                $new_stock = max(0, $current_stock - $quantity);
                break;
            case 'set':
                $new_stock = $quantity;
                break;
            default:
                throw new Exception('Invalid action');
        }
        
        // Update product stock
        $stmt = $conn->prepare("UPDATE products SET stock_quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $product_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update stock');
        }
        $stmt->close();
        
        // Log stock movement
        $change_amount = $new_stock - $current_stock;
        $stmt = $conn->prepare("INSERT INTO stock_movements (product_id, change_amount, reason, notes, admin_id, previous_stock, new_stock, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissiii", $product_id, $change_amount, $reason, $notes, $admin_id, $current_stock, $new_stock);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to log stock movement');
        }
        $stmt->close();
        
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Stock updated successfully',
            'new_stock' => $new_stock,
            'change' => $change_amount
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
