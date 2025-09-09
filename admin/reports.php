<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <h2>Reports & Analytics Dashboard</h2>
        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="staff_approval.php">Staff Approval</a>
            <a href="product_management.php">Products</a>
            <a href="admin_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="analytics-container">
            <!-- Key Metrics Cards -->
            <div class="metrics-grid">
                <?php
                // Get today's sales
                $today = date('Y-m-d');
                $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as today_sales FROM orders WHERE DATE(order_date) = ? AND status = 'completed'");
                $stmt->bind_param("s", $today);
                $stmt->execute();
                $today_sales = $stmt->get_result()->fetch_assoc()['today_sales'];
                $stmt->close();
                
                // Get total orders today
                $stmt = $conn->prepare("SELECT COUNT(*) as today_orders FROM orders WHERE DATE(order_date) = ?");
                $stmt->bind_param("s", $today);
                $stmt->execute();
                $today_orders = $stmt->get_result()->fetch_assoc()['today_orders'];
                $stmt->close();
                
                // Get total customers
                $stmt = $conn->prepare("SELECT COUNT(*) as total_customers FROM customers");
                $stmt->execute();
                $total_customers = $stmt->get_result()->fetch_assoc()['total_customers'];
                $stmt->close();
                
                // Get pending orders
                $stmt = $conn->prepare("SELECT COUNT(*) as pending_orders FROM orders WHERE status IN ('pending', 'confirmed', 'preparing')");
                $stmt->execute();
                $pending_orders = $stmt->get_result()->fetch_assoc()['pending_orders'];
                $stmt->close();
                ?>
                
                <div class="metric-card sales">
                    <div class="metric-icon">💰</div>
                    <div class="metric-content">
                        <h3>Today's Sales</h3>
                        <p class="metric-value">৳<?php echo number_format($today_sales, 2); ?></p>
                    </div>
                </div>
                
                <div class="metric-card orders">
                    <div class="metric-icon">📋</div>
                    <div class="metric-content">
                        <h3>Today's Orders</h3>
                        <p class="metric-value"><?php echo $today_orders; ?></p>
                    </div>
                </div>
                
                <div class="metric-card customers">
                    <div class="metric-icon">👥</div>
                    <div class="metric-content">
                        <h3>Total Customers</h3>
                        <p class="metric-value"><?php echo $total_customers; ?></p>
                    </div>
                </div>
                
                <div class="metric-card pending">
                    <div class="metric-icon">⏳</div>
                    <div class="metric-content">
                        <h3>Pending Orders</h3>
                        <p class="metric-value"><?php echo $pending_orders; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- Daily Sales Chart -->
                <div class="chart-container">
                    <h3>Last 7 Days Sales</h3>
                    <canvas id="dailySalesChart"></canvas>
                </div>
                
                <!-- Product Categories Chart -->
                <div class="chart-container">
                    <h3>Sales by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
                
                <!-- Order Status Chart -->
                <div class="chart-container">
                    <h3>Order Status Distribution</h3>
                    <canvas id="orderStatusChart"></canvas>
                </div>
                
                <!-- Top Products Chart -->
                <div class="chart-container">
                    <h3>Top Selling Products</h3>
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
            
            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h3>Recent Orders</h3>
                <div class="table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT o.id, o.order_date, o.total_amount, o.status, c.name as customer_name 
                                                   FROM orders o 
                                                   JOIN customers c ON o.customer_id = c.id 
                                                   ORDER BY o.order_date DESC 
                                                   LIMIT 10");
                            $stmt->execute();
                            $recent_orders = $stmt->get_result();
                            
                            while ($order = $recent_orders->fetch_assoc()):
                            ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                    <td>৳<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <script src="../global/js/main.js"></script>
    <script>
        // Fetch chart data and render charts
        fetch('get_chart_data.php')
            .then(response => response.json())
            .then(data => {
                renderCharts(data);
            });
        
        function renderCharts(data) {
            // Daily Sales Chart
            const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(dailySalesCtx, {
                type: 'line',
                data: {
                    labels: data.dailySales.labels,
                    datasets: [{
                        label: 'Sales (৳)',
                        data: data.dailySales.values,
                        borderColor: '#6a11cb',
                        backgroundColor: 'rgba(106, 17, 203, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Category Sales Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: data.categories.labels,
                    datasets: [{
                        data: data.categories.values,
                        backgroundColor: [
                            '#6a11cb', '#2575fc', '#ff4757', '#2ed573', 
                            '#ffa502', '#70a1ff', '#5f27cd', '#ff9ff3'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: data.orderStatus.labels,
                    datasets: [{
                        label: 'Orders',
                        data: data.orderStatus.values,
                        backgroundColor: [
                            '#ffa502', '#70a1ff', '#ff6b7a', '#2ed573', '#5f27cd', '#747d8c'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Top Products Chart
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            new Chart(topProductsCtx, {
                type: 'horizontalBar',
                data: {
                    labels: data.topProducts.labels,
                    datasets: [{
                        label: 'Quantity Sold',
                        data: data.topProducts.values,
                        backgroundColor: '#6a11cb'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
