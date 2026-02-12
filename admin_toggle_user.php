<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Only allow admin access
requireAdmin();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"])) {
    $user_id = (int)$_POST["user_id"];
    
    // Do not allow disabling own account
    if ($user_id == $_SESSION["user_id"]) {
        $_SESSION['message'] = "You cannot disable your own account!";
        $_SESSION['message_type'] = "danger";
        header("Location: admin_users.php");
        exit;
    }
    
    // Get user information
    $user = getUserById($user_id);
    
    if ($user) {
        // Toggle status
        $new_status = $user['is_active'] ? 0 : 1;
        
        // Update status
        $sql = "UPDATE users SET is_active = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $new_status, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $status_text = $new_status ? "activated" : "disabled";
            $_SESSION['message'] = "User {$user['username']} has been $status_text!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "An error occurred while updating user status.";
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