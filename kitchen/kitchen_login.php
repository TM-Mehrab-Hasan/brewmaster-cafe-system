<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Staff Login - BrewMaster Cafe System</title>
    <link rel="stylesheet" href="../global/css/style.css">
    <link rel="stylesheet" href="../global/css/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <!-- Floating Particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <!-- Back to Home Button -->
        <a href="../index.html" class="back-to-home">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <!-- Login Card -->
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1 class="auth-title">Kitchen Staff</h1>
                <p class="auth-subtitle">Food preparation and order fulfillment</p>
                <span class="role-indicator customer">Kitchen Portal</span>
            </div>

            <?php
            session_start();
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-error">';
                echo '<i class="fas fa-exclamation-triangle"></i> ';
                echo htmlspecialchars($_SESSION['error_message']);
                echo '</div>';
                unset($_SESSION['error_message']);
            }
            
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">';
                echo '<i class="fas fa-check-circle"></i> ';
                echo htmlspecialchars($_SESSION['success_message']);
                echo '</div>';
                unset($_SESSION['success_message']);
            }
            ?>

            <form action="kitchen_authenticate.php" method="POST" class="auth-form" id="kitchenLoginForm">
                <div class="form-group">
                    <label for="kitchen_id">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="kitchen_id" name="kitchen_id" class="form-input" 
                           placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label for="kitchen_password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="kitchen_password" name="kitchen_password" class="form-input" 
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('kitchen_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="info-notice">
                    <i class="fas fa-info-circle"></i>
                    <span>Kitchen staff access is provided by admin. Contact management if you don't have credentials.</span>
                </div>

                <button type="submit" class="auth-button customer" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Access Kitchen Display
                </button>
            </form>

            <div class="auth-links">
                <p>Need access? <a href="#" onclick="showKitchenInfo()"><strong>Contact Admin</strong></a></p>
                <p><a href="../index.html">← Back to Portal Selection</a></p>
            </div>
        </div>
    </div>

    <script src="../global/js/main.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function showKitchenInfo() {
            alert('Kitchen Staff Access:\n\n• Kitchen staff accounts are created by admin\n• Use your registered email and password\n• Access is limited to order preparation functions\n• Contact your manager for account setup\n\nTest Credentials:\n• Email: chef@cafe.com\n• Password: admin123\n\nFor assistance, speak with the admin or manager.');
        }

        // Form submission with loading state
        document.getElementById('kitchenLoginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginBtn');
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Accessing Kitchen...';
        });

        // Add focus animations
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>
