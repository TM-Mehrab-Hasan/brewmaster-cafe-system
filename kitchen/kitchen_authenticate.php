<?php
session_start();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kitchen_id'], $_POST['kitchen_password'])) {
    $kitchen_email = trim($_POST['kitchen_id']);
    $kitchen_password = trim($_POST['kitchen_password']);
    
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'cafe_management');
    
    if ($conn->connect_error) {
        $_SESSION['error_message'] = 'Database connection failed. Please try again.';
        header('Location: kitchen_login.php');
        exit;
    }
    
    // Check credentials in kitchen_staff table
    $stmt = $conn->prepare("SELECT id, name, email, password FROM kitchen_staff WHERE email = ? AND status = 'approved'");
    $stmt->bind_param("s", $kitchen_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $kitchen_staff = $result->fetch_assoc();
        
        // Verify password (supporting both hashed and test password)
        if (password_verify($kitchen_password, $kitchen_staff['password']) || $kitchen_password === 'admin123') {
            // Set session variables
            $_SESSION['kitchen_staff_id'] = $kitchen_staff['id'];
            $_SESSION['kitchen_staff_name'] = $kitchen_staff['name'];
            $_SESSION['kitchen_staff_email'] = $kitchen_staff['email'];
            $_SESSION['success_message'] = 'Welcome to Kitchen Dashboard, ' . $kitchen_staff['name'] . '!';
            
            // Redirect to kitchen dashboard
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error_message'] = 'Invalid password. Please try again.';
            header('Location: kitchen_login.php');
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Kitchen staff not found or not approved. Contact admin.';
        header('Location: kitchen_login.php');
        exit;
    }
    
    $stmt->close();
    $conn->close();
} else {
    // If not a POST request, redirect to login page
    header('Location: kitchen_login.php');
    exit;
}
?>
