<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Only allow admin access
requireAdmin();

// Check if ID exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid user ID.";
    $_SESSION['message_type'] = "danger";
    header("Location: admin_users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Get user information
$user = getUserById($user_id);

if (!$user) {
    $_SESSION['message'] = "User not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: admin_users.php");
    exit;
}

// Set page title
$page_title = "Edit User - Member Management System";

// Process edit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitizeInput($_POST["username"]);
    $email = sanitizeInput($_POST["email"]);
    $role = sanitizeInput($_POST["role"]);
    $is_active = isset($_POST["is_active"]) ? 1 : 0;
    
    // Validate data
    $username_err = $email_err = "";
    
    // Validate username
    if (empty($username)) {
        $username_err = "Please enter a username.";
    } elseif (!validateUsername($username)) {
        $username_err = "Username can only contain letters, numbers, and underscores, and must be at least 3 characters long.";
    } elseif ($username != $user['username']) {
        // Check if username already exists (only if username changed)
        $sql = "SELECT user_id FROM users WHERE username = ? AND user_id != ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $username, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $username_err = "This username is already taken.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Validate email
    if (empty($email)) {
        $email_err = "Please enter an email address.";
    } elseif (!validateEmail($email)) {
        $email_err = "Please enter a valid email address.";
    } elseif ($email != $user['email']) {
        // Check if email already exists (only if email changed)
        $sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $email_err = "This email is already registered by another account.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Check if trying to change own role
    if ($user_id == $_SESSION['user_id'] && $user['role'] == 'admin' && $role != 'admin') {
        $_SESSION['message'] = "You cannot change your own role from administrator.";
        $_SESSION['message_type'] = "danger";
        header("Location: admin_edit_user.php?id=" . $user_id);
        exit;
    }
    
    // Check if trying to disable own account
    if ($user_id == $_SESSION['user_id'] && !$is_active) {
        $_SESSION['message'] = "You cannot disable your own account.";
        $_SESSION['message_type'] = "danger";
        header("Location: admin_edit_user.php?id=" . $user_id);
        exit;
    }
    
    // If no validation errors, proceed with update
    if (empty($username_err) && empty($email_err)) {
        // Update user information
        $sql = "UPDATE users SET username = ?, email = ?, role = ?, is_active = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssii", $username, $email, $role, $is_active, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "User information has been updated successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: admin_users.php");
            exit;
        } else {
            $_SESSION['message'] = "An error occurred. Please try again later.";
            $_SESSION['message_type'] = "danger";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Include header
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Admin Menu</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="admin_dashboard.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="admin_users.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-users me-2"></i>User Management
                </a>
                <a href="profile.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i>Personal Profile
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-edit me-2"></i>Edit User</h2>
            <a href="admin_users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Editing user: <?php echo $user['username']; ?></h5>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $user_id); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $user['username']; ?>" required>
                            <?php if (!empty($username_err)): ?>
                            <div class="invalid-feedback"><?php echo $username_err; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $user['email']; ?>" required>
                            <?php if (!empty($email_err)): ?>
                            <div class="invalid-feedback"><?php echo $email_err; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>Regular User</option>
                                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?php echo ($user['is_active']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Account is active</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Account Information</h6>
                            <p><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
                            <p><strong>Created Date:</strong> <?php echo formatDate($user['created_at'], 'd/m/Y H:i:s'); ?></p>
                            <?php if ($_SESSION['user_id'] == $user['user_id']): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>This is your account. Some restrictions apply when editing your own account.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="admin_users.php" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update User</button>
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