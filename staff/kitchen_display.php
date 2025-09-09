<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - Staff Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <style>
        .kitchen-display {
            background: #1a1a1a;
            color: white;
            min-height: 100vh;
            padding: 1rem;
        }
        
        .kitchen-header {
            background: #2d3748;
            color: white;
            padding: 1rem;
            text-align: center;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        
        .orders-board {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .kitchen-order-card {
            background: #2d3748;
            border-radius: 10px;
            padding: 1.5rem;
            border-left: 5px solid;
            min-height: 200px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .kitchen-order-card.new { border-left-color: #ffa502; animation: pulse 2s infinite; }
        .kitchen-order-card.preparing { border-left-color: #ff6b7a; }
        .kitchen-order-card.ready { border-left-color: #2ed573; animation: glow 1s infinite alternate; }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 165, 2, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 165, 2, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 165, 2, 0); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(46, 213, 115, 0.5); }
            to { box-shadow: 0 0 30px rgba(46, 213, 115, 0.8); }
        }
        
        .order-number {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .order-time {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.9rem;
            color: #a0aec0;
        }
        
        .elapsed-time {
            position: absolute;
            top: 2.5rem;
            right: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .elapsed-time.warning { color: #ffa502; }
        .elapsed-time.danger { color: #ff4757; }
        
        .customer-info {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #4a5568;
        }
        
        .customer-name {
            font-weight: bold;
            color: #e2e8f0;
        }
        
        .order-items {
            margin: 1rem 0;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #4a5568;
        }
        
        .item-name {
            font-weight: 500;
            color: #e2e8f0;
        }
        
        .item-quantity {
            background: #4a5568;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .order-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }
        
        .kitchen-btn {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-start {
            background: #ffa502;
            color: white;
        }
        
        .btn-ready {
            background: #2ed573;
            color: white;
        }
        
        .btn-complete {
            background: #5f27cd;
            color: white;
        }
        
        .kitchen-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .status-badge {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-confirmed { background: #70a1ff; color: white; }
        .status-preparing { background: #ff6b7a; color: white; }
        .status-ready { background: #2ed573; color: white; }
        
        .refresh-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2ed573;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .no-orders {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: #a0aec0;
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="kitchen-display">
    <?php
    session_start();
    if (!isset($_SESSION['staff_id'])) {
        header('Location: staff_login.php');
        exit;
    }
    ?>
    
    <div class="kitchen-header">
        <h1>🍳 Kitchen Display System</h1>
        <p>Real-time Order Management | Last Updated: <span id="lastUpdate"></span></p>
        <div style="margin-top: 1rem;">
            <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="kitchen-btn" style="max-width: 200px;">Auto Refresh: ON</button>
            <button onclick="refreshOrders()" class="kitchen-btn" style="max-width: 150px; background: #6a11cb;">Refresh Now</button>
        </div>
    </div>
    
    <div class="refresh-indicator" id="refreshIndicator" style="display: none;">
        Updating...
    </div>
    
    <div class="orders-board" id="ordersBoard">
        <!-- Orders will be loaded here -->
    </div>
    
    <script>
        let autoRefresh = true;
        let refreshInterval;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
            startAutoRefresh();
        });
        
        function loadOrders() {
            document.getElementById('refreshIndicator').style.display = 'block';
            
            fetch('get_kitchen_orders.php')
                .then(response => response.json())
                .then(data => {
                    displayOrders(data);
                    updateLastRefreshTime();
                    document.getElementById('refreshIndicator').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error loading orders:', error);
                    document.getElementById('refreshIndicator').style.display = 'none';
                });
        }
        
        function displayOrders(orders) {
            const board = document.getElementById('ordersBoard');
            
            if (orders.length === 0) {
                board.innerHTML = '<div class="no-orders">🎉 No pending orders! Kitchen is up to date.</div>';
                return;
            }
            
            board.innerHTML = orders.map(order => createOrderCard(order)).join('');
            
            // Start timers for each order
            orders.forEach(order => {
                updateElapsedTime(order.id, order.order_date);
            });
        }
        
        function createOrderCard(order) {
            const statusClass = order.status === 'confirmed' ? 'new' : 
                               order.status === 'preparing' ? 'preparing' : 'ready';
            
            const items = order.items.map(item => `
                <div class="order-item">
                    <span class="item-name">${item.name}</span>
                    <span class="item-quantity">${item.quantity}x</span>
                </div>
            `).join('');
            
            const actions = order.status === 'confirmed' ? 
                `<button onclick="updateOrderStatus(${order.id}, 'preparing')" class="kitchen-btn btn-start">Start Cooking</button>` :
                order.status === 'preparing' ? 
                `<button onclick="updateOrderStatus(${order.id}, 'ready')" class="kitchen-btn btn-ready">Mark Ready</button>` :
                `<button onclick="updateOrderStatus(${order.id}, 'completed')" class="kitchen-btn btn-complete">Complete Order</button>`;
            
            return `
                <div class="kitchen-order-card ${statusClass}">
                    <div class="order-number">#${order.id}</div>
                    <div class="order-time">${formatTime(order.order_date)}</div>
                    <div class="elapsed-time" id="elapsed-${order.id}"></div>
                    
                    <div class="customer-info">
                        <div class="customer-name">${order.customer_name}</div>
                        <div style="color: #a0aec0; font-size: 0.9rem;">${order.customer_phone}</div>
                    </div>
                    
                    <div class="order-items">
                        ${items}
                    </div>
                    
                    <div class="order-actions">
                        ${actions}
                    </div>
                    
                    <div class="status-badge status-${order.status}">
                        ${order.status.toUpperCase()}
                    </div>
                </div>
            `;
        }
        
        function updateOrderStatus(orderId, newStatus) {
            fetch('../staff/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadOrders(); // Refresh the display
                    
                    // Play notification sound for ready orders
                    if (newStatus === 'ready') {
                        playNotificationSound();
                    }
                } else {
                    alert('Error updating order status');
                }
            });
        }
        
        function updateElapsedTime(orderId, orderDate) {
            const element = document.getElementById(`elapsed-${orderId}`);
            if (!element) return;
            
            const startTime = new Date(orderDate).getTime();
            
            const updateTimer = () => {
                const now = new Date().getTime();
                const elapsed = Math.floor((now - startTime) / 1000 / 60); // minutes
                
                element.textContent = `${elapsed}m`;
                
                // Color coding based on time
                if (elapsed > 20) {
                    element.className = 'elapsed-time danger';
                } else if (elapsed > 10) {
                    element.className = 'elapsed-time warning';
                } else {
                    element.className = 'elapsed-time';
                }
            };
            
            updateTimer();
            setInterval(updateTimer, 60000); // Update every minute
        }
        
        function formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function updateLastRefreshTime() {
            document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
        }
        
        function refreshOrders() {
            loadOrders();
        }
        
        function toggleAutoRefresh() {
            autoRefresh = !autoRefresh;
            const btn = document.getElementById('autoRefreshBtn');
            
            if (autoRefresh) {
                btn.textContent = 'Auto Refresh: ON';
                btn.style.background = '#2ed573';
                startAutoRefresh();
            } else {
                btn.textContent = 'Auto Refresh: OFF';
                btn.style.background = '#ff4757';
                stopAutoRefresh();
            }
        }
        
        function startAutoRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(loadOrders, 30000); // Refresh every 30 seconds
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
        
        function playNotificationSound() {
            // Create a simple notification sound
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
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'r' || e.key === 'R') {
                refreshOrders();
            } else if (e.key === 'a' || e.key === 'A') {
                toggleAutoRefresh();
            }
        });
    </script>
</body>
</html>
