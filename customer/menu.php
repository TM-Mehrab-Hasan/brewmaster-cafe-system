<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Cafe Management</title>
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
    ?>
    <header>
        <h2>Our Menu</h2>
        <nav class="customer-nav">
            <a href="index.php">Home</a>
            <a href="my_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="customer_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="menu-filters">
            <button class="filter-btn active" onclick="filterCategory('all')">All</button>
            <button class="filter-btn" onclick="filterCategory('Coffee')">Coffee</button>
            <button class="filter-btn" onclick="filterCategory('Tea')">Tea</button>
            <button class="filter-btn" onclick="filterCategory('Snacks')">Snacks</button>
            <button class="filter-btn" onclick="filterCategory('Desserts')">Desserts</button>
            <button class="filter-btn" onclick="filterCategory('Breakfast')">Breakfast</button>
            <button class="filter-btn" onclick="filterCategory('Beverages')">Beverages</button>
        </div>
        
        <div class="cart-summary">
            <div class="cart-icon" onclick="toggleCart()">
                🛒 Cart (<span id="cart-count">0</span>) - ৳<span id="cart-total">0.00</span>
            </div>
        </div>
        
        <div id="cart-sidebar" class="cart-sidebar">
            <div class="cart-header">
                <h3>Your Cart</h3>
                <button onclick="toggleCart()" class="close-cart">×</button>
            </div>
            <div id="cart-items"></div>
            <div class="cart-footer">
                <div class="cart-total">Total: ৳<span id="sidebar-total">0.00</span></div>
                <button onclick="checkout()" class="btn btn-primary">Checkout</button>
            </div>
        </div>
        
        <?php
        $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 ORDER BY category, name");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = [];
        while ($product = $result->fetch_assoc()) {
            $categories[$product['category']][] = $product;
        }
        ?>
        
        <div class="menu-sections">
            <?php foreach ($categories as $category => $products): ?>
                <div class="category-section" data-category="<?php echo $category; ?>">
                    <h3 class="category-title"><?php echo $category; ?></h3>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card" data-category="<?php echo $product['category']; ?>">
                                <?php if ($product['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php endif; ?>
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="price">৳<?php echo number_format($product['price'], 2); ?></p>
                                <div class="quantity-controls">
                                    <button onclick="decreaseQuantity(<?php echo $product['id']; ?>)">-</button>
                                    <span id="qty-<?php echo $product['id']; ?>">0</span>
                                    <button onclick="increaseQuantity(<?php echo $product['id']; ?>)">+</button>
                                </div>
                                <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)" class="btn btn-primary">Add to Cart</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php
        $stmt->close();
        $conn->close();
        ?>
    </main>
    <script src="../global/js/main.js"></script>
    <script src="../global/js/cart.js"></script>
</body>
</html>
