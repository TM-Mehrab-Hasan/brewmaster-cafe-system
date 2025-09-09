<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: admin_login.php');
        exit;
    }
    
    include '../global/php/db_connect.php';
    ?>
    <header>
        <h2>Product Management</h2>
    </header>
    <main>
        <div class="product-management">
            <div class="add-product-form">
                <h3>Add New Product</h3>
                <form action="add_product_process.php" method="POST" class="form-box">
                    <label for="product_name">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" required>
                    
                    <label for="product_description">Description:</label>
                    <textarea id="product_description" name="product_description" rows="3"></textarea>
                    
                    <label for="product_price">Price (BDT):</label>
                    <input type="number" id="product_price" name="product_price" step="0.01" required>
                    
                    <label for="product_category">Category:</label>
                    <select id="product_category" name="product_category" required>
                        <option value="">Select Category</option>
                        <option value="Coffee">Coffee</option>
                        <option value="Tea">Tea</option>
                        <option value="Snacks">Snacks</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Beverages">Beverages</option>
                    </select>
                    
                    <label for="product_image">Image URL:</label>
                    <input type="url" id="product_image" name="product_image">
                    
                    <button type="submit">Add Product</button>
                </form>
            </div>
            
            <div class="products-list">
                <h3>Existing Products</h3>
                <?php
                $stmt = $conn->prepare("SELECT * FROM products ORDER BY category, name");
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                
                <div class="products-grid">
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <div class="product-card">
                            <?php if ($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php endif; ?>
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price">৳<?php echo number_format($product['price'], 2); ?></p>
                            <p class="status">Status: <?php echo $product['is_available'] ? 'Available' : 'Unavailable'; ?></p>
                            
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-edit">Edit</a>
                                <form action="toggle_product_status.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-toggle">
                                        <?php echo $product['is_available'] ? 'Disable' : 'Enable'; ?>
                                    </button>
                                </form>
                                <form action="delete_product.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php
                $stmt->close();
                $conn->close();
                ?>
            </div>
        </div>
        
        <p><a href="index.php" class="back-link">Back to Admin Dashboard</a></p>
    </main>
    <script src="../global/js/main.js"></script>
</body>
</html>
