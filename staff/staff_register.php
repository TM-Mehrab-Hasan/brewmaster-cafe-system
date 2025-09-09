<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration - BrewMaster Cafe System</title>
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
                <h1 class="auth-title">Join Our Team</h1>
                <p class="auth-subtitle">Register as a staff member</p>
                <span class="role-indicator staff">Staff Registration</span>
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

            <form action="staff_register_process.php" method="POST" class="auth-form" id="staffRegisterForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="staff_name">Full Name</label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="staff_name" name="staff_name" class="form-input" 
                               placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="staff_phone">Phone Number</label>
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" id="staff_phone" name="staff_phone" class="form-input" 
                               placeholder="Enter your phone number" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="staff_email">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="staff_email" name="staff_email" class="form-input" 
                           placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label for="staff_password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="staff_password" name="staff_password" class="form-input" 
                           placeholder="Create a strong password" required minlength="6">
                    <button type="button" class="password-toggle" onclick="togglePassword('staff_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="info-notice">
                    <i class="fas fa-info-circle"></i>
                    <span>Note: Staff registration requires admin approval before account activation.</span>
                </div>

                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="terms_agreed" required>
                        <span class="checkmark"></span>
                    </label>
                    <span>I agree to the <a href="#" onclick="showTerms()">Terms and Conditions</a></span>
                </div>

                <button type="submit" class="auth-button staff" id="registerBtn">
                    <i class="fas fa-user-plus"></i>
                    Submit Registration
                </button>
            </form>

            <div class="auth-links">
                <p>Already a team member? <a href="staff_login.php"><strong>Login here</strong></a></p>
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
            alert('Terms and Conditions:\n\n1. Staff members must maintain professional conduct\n2. Follow all cafe policies and procedures\n3. Protect customer and business information\n4. Report to work on time as scheduled\n5. Admin approval required for account activation');
        }

        // Form submission with loading state
        document.getElementById('staffRegisterForm').addEventListener('submit', function(e) {
            const button = document.getElementById('registerBtn');
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Registration...';
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
        document.getElementById('staff_password').addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            this.className = 'form-input strength-' + Math.min(strength, 3);
        });
    </script>
</body>
</html>
