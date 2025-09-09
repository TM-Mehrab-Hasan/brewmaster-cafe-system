<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Staff Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['staff_id'])) {
        header('Location: staff_login.php');
        exit;
    }
    
    include '../global/php/db_connect.php';
    $staff_id = $_SESSION['staff_id'];
    
    // Get date range (default to today)
    $start_date = $_GET['start_date'] ?? date('Y-m-d');
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    ?>
    <header>
        <h2>Sales Report</h2>
        <nav class="staff-nav">
            <a href="index.php">Dashboard</a>
            <a href="order_management.php">Orders</a>
            <a href="payment_processing.php">Payments</a>
            <a href="staff_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="sales-report-container">
            <!-- Date Filter -->
            <div class="report-filters">
                <form method="GET" class="filter-form">
                    <div class="date-inputs">
                        <div class="input-group">
                            <label for="start_date">From Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                        </div>
                        <div class="input-group">
                            <label for="end_date">To Date:</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                    
                    <div class="quick-filters">
                        <button type="button" onclick="setDateRange('today')" class="btn btn-secondary">Today</button>
                        <button type="button" onclick="setDateRange('yesterday')" class="btn btn-secondary">Yesterday</button>
                        <button type="button" onclick="setDateRange('week')" class="btn btn-secondary">This Week</button>
                        <button type="button" onclick="setDateRange('month')" class="btn btn-secondary">This Month</button>
                    </div>
                </form>
            </div>
            
            <!-- Sales Summary -->
            <div class="sales-summary">
                <?php
                // Get staff sales data
                $stmt = $conn->prepare("SELECT 
                                       COUNT(p.id) as total_transactions,
                                       COALESCE(SUM(p.amount), 0) as total_sales,
                                       COALESCE(AVG(p.amount), 0) as avg_transaction,
                                       COUNT(DISTINCT o.customer_id) as unique_customers
                                       FROM payments p 
                                       JOIN orders o ON p.order_id = o.id 
                                       WHERE p.staff_id = ? 
                                       AND DATE(p.payment_date) BETWEEN ? AND ?");
                $stmt->bind_param("iss", $staff_id, $start_date, $end_date);
                $stmt->execute();
                $summary = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                
                // Get payment method breakdown
                $stmt = $conn->prepare("SELECT payment_method, COUNT(*) as count, SUM(amount) as total 
                                       FROM payments 
                                       WHERE staff_id = ? 
                                       AND DATE(payment_date) BETWEEN ? AND ? 
                                       GROUP BY payment_method 
                                       ORDER BY total DESC");
                $stmt->bind_param("iss", $staff_id, $start_date, $end_date);
                $stmt->execute();
                $payment_methods = $stmt->get_result();
                ?>
                
                <div class="summary-cards">
                    <div class="summary-card total-sales">
                        <div class="card-icon">💰</div>
                        <div class="card-content">
                            <h3>Total Sales</h3>
                            <p class="card-value">৳<?php echo number_format($summary['total_sales'], 2); ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-card transactions">
                        <div class="card-icon">🧾</div>
                        <div class="card-content">
                            <h3>Transactions</h3>
                            <p class="card-value"><?php echo $summary['total_transactions']; ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-card avg-sale">
                        <div class="card-icon">📊</div>
                        <div class="card-content">
                            <h3>Avg. Transaction</h3>
                            <p class="card-value">৳<?php echo number_format($summary['avg_transaction'], 2); ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-card customers">
                        <div class="card-icon">👥</div>
                        <div class="card-content">
                            <h3>Customers Served</h3>
                            <p class="card-value"><?php echo $summary['unique_customers']; ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods Breakdown -->
                <div class="payment-breakdown">
                    <h3>Payment Methods</h3>
                    <div class="payment-methods-grid">
                        <?php while ($method = $payment_methods->fetch_assoc()): ?>
                            <div class="payment-method-card">
                                <h4><?php echo ucfirst($method['payment_method']); ?></h4>
                                <p class="method-count"><?php echo $method['count']; ?> transactions</p>
                                <p class="method-total">৳<?php echo number_format($method['total'], 2); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <?php $stmt->close(); ?>
            </div>
            
            <!-- Hourly Sales Chart -->
            <div class="charts-section">
                <div class="chart-container">
                    <h3>Sales by Hour</h3>
                    <canvas id="hourlySalesChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3>Payment Methods Distribution</h3>
                    <canvas id="paymentMethodsChart"></canvas>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="recent-transactions">
                <h3>Recent Transactions</h3>
                <?php
                $stmt = $conn->prepare("SELECT p.*, o.id as order_id, c.name as customer_name 
                                       FROM payments p 
                                       JOIN orders o ON p.order_id = o.id 
                                       JOIN customers c ON o.customer_id = c.id 
                                       WHERE p.staff_id = ? 
                                       AND DATE(p.payment_date) BETWEEN ? AND ? 
                                       ORDER BY p.payment_date DESC 
                                       LIMIT 20");
                $stmt->bind_param("iss", $staff_id, $start_date, $end_date);
                $stmt->execute();
                $transactions = $stmt->get_result();
                ?>
                
                <div class="transactions-table">
                    <table class="sales-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('h:i A', strtotime($transaction['payment_date'])); ?></td>
                                    <td>#<?php echo $transaction['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($transaction['customer_name']); ?></td>
                                    <td>৳<?php echo number_format($transaction['amount'], 2); ?></td>
                                    <td><span class="payment-badge <?php echo $transaction['payment_method']; ?>"><?php echo ucfirst($transaction['payment_method']); ?></span></td>
                                    <td>
                                        <button onclick="printReceipt(<?php echo $transaction['id']; ?>)" class="btn btn-small">Print</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php $stmt->close(); ?>
            </div>
            
            <!-- Export Options -->
            <div class="export-section">
                <h3>Export Report</h3>
                <div class="export-buttons">
                    <button onclick="exportToPDF()" class="btn btn-primary">Export to PDF</button>
                    <button onclick="exportToCSV()" class="btn btn-secondary">Export to CSV</button>
                    <button onclick="printReport()" class="btn btn-info">Print Report</button>
                </div>
            </div>
        </div>
    </main>
    
    <?php $conn->close(); ?>
    
    <script src="../global/js/main.js"></script>
    <script>
        // Load chart data
        fetch(`get_staff_chart_data.php?start_date=${encodeURIComponent('<?php echo $start_date; ?>')}&end_date=${encodeURIComponent('<?php echo $end_date; ?>')}`)
            .then(response => response.json())
            .then(data => {
                renderStaffCharts(data);
            });
        
        function renderStaffCharts(data) {
            // Hourly Sales Chart
            const hourlyCtx = document.getElementById('hourlySalesChart').getContext('2d');
            new Chart(hourlyCtx, {
                type: 'bar',
                data: {
                    labels: data.hourlySales.labels,
                    datasets: [{
                        label: 'Sales (৳)',
                        data: data.hourlySales.values,
                        backgroundColor: 'rgba(106, 17, 203, 0.8)',
                        borderColor: '#6a11cb',
                        borderWidth: 1
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
            
            // Payment Methods Chart
            const methodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
            new Chart(methodsCtx, {
                type: 'doughnut',
                data: {
                    labels: data.paymentMethods.labels,
                    datasets: [{
                        data: data.paymentMethods.values,
                        backgroundColor: [
                            '#6a11cb', '#2575fc', '#ff4757', '#2ed573', '#ffa502'
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
        }
        
        function setDateRange(period) {
            const today = new Date();
            let startDate, endDate;
            
            switch(period) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0];
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    startDate = endDate = yesterday.toISOString().split('T')[0];
                    break;
                case 'week':
                    const weekStart = new Date(today);
                    weekStart.setDate(today.getDate() - today.getDay());
                    startDate = weekStart.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
            }
            
            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
        }
        
        function printReceipt(paymentId) {
            window.open(`print_receipt.php?payment_id=${paymentId}`, '_blank', 'width=600,height=800');
        }
        
        function exportToPDF() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            window.open(`export_sales_pdf.php?start_date=${startDate}&end_date=${endDate}`, '_blank');
        }
        
        function exportToCSV() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            window.open(`export_sales_csv.php?start_date=${startDate}&end_date=${endDate}`, '_blank');
        }
        
        function printReport() {
            window.print();
        }
    </script>
</body>
</html>
