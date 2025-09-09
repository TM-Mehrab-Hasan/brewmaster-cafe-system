<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - BrewMaster Cafe System</title>
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

        <!-- Registration Card -->
        <div class="auth-card register-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="auth-title">Join BrewMaster</h1>
                <p class="auth-subtitle">Create your account and start your coffee journey</p>
                <span class="role-indicator customer">Customer Registration</span>
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

            <form action="customer_register_process.php" method="POST" class="auth-form" id="customerRegisterForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="customer_name" name="customer_name" class="form-input" 
                               placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone">Phone Number</label>
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" id="customer_phone" name="customer_phone" class="form-input" 
                               placeholder="Enter your phone number" required>
                    </div>
                </div>

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
                           placeholder="Create a strong password" required minlength="6">
                    <button type="button" class="password-toggle" onclick="togglePassword('customer_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="benefits-notice">
                    <i class="fas fa-star"></i>
                    <div>
                        <strong>Member Benefits:</strong>
                        <span>Order tracking • Loyalty rewards • Special offers • Faster checkout</span>
                    </div>
                </div>

                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="newsletter" checked>
                        <span class="checkmark"></span>
                    </label>
                    <span>Send me special offers and updates</span>
                </div>

                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="terms_agreed" required>
                        <span class="checkmark"></span>
                    </label>
                    <span>I agree to the <a href="#" onclick="showTerms()">Terms and Conditions</a></span>
                </div>

                <button type="submit" class="auth-button customer" id="registerBtn">
                    <i class="fas fa-user-plus"></i>
                    Create My Account
                </button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="customer_login.php"><strong>Sign in here</strong></a></p>
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

        function showTerms() {
            alert('Terms and Conditions:\n\n1. Accurate information required for orders\n2. Respect cafe staff and other customers\n3. Follow payment and pickup policies\n4. We protect your personal information\n5. Account responsible for all orders placed');
        }

        // Form submission with loading state
        document.getElementById('customerRegisterForm').addEventListener('submit', function(e) {
            const button = document.getElementById('registerBtn');
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
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

        // Password strength indicator
        document.getElementById('customer_password').addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            this.className = 'form-input strength-' + Math.min(strength, 3);
        });

        // Email validation feedback
        document.getElementById('customer_email').addEventListener('blur', function() {
            if (this.value && !this.validity.valid) {
                this.classList.add('invalid');
            } else {
                this.classList.remove('invalid');
            }
        });
    </script>
</body>
</html>
