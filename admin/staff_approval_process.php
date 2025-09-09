<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

include '../global/php/db_connect.php';

$staff_id = $_POST['staff_id'] ?? '';
$action = $_POST['action'] ?? '';

if ($staff_id && $action) {
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE staff SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $staff_id);
        if ($stmt->execute()) {
            echo "Staff approved successfully! <a href='staff_approval.php'>Back to approvals</a>";
        } else {
            echo "Error approving staff.";
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE staff SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $staff_id);
        if ($stmt->execute()) {
            echo "Staff rejected. <a href='staff_approval.php'>Back to approvals</a>";
        } else {
            echo "Error rejecting staff.";
        }
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
$conn->close();
?>
