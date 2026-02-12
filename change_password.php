<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Login required to access this page
requireLogin();

// Set page title
$page_title = "Change Password - Member Management System";

// Get current user ID
$user_id = $_SESSION["user_id"];

// Process password change form
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate current password
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Please enter your current password.";
    } else {
        $current_password = trim($_POST["current_password"]);
        
        // Check current password
        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $hashed_password);
            if (mysqli_stmt_fetch($stmt)) {
                if (!password_verify($current_password, $hashed_password)) {
                    $current_password_err = "Current password is incorrect.";
                }
            }
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter a new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 8) {
        $new_password_err = "Password must be at least 8 characters long.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // Check input errors before updating the database
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        // Prepare update statement
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Set success message
                $_SESSION['message'] = "Your password has been updated successfully!";
                $_SESSION['message_type'] = "success";
                
                // Redirect to profile page
                header("location: profile.php");
                exit;
            } else {
                $_SESSION['message'] = "An error occurred. Please try again later.";
                $_SESSION['message_type'] = "danger";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Include header
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Account Menu</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </a>
                <a href="change_password.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-key me-2"></i>Change Password
                </a>
                <?php if (isAdmin()): ?>
                <a href="admin_dashboard.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Change Password</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control <?php echo (!empty($current_password_err)) ? 'is-invalid' : ''; ?>" required>
                        <div class="invalid-feedback"><?php echo $current_password_err; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" required>
                        <div class="form-text">Password must be at least 8 characters long.</div>
                        <div class="invalid-feedback"><?php echo $new_password_err; ?></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" required>
                        <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="profile.php" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include "includes/footer.php";
?>