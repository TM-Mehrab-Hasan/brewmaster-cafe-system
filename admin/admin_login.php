<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BrewMaster Cafe System</title>
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
                    <i class="fas fa-crown"></i>
                </div>
                <h1 class="auth-title">Administrator</h1>
                <p class="auth-subtitle">Complete system control and management</p>
                <span class="role-indicator">Admin Portal</span>
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

            <form action="admin_login_process.php" method="POST" class="auth-form" id="adminLoginForm">
                <div class="form-group">
                    <label for="admin_username">Username</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="admin_username" name="admin_username" class="form-input" 
                           placeholder="Enter your username" required>
                </div>

                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="admin_password" name="admin_password" class="form-input" 
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('admin_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember_me">
                        <span class="checkmark"></span>
                    </label>
                    <span>Remember me for 30 days</span>
                </div>

                <button type="submit" class="auth-button" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In to Dashboard
                </button>
            </form>

            <div class="auth-links">
                <p>Default credentials: <strong>admin</strong> / <strong>admin123</strong></p>
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

        // Form submission with loading state
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
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
