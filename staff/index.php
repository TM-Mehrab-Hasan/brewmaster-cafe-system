<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - BrewMaster Cafe System</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <link rel="stylesheet" href="../global/css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['staff_id'])) {
        header('Location: staff_login.php');
        exit;
    }
    ?>

    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h1><i class="fas fa-user-tie"></i> Staff Dashboard</h1>
            <p class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['staff_name']); ?>! Ready to serve our customers with excellence.</p>
        </div>
        <div class="header-right">
            <div class="header-actions">
                <a href="../index.html" class="header-btn">
                    <i class="fas fa-home"></i>
                    Home
                </a>
                <a href="kitchen_display.php" class="header-btn staff">
                    <i class="fas fa-utensils"></i>
                    Kitchen Orders
                </a>
                <a href="staff_logout.php" class="header-btn logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card staff">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-number" id="todayOrders">-</div>
                <div class="stat-label">Today's Orders</div>
            </div>
            <div class="stat-card staff">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number" id="pendingOrders">-</div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card staff">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-number" id="todayRevenue">-</div>
                <div class="stat-label">Today's Revenue</div>
            </div>
            <div class="stat-card staff">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number" id="avgOrderTime">-</div>
                <div class="stat-label">Avg. Order Time</div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="message info" id="stockAlert" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Some items are running low on stock. Check inventory before taking orders.</span>
            <a href="#" onclick="checkInventory()" style="margin-left: auto; color: #5a67d8; font-weight: 600;">View Stock</a>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div class="quick-actions-grid">
                <a href="order_management.php" class="quick-btn staff">
                    <i class="fas fa-plus-circle"></i>
                    New Order
                </a>
                <a href="payment_processing.php" class="quick-btn staff">
                    <i class="fas fa-credit-card"></i>
                    Process Payment
                </a>
                <a href="kitchen_display.php" class="quick-btn staff">
                    <i class="fas fa-utensils"></i>
                    Kitchen Display
                </a>
                <a href="sales_report.php" class="quick-btn staff">
                    <i class="fas fa-chart-bar"></i>
                    View Reports
                </a>
            </div>
        </div>

        <!-- Order Management Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-clipboard-list"></i>
                    Order Management
                </h2>
            </div>
            <div class="menu-grid">
                <a href="order_management.php" class="menu-item staff">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3>Order Management</h3>
                            <p>View, create, and manage customer orders with real-time stock checking</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Order Control</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="payment_processing.php" class="menu-item staff">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h3>Payment Processing</h3>
                            <p>Process payments and generate receipts for orders</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Payments</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="kitchen_display.php" class="menu-item staff">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <h3>Kitchen Display</h3>
                            <p>Monitor kitchen orders and preparation status</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Kitchen Staff</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Reports & Profile Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-chart-line"></i>
                    Reports & Profile
                </h2>
            </div>
            <div class="menu-grid">
                <a href="sales_report.php" class="menu-item staff">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <h3>Sales Report</h3>
                            <p>View daily sales statistics and performance metrics</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Analytics</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="#" onclick="showInventory()" class="menu-item staff">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <h3>Stock Levels</h3>
                            <p>View current inventory levels for all menu items</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Inventory View</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="staff_profile.php" class="menu-item staff">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h3>My Profile</h3>
                            <p>View and edit your personal information and preferences</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Profile</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="staff_logout.php" class="menu-item danger">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div>
                            <h3>Logout</h3>
                            <p>Securely exit your staff session</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Exit</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="../global/js/main.js"></script>
    <script>
        // Load dashboard statistics
        async function loadStats() {
            try {
                // This would connect to your backend APIs
                // For now, showing sample data
                document.getElementById('todayOrders').textContent = '23';
                document.getElementById('pendingOrders').textContent = '5';
                document.getElementById('todayRevenue').textContent = '$567';
                document.getElementById('avgOrderTime').textContent = '12m';
                
                // Add animation to counters
                animateCounters();
                
                // Check for low stock alerts
                checkLowStock();
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                if (counter.textContent.includes('m')) return; // Skip time values
                
                const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                let current = 0;
                const increment = target / 30;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = counter.textContent.includes('$') ? 
                            '$' + target.toLocaleString() : target.toString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = counter.textContent.includes('$') ? 
                            '$' + Math.floor(current).toLocaleString() : Math.floor(current).toString();
                    }
                }, 50);
            });
        }

        function checkLowStock() {
            // Simulate checking for low stock items
            // In real implementation, this would call your inventory API
            const hasLowStock = Math.random() > 0.5; // Random for demo
            if (hasLowStock) {
                document.getElementById('stockAlert').style.display = 'flex';
            }
        }

        function showInventory() {
            // This would open a modal or navigate to inventory view
            alert('Current Stock Levels:\n\n• Coffee Beans: 15 kg (Low Stock!)\n• Milk: 25 liters\n• Sugar: 8 kg\n• Cups: 150 pieces\n• Pastries: 23 pieces\n\n⚠️ Coffee beans need restocking soon!');
        }

        function checkInventory() {
            showInventory();
            document.getElementById('stockAlert').style.display = 'none';
        }

        // Load stats when page loads
        document.addEventListener('DOMContentLoaded', loadStats);

        // Auto-refresh stats every 2 minutes for staff dashboard
        setInterval(loadStats, 120000);

        // Add notification for new orders (simulated)
        setInterval(() => {
            if (Math.random() > 0.9) { // 10% chance every check
                showNewOrderNotification();
            }
        }, 30000); // Check every 30 seconds

        function showNewOrderNotification() {
            // Create a temporary notification
            const notification = document.createElement('div');
            notification.className = 'message success';
            notification.innerHTML = '<i class="fas fa-bell"></i> <span>New order received! Check order management.</span>';
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '1000';
            notification.style.maxWidth = '300px';
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    </script>
</body>
</html>
