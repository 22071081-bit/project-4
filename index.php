<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Check if setup hasn't been run
$setup_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($setup_check) == 0) {
    // Display setup message
    $_SESSION['message'] = "System has not been set up. Please run setup.php to create the database.";
    $_SESSION['message_type'] = "warning";
}

// Homepage
$page_title = "Home - Member Management System";

// Include header
include "includes/header.php";
?>

<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="card-title mb-4">Welcome to Member Management System</h1>
                
                <p class="lead">
                    A secure platform for managing user accounts, profiles, and access permissions.
                </p>
                
                <?php if (!isLoggedIn()): ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <a href="login.php" class="btn btn-primary me-md-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="register.php" class="btn btn-outline-secondary">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </a>
                </div>
                <?php else: ?>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">You are logged in!</h4>
                    <p>You can now access your profile and manage your account settings.</p>
                    <hr>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="profile.php" class="btn btn-info">
                            <i class="fas fa-user-edit me-2"></i>View Profile
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="admin_dashboard.php" class="btn btn-warning">
                            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-shield fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Secure Authentication</h5>
                        <p class="card-text">Strong password encryption and secure session management.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-cog fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">Profile Management</h5>
                        <p class="card-text">Users can easily update their personal information.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title">User Administration</h5>
                        <p class="card-text">Administrators can manage all users and control access permissions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include "includes/footer.php";
?>