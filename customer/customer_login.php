<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - BrewMaster Cafe System</title>
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
                    <i class="fas fa-coffee"></i>
                </div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Your favorite coffee awaits</p>
                <span class="role-indicator customer">Customer Portal</span>
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

            <form action="customer_login_process.php" method="POST" class="auth-form" id="customerLoginForm">
                <div class="form-group">
                    <label for="customer_email">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="customer_email" name="customer_email" class="form-input" 
                           placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label for="customer_password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="customer_password" name="customer_password" class="form-input" 
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('customer_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember_me">
                        <span class="checkmark"></span>
                    </label>
                    <span>Remember me</span>
                </div>

                <button type="submit" class="auth-button customer" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Start Ordering
                </button>
            </form>

            <div class="auth-links">
                <p>New customer? <a href="customer_register.php"><strong>Create account</strong></a></p>
                <p><a href="#" onclick="showGuestOptions()">Order as Guest</a></p>
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

        function showGuestOptions() {
            alert('Guest Ordering:\n\nYou can order as a guest, but creating an account gives you:\n• Order history tracking\n• Faster checkout\n• Loyalty rewards\n• Special offers\n\nWould you like to create an account instead?');
        }

        // Form submission with loading state
        document.getElementById('customerLoginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginBtn');
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
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
