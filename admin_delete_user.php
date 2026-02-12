<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Only allow admin access
requireAdmin();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"])) {
    $user_id = (int)$_POST["user_id"];
    
    // Do not allow deleting own account
    if ($user_id == $_SESSION["user_id"]) {
        $_SESSION['message'] = "You cannot delete your own account!";
        $_SESSION['message_type'] = "danger";
        header("Location: admin_users.php");
        exit;
    }
    
    // Get user information before deletion for notification
    $user = getUserById($user_id);
    
    if ($user) {
        // Delete user account
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "User '{$user['username']}' has been permanently deleted!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "An error occurred while deleting the user.";
            $_SESSION['message_type'] = "danger";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "User not found.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
}

// Redirect to user management page
header("Location: admin_users.php");
exit;
?>