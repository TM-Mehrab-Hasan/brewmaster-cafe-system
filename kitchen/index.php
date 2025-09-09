<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Dashboard - BrewMaster Cafe System</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <link rel="stylesheet" href="../global/css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Kitchen-specific styles */
        .order-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #ed8936;
        }
        
        .order-card.urgent {
            border-left-color: #f56565;
            animation: pulse 2s infinite;
        }
        
        .order-card.preparing {
            border-left-color: #48bb78;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .order-number {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }
        
        .order-time {
            background: #e2e8f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .order-items {
            margin-bottom: 15px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn.start {
            background: #48bb78;
            color: white;
        }
        
        .action-btn.complete {
            background: #667eea;
            color: white;
        }
        
        .action-btn.delay {
            background: #ed8936;
            color: white;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['kitchen_staff_id'])) {
        header('Location: kitchen_login.php');
        exit;
    }
    ?>

    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h1><i class="fas fa-utensils"></i> Kitchen Dashboard</h1>
            <p class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['kitchen_staff_name']); ?>! - Focus on food preparation and order fulfillment</p>
        </div>
        <div class="header-right">
            <div class="header-actions">
                <a href="#" onclick="refreshOrders()" class="header-btn customer">
                    <i class="fas fa-sync-alt"></i>
                    Refresh Orders
                </a>
                <a href="kitchen_logout.php" class="header-btn logout">
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
            <div class="stat-card customer">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number" id="pendingOrders">-</div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card customer">
                <div class="stat-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="stat-number" id="preparingOrders">-</div>
                <div class="stat-label">In Preparation</div>
            </div>
            <div class="stat-card customer">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number" id="completedToday">-</div>
                <div class="stat-label">Completed Today</div>
            </div>
            <div class="stat-card customer">
                <div class="stat-icon">
                    <i class="fas fa-stopwatch"></i>
                </div>
                <div class="stat-number" id="avgPrepTime">-</div>
                <div class="stat-label">Avg. Prep Time</div>
            </div>
        </div>

        <!-- Active Orders Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-clipboard-list"></i>
                    Active Orders
                </h2>
                <button onclick="refreshOrders()" class="quick-btn customer">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
            
            <div id="ordersContainer">
                <!-- Sample orders - would be loaded from database -->
                <div class="order-card urgent">
                    <div class="order-header">
                        <span class="order-number">#1023</span>
                        <span class="order-time">5 min ago</span>
                    </div>
                    <div class="order-items">
                        <div class="order-item">
                            <span>2x Cappuccino</span>
                            <span>Hot</span>
                        </div>
                        <div class="order-item">
                            <span>1x Blueberry Muffin</span>
                            <span>Fresh</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="action-btn start" onclick="startOrder(1023)">
                            <i class="fas fa-play"></i> Start Preparing
                        </button>
                        <button class="action-btn delay" onclick="delayOrder(1023)">
                            <i class="fas fa-clock"></i> Report Delay
                        </button>
                    </div>
                </div>

                <div class="order-card preparing">
                    <div class="order-header">
                        <span class="order-number">#1024</span>
                        <span class="order-time">8 min ago</span>
                    </div>
                    <div class="order-items">
                        <div class="order-item">
                            <span>1x Latte</span>
                            <span>Extra Hot</span>
                        </div>
                        <div class="order-item">
                            <span>1x Croissant</span>
                            <span>Warmed</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="action-btn complete" onclick="completeOrder(1024)">
                            <i class="fas fa-check"></i> Mark Complete
                        </button>
                        <button class="action-btn delay" onclick="delayOrder(1024)">
                            <i class="fas fa-clock"></i> Need More Time
                        </button>
                    </div>
                </div>

                <div class="order-card">
                    <div class="order-header">
                        <span class="order-number">#1025</span>
                        <span class="order-time">2 min ago</span>
                    </div>
                    <div class="order-items">
                        <div class="order-item">
                            <span>3x Espresso</span>
                            <span>Double Shot</span>
                        </div>
                        <div class="order-item">
                            <span>2x Danish Pastry</span>
                            <span>Fresh</span>
                        </div>
                        <div class="order-item">
                            <span>1x Hot Chocolate</span>
                            <span>Extra Marshmallows</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="action-btn start" onclick="startOrder(1025)">
                            <i class="fas fa-play"></i> Start Preparing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../global/js/main.js"></script>
    <script>
        // Load dashboard statistics
        function loadStats() {
            // Sample data - would come from backend
            document.getElementById('pendingOrders').textContent = '3';
            document.getElementById('preparingOrders').textContent = '1';
            document.getElementById('completedToday').textContent = '27';
            document.getElementById('avgPrepTime').textContent = '4.2m';
            
            animateCounters();
        }

        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                if (counter.textContent.includes('m')) return; // Skip time values
                
                const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                let current = 0;
                const increment = target / 20;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target.toString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toString();
                    }
                }, 50);
            });
        }

        function startOrder(orderNumber) {
            const orderCard = event.target.closest('.order-card');
            orderCard.classList.remove('urgent');
            orderCard.classList.add('preparing');
            
            // Update action buttons
            const actionsDiv = orderCard.querySelector('.order-actions');
            actionsDiv.innerHTML = `
                <button class="action-btn complete" onclick="completeOrder(${orderNumber})">
                    <i class="fas fa-check"></i> Mark Complete
                </button>
                <button class="action-btn delay" onclick="delayOrder(${orderNumber})">
                    <i class="fas fa-clock"></i> Need More Time
                </button>
            `;
            
            showNotification('success', `Order #${orderNumber} preparation started!`);
            playNotificationSound();
        }

        function completeOrder(orderNumber) {
            const orderCard = event.target.closest('.order-card');
            
            // Fade out and remove the order
            orderCard.style.transition = 'all 0.5s ease';
            orderCard.style.opacity = '0';
            orderCard.style.transform = 'translateX(100px)';
            
            setTimeout(() => {
                orderCard.remove();
                updateStats();
            }, 500);
            
            showNotification('success', `Order #${orderNumber} completed and ready for pickup!`);
            playNotificationSound();
        }

        function delayOrder(orderNumber) {
            const reason = prompt('Reason for delay (optional):');
            const delay = prompt('Estimated additional time (minutes):', '5');
            
            if (delay) {
                showNotification('info', `Order #${orderNumber} delayed by ${delay} minutes. Staff will be notified.`);
                // In real implementation, this would notify the front staff
            }
        }

        function refreshOrders() {
            showNotification('info', 'Refreshing order list...');
            
            // Simulate refresh
            setTimeout(() => {
                loadStats();
                showNotification('success', 'Orders refreshed successfully!');
            }, 1000);
        }

        function updateStats() {
            // Update the completed count
            const completedElement = document.getElementById('completedToday');
            const current = parseInt(completedElement.textContent);
            completedElement.textContent = (current + 1).toString();
            
            // Update pending count
            const pendingElement = document.getElementById('pendingOrders');
            const currentPending = parseInt(pendingElement.textContent);
            if (currentPending > 0) {
                pendingElement.textContent = (currentPending - 1).toString();
            }
        }

        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `message ${type}`;
            notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i> <span>${message}</span>`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '1000';
            notification.style.maxWidth = '350px';
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 4000);
        }

        function playNotificationSound() {
            // Create audio context for notification sound
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (e) {
                console.log('Audio notification not available');
            }
        }

        // Load stats when page loads
        document.addEventListener('DOMContentLoaded', loadStats);

        // Auto-refresh orders every minute
        setInterval(refreshOrders, 60000);

        // Check for new orders every 15 seconds
        setInterval(() => {
            // In real implementation, this would check for new orders
            if (Math.random() > 0.8) { // 20% chance of new order
                showNotification('info', 'New order received! Check the order list.');
                playNotificationSound();
            }
        }, 15000);
    </script>
</body>
</html>
