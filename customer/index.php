<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal - Cafe Management</title>
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
        <h1>Welcome to Our Cafe, <?php echo $_SESSION['customer_name']; ?>!</h1>
        <nav class="customer-nav">
            <a href="menu.php">Menu</a>
            <a href="my_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="customer_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="welcome-section">
            <h2>Explore Our Delicious Menu</h2>
            <p>Discover fresh coffee, tasty snacks, and delightful desserts!</p>
            <a href="menu.php" class="cta-button">View Menu</a>
        </div>
        
        <div class="featured-products">
            <h3>Featured Items</h3>
            <?php
            $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 ORDER BY RAND() LIMIT 3");
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
                        <p class="price">৳<?php echo number_format($product['price'], 2); ?></p>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary">Add to Cart</button>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <?php
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </main>
    <script src="../global/js/main.js"></script>
    <script src="../global/js/cart.js"></script>
</body>
</html>
