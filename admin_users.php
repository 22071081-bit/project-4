<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Only allow admin access
requireAdmin();

// Set page title
$page_title = "User Management - Member Management System";

// Get user list
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_free_result($result);
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
            <h2><i class="fas fa-users me-2"></i>User Management</h2>
            <div class="d-flex">
                <div class="input-group me-2">
                    <input type="text" id="userSearch" class="form-control" placeholder="Search users...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" id="usersTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php if ($user['role'] == 'admin'): ?>
                                    <span class="badge bg-warning text-dark">Administrator</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatDate($user['created_at'], 'd/m/Y'); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="admin_edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="admin_toggle_user.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to <?php echo $user['is_active'] ? 'disable' : 'activate'; ?> this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="btn btn-sm <?php echo $user['is_active'] ? 'btn-danger' : 'btn-success'; ?>" title="<?php echo $user['is_active'] ? 'Disable' : 'Activate'; ?>">
                                                <?php if ($user['is_active']): ?>
                                                <i class="fas fa-ban"></i>
                                                <?php else: ?>
                                                <i class="fas fa-check"></i>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                        <form action="admin_delete_user.php" method="POST" class="d-inline" onsubmit="return confirm('WARNING: Are you sure you want to PERMANENTLY DELETE this user? This action cannot be undone!');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-dark" title="Delete permanently">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript for user search
$page_specific_script = <<<EOT
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple search functionality
        document.getElementById('userSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('usersTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const username = rows[i].cells[1].textContent.toLowerCase();
                const email = rows[i].cells[2].textContent.toLowerCase();
                
                if (username.includes(searchValue) || email.includes(searchValue)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    });
</script>
EOT;

// Include footer
include "includes/footer.php";
?>