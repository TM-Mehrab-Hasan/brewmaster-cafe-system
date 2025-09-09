<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Cafe Management</title>
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
    $customer_id = $_SESSION['customer_id'];
    
    // Get customer details
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Get customer statistics
    $stmt = $conn->prepare("SELECT COUNT(*) as total_orders, 
                           COALESCE(SUM(total_amount), 0) as total_spent,
                           MAX(order_date) as last_order
                           FROM orders WHERE customer_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Get favorite products
    $stmt = $conn->prepare("SELECT p.name, p.price, SUM(oi.quantity) as order_count
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           JOIN orders o ON oi.order_id = o.id 
                           WHERE o.customer_id = ? AND o.status = 'completed'
                           GROUP BY p.id 
                           ORDER BY order_count DESC 
                           LIMIT 5");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $favorites = $stmt->get_result();
    ?>
    <header>
        <h2>My Profile</h2>
        <nav class="customer-nav">
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="my_orders.php">My Orders</a>
            <a href="customer_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="profile-container">
            <!-- Profile Summary -->
            <div class="profile-summary">
                <div class="profile-avatar">
                    <div class="avatar-circle">
                        <?php echo strtoupper(substr($customer['name'], 0, 2)); ?>
                    </div>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($customer['name']); ?></h2>
                    <p><?php echo htmlspecialchars($customer['email']); ?></p>
                    <p class="member-since">Member since <?php echo date('M Y', strtotime($customer['created_at'])); ?></p>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card orders">
                    <div class="stat-icon">📋</div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <p class="stat-value"><?php echo $stats['total_orders']; ?></p>
                    </div>
                </div>
                
                <div class="stat-card spending">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <h3>Total Spent</h3>
                        <p class="stat-value">৳<?php echo number_format($stats['total_spent'], 2); ?></p>
                    </div>
                </div>
                
                <div class="stat-card last-visit">
                    <div class="stat-icon">🕒</div>
                    <div class="stat-content">
                        <h3>Last Order</h3>
                        <p class="stat-value">
                            <?php 
                            if ($stats['last_order']) {
                                echo date('M d, Y', strtotime($stats['last_order']));
                            } else {
                                echo 'No orders yet';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="stat-card loyalty">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-content">
                        <h3>Loyalty Points</h3>
                        <p class="stat-value"><?php echo floor($stats['total_spent'] / 10); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Sections -->
            <div class="profile-sections">
                <!-- Personal Information -->
                <div class="profile-section">
                    <div class="section-header">
                        <h3>Personal Information</h3>
                        <button onclick="editProfile()" class="btn btn-primary">Edit Profile</button>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Full Name</label>
                            <p><?php echo htmlspecialchars($customer['name']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Email Address</label>
                            <p><?php echo htmlspecialchars($customer['email']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Phone Number</label>
                            <p><?php echo htmlspecialchars($customer['phone']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Member Since</label>
                            <p><?php echo date('F d, Y', strtotime($customer['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Favorite Products -->
                <div class="profile-section">
                    <div class="section-header">
                        <h3>Your Favorites</h3>
                    </div>
                    
                    <?php if ($favorites->num_rows > 0): ?>
                        <div class="favorites-list">
                            <?php while ($favorite = $favorites->fetch_assoc()): ?>
                                <div class="favorite-item">
                                    <div class="favorite-info">
                                        <h5><?php echo htmlspecialchars($favorite['name']); ?></h5>
                                        <p>৳<?php echo number_format($favorite['price'], 2); ?></p>
                                    </div>
                                    <div class="favorite-stats">
                                        <span class="order-count">Ordered <?php echo $favorite['order_count']; ?> times</span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-favorites">No favorite items yet. Start ordering to build your favorites!</p>
                    <?php endif; ?>
                </div>
                
                <!-- Account Actions -->
                <div class="profile-section">
                    <div class="section-header">
                        <h3>Account Actions</h3>
                    </div>
                    
                    <div class="actions-grid">
                        <button onclick="changePassword()" class="action-btn">
                            <span class="action-icon">🔒</span>
                            <span>Change Password</span>
                        </button>
                        
                        <button onclick="downloadData()" class="action-btn">
                            <span class="action-icon">📥</span>
                            <span>Download My Data</span>
                        </button>
                        
                        <button onclick="deleteAccount()" class="action-btn danger">
                            <span class="action-icon">🗑️</span>
                            <span>Delete Account</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Profile Modal -->
        <div id="editProfileModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Profile</h3>
                <form id="editProfileForm">
                    <div class="form-group">
                        <label for="edit_name">Full Name</label>
                        <input type="text" id="edit_name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_email">Email Address</label>
                        <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_phone">Phone Number</label>
                        <input type="text" id="edit_phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password Modal -->
        <div id="changePasswordModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closePasswordModal()">&times;</span>
                <h3>Change Password</h3>
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closePasswordModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <?php
    $stmt->close();
    $conn->close();
    ?>
    
    <script src="../global/js/main.js"></script>
    <script>
        function editProfile() {
            document.getElementById('editProfileModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }
        
        function changePassword() {
            document.getElementById('changePasswordModal').style.display = 'block';
        }
        
        function closePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'none';
        }
        
        // Handle profile edit form
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating profile: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating profile');
            });
        });
        
        // Handle password change form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match!');
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully!');
                    closePasswordModal();
                    this.reset();
                } else {
                    alert('Error changing password: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error changing password');
            });
        });
        
        function downloadData() {
            window.open('download_data.php', '_blank');
        }
        
        function deleteAccount() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
                    fetch('delete_account.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Account deleted successfully');
                            window.location.href = 'customer_register.php';
                        } else {
                            alert('Error deleting account: ' + data.message);
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
