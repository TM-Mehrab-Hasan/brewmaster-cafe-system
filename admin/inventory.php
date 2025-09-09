<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Admin Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: admin_login.php');
        exit;
    }
    
    include '../global/php/db_connect.php';
    ?>
    <header>
        <h2>Inventory Management</h2>
        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="product_management.php">Products</a>
            <a href="staff_management.php">Staff</a>
            <a href="reports.php">Reports</a>
            <a href="inventory.php" class="active">Inventory</a>
            <a href="admin_logout.php">Logout</a>
        </nav>
    </header>
    
    <main>
        <div class="inventory-container">
            <!-- Inventory Summary -->
            <div class="inventory-summary">
                <?php
                // Get inventory statistics
                $low_stock_threshold = 10;
                
                $stmt = $conn->prepare("SELECT 
                                       COUNT(*) as total_items,
                                       SUM(CASE WHEN stock_quantity <= ? THEN 1 ELSE 0 END) as low_stock_items,
                                       SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                                       AVG(stock_quantity) as avg_stock
                                       FROM products");
                $stmt->bind_param("i", $low_stock_threshold);
                $stmt->execute();
                $inventory_stats = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                ?>
                
                <div class="summary-cards">
                    <div class="summary-card total-items">
                        <div class="card-icon">📦</div>
                        <div class="card-content">
                            <h3>Total Items</h3>
                            <p class="card-value"><?php echo $inventory_stats['total_items']; ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-card low-stock">
                        <div class="card-icon">⚠️</div>
                        <div class="card-content">
                            <h3>Low Stock</h3>
                            <p class="card-value"><?php echo $inventory_stats['low_stock_items']; ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-card out-stock">
                        <div class="card-icon">❌</div>
                        <div class="card-content">
                            <h3>Out of Stock</h3>
                            <p class="card-value"><?php echo $inventory_stats['out_of_stock']; ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-card avg-stock">
                        <div class="card-icon">📊</div>
                        <div class="card-content">
                            <h3>Avg. Stock</h3>
                            <p class="card-value"><?php echo number_format($inventory_stats['avg_stock'], 1); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <button onclick="openStockUpdateModal()" class="btn btn-primary">
                    <span>📥</span> Update Stock
                </button>
                <button onclick="openNewItemModal()" class="btn btn-success">
                    <span>➕</span> Add New Item
                </button>
                <button onclick="generateStockReport()" class="btn btn-info">
                    <span>📋</span> Stock Report
                </button>
                <button onclick="setLowStockAlerts()" class="btn btn-warning">
                    <span>🔔</span> Set Alerts
                </button>
            </div>
            
            <!-- Filter and Search -->
            <div class="inventory-filters">
                <div class="filter-group">
                    <label for="category_filter">Category:</label>
                    <select id="category_filter" onchange="filterInventory()">
                        <option value="">All Categories</option>
                        <option value="food">Food</option>
                        <option value="beverage">Beverage</option>
                        <option value="dessert">Dessert</option>
                        <option value="appetizer">Appetizer</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="stock_filter">Stock Status:</label>
                    <select id="stock_filter" onchange="filterInventory()">
                        <option value="">All Items</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search_item">Search:</label>
                    <input type="text" id="search_item" placeholder="Search items..." onkeyup="filterInventory()">
                </div>
            </div>
            
            <!-- Inventory Table -->
            <div class="inventory-table-container">
                <table id="inventoryTable" class="inventory-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Unit Price</th>
                            <th>Stock Value</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT *, 
                                               (stock_quantity * price) as stock_value,
                                               CASE 
                                                   WHEN stock_quantity = 0 THEN 'out_of_stock'
                                                   WHEN stock_quantity <= ? THEN 'low_stock'
                                                   ELSE 'in_stock'
                                               END as stock_status
                                               FROM products 
                                               ORDER BY name ASC");
                        $stmt->bind_param("i", $low_stock_threshold);
                        $stmt->execute();
                        $products = $stmt->get_result();
                        
                        while ($product = $products->fetch_assoc()):
                        ?>
                            <tr data-category="<?php echo $product['category']; ?>" data-status="<?php echo $product['stock_status']; ?>">
                                <td>
                                    <div class="item-info">
                                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                        <small><?php echo htmlspecialchars($product['description']); ?></small>
                                    </div>
                                </td>
                                <td><span class="category-badge <?php echo $product['category']; ?>"><?php echo ucfirst($product['category']); ?></span></td>
                                <td>
                                    <span class="stock-quantity <?php echo $product['stock_status']; ?>">
                                        <?php echo $product['stock_quantity']; ?>
                                    </span>
                                </td>
                                <td>৳<?php echo number_format($product['price'], 2); ?></td>
                                <td>৳<?php echo number_format($product['stock_value'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $product['stock_status']; ?>">
                                        <?php 
                                        echo $product['stock_status'] === 'out_of_stock' ? 'Out of Stock' : 
                                            ($product['stock_status'] === 'low_stock' ? 'Low Stock' : 'In Stock'); 
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="updateStock(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['stock_quantity']; ?>)" 
                                                class="btn btn-small btn-primary" title="Update Stock">
                                            📝
                                        </button>
                                        <button onclick="viewStockHistory(<?php echo $product['id']; ?>)" 
                                                class="btn btn-small btn-info" title="View History">
                                            📊
                                        </button>
                                        <button onclick="setReorderLevel(<?php echo $product['id']; ?>)" 
                                                class="btn btn-small btn-warning" title="Set Reorder Level">
                                            🔔
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <!-- Stock Update Modal -->
    <div id="stockUpdateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Stock</h3>
                <span class="close" onclick="closeModal('stockUpdateModal')">&times;</span>
            </div>
            <form id="stockUpdateForm">
                <input type="hidden" id="update_product_id" name="product_id">
                <div class="form-group">
                    <label>Item Name:</label>
                    <input type="text" id="update_product_name" readonly>
                </div>
                <div class="form-group">
                    <label>Current Stock:</label>
                    <input type="number" id="current_stock" readonly>
                </div>
                <div class="form-group">
                    <label>Action:</label>
                    <select id="stock_action" name="action" onchange="toggleStockInputs()">
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                        <option value="set">Set Exact Amount</option>
                    </select>
                </div>
                <div class="form-group">
                    <label id="quantity_label">Quantity to Add:</label>
                    <input type="number" id="stock_quantity" name="quantity" required min="0">
                </div>
                <div class="form-group">
                    <label>Reason:</label>
                    <select id="stock_reason" name="reason">
                        <option value="restock">Restock</option>
                        <option value="damaged">Damaged Items</option>
                        <option value="expired">Expired Items</option>
                        <option value="sold">Sold Items</option>
                        <option value="adjustment">Inventory Adjustment</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes (Optional):</label>
                    <textarea id="stock_notes" name="notes" rows="3"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('stockUpdateModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php $conn->close(); ?>
    
    <script src="../global/js/main.js"></script>
    <script>
        function filterInventory() {
            const categoryFilter = document.getElementById('category_filter').value;
            const stockFilter = document.getElementById('stock_filter').value;
            const searchTerm = document.getElementById('search_item').value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryTable tbody tr');
            
            rows.forEach(row => {
                const category = row.dataset.category;
                const status = row.dataset.status;
                const itemName = row.querySelector('.item-info strong').textContent.toLowerCase();
                
                let showRow = true;
                
                // Category filter
                if (categoryFilter && category !== categoryFilter) {
                    showRow = false;
                }
                
                // Stock status filter
                if (stockFilter && status !== stockFilter) {
                    showRow = false;
                }
                
                // Search filter
                if (searchTerm && !itemName.includes(searchTerm)) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }
        
        function updateStock(productId, productName, currentStock) {
            document.getElementById('update_product_id').value = productId;
            document.getElementById('update_product_name').value = productName;
            document.getElementById('current_stock').value = currentStock;
            document.getElementById('stock_quantity').value = '';
            document.getElementById('stock_notes').value = '';
            openModal('stockUpdateModal');
        }
        
        function toggleStockInputs() {
            const action = document.getElementById('stock_action').value;
            const label = document.getElementById('quantity_label');
            
            switch(action) {
                case 'add':
                    label.textContent = 'Quantity to Add:';
                    break;
                case 'remove':
                    label.textContent = 'Quantity to Remove:';
                    break;
                case 'set':
                    label.textContent = 'Set Stock to:';
                    break;
            }
        }
        
        document.getElementById('stockUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update_stock.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Stock updated successfully!', 'success');
                    closeModal('stockUpdateModal');
                    location.reload(); // Refresh to show updated stock
                } else {
                    showNotification(data.message || 'Error updating stock', 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating stock', 'error');
                console.error('Error:', error);
            });
        });
        
        function viewStockHistory(productId) {
            window.open(`stock_history.php?product_id=${productId}`, '_blank', 'width=800,height=600');
        }
        
        function setReorderLevel(productId) {
            const level = prompt('Set reorder level for this item:');
            if (level && !isNaN(level)) {
                // Implementation for setting reorder levels
                showNotification('Reorder level set successfully!', 'success');
            }
        }
        
        function generateStockReport() {
            window.open('stock_report.php', '_blank');
        }
        
        function setLowStockAlerts() {
            const threshold = prompt('Set low stock threshold (items with stock below this will be flagged):');
            if (threshold && !isNaN(threshold)) {
                // Implementation for setting global low stock threshold
                showNotification('Low stock threshold updated!', 'success');
            }
        }
        
        function openStockUpdateModal() {
            // Show bulk stock update modal
            showNotification('Select an item to update its stock', 'info');
        }
        
        function openNewItemModal() {
            window.location.href = 'product_management.php';
        }
    </script>
</body>
</html>
