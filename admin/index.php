<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BrewMaster Cafe System</title>
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
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: admin_login.php');
        exit;
    }
    ?>

    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h1><i class="fas fa-crown"></i> Admin Control Center</h1>
            <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! Complete system oversight and management.</p>
        </div>
        <div class="header-right">
            <div class="header-actions">
                <a href="../index.html" class="header-btn">
                    <i class="fas fa-home"></i>
                    Home
                </a>
                <a href="reports.php" class="header-btn">
                    <i class="fas fa-chart-line"></i>
                    Quick Reports
                </a>
                <a href="admin_logout.php" class="header-btn logout">
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
            <div class="stat-card admin">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number" id="staffCount">-</div>
                <div class="stat-label">Total Staff</div>
            </div>
            <div class="stat-card admin">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-number" id="orderCount">-</div>
                <div class="stat-label">Today's Orders</div>
            </div>
            <div class="stat-card admin">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-number" id="revenueCount">-</div>
                <div class="stat-label">Today's Revenue</div>
            </div>
            <div class="stat-card admin">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-number" id="productCount">-</div>
                <div class="stat-label">Active Products</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div class="quick-actions-grid">
                <a href="staff_approval.php" class="quick-btn">
                    <i class="fas fa-user-check"></i>
                    Approve Staff
                </a>
                <a href="product_management.php" class="quick-btn">
                    <i class="fas fa-plus-circle"></i>
                    Add Product
                </a>
                <a href="inventory.php" class="quick-btn">
                    <i class="fas fa-warehouse"></i>
                    Check Stock
                </a>
                <a href="reports.php" class="quick-btn">
                    <i class="fas fa-download"></i>
                    Export Reports
                </a>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-cogs"></i>
                    System Management
                </h2>
            </div>
            <div class="menu-grid">
                <a href="staff_approval.php" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <h3>Staff Approval</h3>
                            <p>Review and approve staff registration requests</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Pending Review</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="staff_management.php" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h3>Staff Management</h3>
                            <p>View, modify, and manage staff accounts</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">User Management</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="product_management.php" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div>
                            <h3>Product Management</h3>
                            <p>Add, edit, and manage menu items and pricing</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Menu Control</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="inventory.php" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div>
                            <h3>Inventory Management</h3>
                            <p>Track stock levels and manage inventory</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Stock Control</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="order_management.php" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3>Order Management</h3>
                            <p>View and manage all customer orders</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Order Control</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="reports.php" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h3>Reports & Analytics</h3>
                            <p>View sales reports, revenue analytics, and insights</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Business Intelligence</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- System Actions -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tools"></i>
                    System Actions
                </h2>
            </div>
            <div class="menu-grid">
                <a href="#" onclick="showBackup()" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <h3>Backup System</h3>
                            <p>Create system backups and data exports</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Maintenance</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="#" onclick="showSettings()" class="menu-item">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div>
                            <h3>System Settings</h3>
                            <p>Configure system preferences and settings</p>
                        </div>
                    </div>
                    <div class="menu-item-footer">
                        <span class="menu-badge">Configuration</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </div>
                </a>

                <a href="admin_logout.php" class="menu-item danger">
                    <div class="menu-item-header">
                        <div class="menu-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div>
                            <h3>Logout</h3>
                            <p>Securely exit the admin control panel</p>
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
                document.getElementById('staffCount').textContent = '12';
                document.getElementById('orderCount').textContent = '47';
                document.getElementById('revenueCount').textContent = '$1,234';
                document.getElementById('productCount').textContent = '28';
                
                // Add animation to counters
                animateCounters();
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                let current = 0;
                const increment = target / 50;
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

        function showBackup() {
            alert('Backup System:\n\n• Database backup\n• File system backup\n• Automated daily backups\n• Export data to CSV\n\nFeature coming soon!');
        }

        function showSettings() {
            alert('System Settings:\n\n• Cafe information\n• Operating hours\n• Tax settings\n• Email notifications\n• Security settings\n\nFeature coming soon!');
        }

        // Load stats when page loads
        document.addEventListener('DOMContentLoaded', loadStats);

        // Auto-refresh stats every 5 minutes
        setInterval(loadStats, 300000);
    </script>
</body>
</html>
