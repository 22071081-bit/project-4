<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Only allow admin access
requireAdmin();

// Set page title
$page_title = "Admin Dashboard - Member Management System";

// Get user statistics
$total_users = 0;
$active_users = 0;
$admin_users = 0;

// Total users
$sql = "SELECT COUNT(*) as count FROM users";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_users = $row['count'];
}

// Active users
$sql = "SELECT COUNT(*) as count FROM users WHERE is_active = 1";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $active_users = $row['count'];
}

// Administrators
$sql = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $admin_users = $row['count'];
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
                <a href="admin_dashboard.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="admin_users.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i>User Management
                </a>
                <a href="profile.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i>Personal Profile
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white stats-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="display-4"><?php echo $total_users; ?></p>
                        <a href="admin_users.php" class="btn btn-outline-light btn-sm">View All Users</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white stats-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Active Users</h5>
                        <p class="display-4"><?php echo $active_users; ?></p>
                        <a href="admin_users.php" class="btn btn-outline-light btn-sm">View Active Users</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-warning stats-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Administrators</h5>
                        <p class="display-4"><?php echo $admin_users; ?></p>
                        <a href="admin_users.php" class="btn btn-outline-dark btn-sm">View Administrators</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Current Administrator:</strong> <?php echo $_SESSION['username']; ?></p>
                        <p><strong>Server Time:</strong> <span id="server-time"><?php echo date('d/m/Y H:i:s'); ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>System Status:</strong> <span class="badge bg-success">Online</span></p>
                        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="admin_users.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript for real-time clock
$page_specific_script = <<<EOT
<script>
    function updateServerTime() {
        const now = new Date();
        document.getElementById('server-time').textContent = now.toLocaleString('en-US');
    }
    
    // Update every second
    updateServerTime();
    setInterval(updateServerTime, 1000);
</script>
EOT;

// Include footer
include "includes/footer.php";
?>