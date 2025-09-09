<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Approval - Admin Panel</title>
    <link rel="stylesheet" href="../global/css/style.css">
</head>
<body>
    <header>
        <h2>Staff Approval Management</h2>
    </header>
    <main>
        <?php
        session_start();
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: admin_login.php');
            exit;
        }
        
        include '../global/php/db_connect.php';
        
        $stmt = $conn->prepare("SELECT id, name, email, phone, created_at FROM staff WHERE status = 'pending'");
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        
        <h3>Pending Staff Registrations</h3>
        <div class="staff-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="staff-card">
                    <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                    <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($row['phone']); ?></p>
                    <p>Applied: <?php echo $row['created_at']; ?></p>
                    <form action="staff_approval_process.php" method="POST" style="display: inline;">
                        <input type="hidden" name="staff_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="action" value="approve">Approve</button>
                        <button type="submit" name="action" value="reject">Reject</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
        
        <p><a href="index.php">Back to Admin Dashboard</a></p>
        
        <?php
        $stmt->close();
        $conn->close();
        ?>
    </main>
    <script src="../global/js/main.js"></script>
</body>
</html>
