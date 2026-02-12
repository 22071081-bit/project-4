<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Login required to access this page
requireLogin();

// Set page title
$page_title = "Profile - Member Management System";

// Get user information
$user_id = $_SESSION["user_id"];
$user = getUserById($user_id);
$profile = getUserProfile($user_id);

// Process profile update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = sanitizeInput($_POST["email"]);
    $first_name = sanitizeInput($_POST["first_name"]);
    $last_name = sanitizeInput($_POST["last_name"]);
    $address = sanitizeInput($_POST["address"]);
    $city = sanitizeInput($_POST["city"]);
    $country = sanitizeInput($_POST["country"]);
    $phone = sanitizeInput($_POST["phone"]);
    $bio = sanitizeInput($_POST["bio"]);
    
    // Validate email
    $email_err = "";
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
    
    // If no validation errors, proceed with update
    if (empty($email_err)) {
        // Update email in users table
        $sql = "UPDATE users SET email = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Check if user already has a profile
        if ($profile) {
            // Update existing profile
            $sql = "UPDATE user_profiles SET 
                    first_name = ?, 
                    last_name = ?, 
                    address = ?, 
                    city = ?, 
                    country = ?, 
                    phone = ?, 
                    bio = ? 
                    WHERE user_id = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssssi", $first_name, $last_name, $address, $city, $country, $phone, $bio, $user_id);
        } else {
            // Create new profile
            $sql = "INSERT INTO user_profiles 
                    (user_id, first_name, last_name, address, city, country, phone, bio) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssssss", $user_id, $first_name, $last_name, $address, $city, $country, $phone, $bio);
        }
        
        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Your profile has been updated successfully!";
            $_SESSION['message_type'] = "success";
            
            // Refresh page to show updated data
            header("Location: profile.php");
            exit;
        } else {
            $_SESSION['message'] = "An error occurred while updating your profile. Please try again.";
            $_SESSION['message_type'] = "danger";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = $email_err;
        $_SESSION['message_type'] = "danger";
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
                <a href="profile.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </a>
                <a href="change_password.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-key me-2"></i>Change Password
                </a>
                <?php if (isAdmin()): ?>
                <a href="admin_dashboard.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="profile-pic">
                    <i class="fas fa-user"></i>
                </div>
                <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                <p><strong>Role:</strong> <?php echo ($user['role'] == 'admin') ? 'Administrator' : 'Regular User'; ?></p>
                <p><strong>Member since:</strong> <?php echo formatDate($user['created_at'], 'd/m/Y'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Profile</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $profile ? $profile['first_name'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $profile ? $profile['last_name'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control" value="<?php echo $profile ? $profile['address'] : ''; ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" id="city" class="form-control" value="<?php echo $profile ? $profile['city'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" name="country" id="country" class="form-control" value="<?php echo $profile ? $profile['country'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $profile ? $profile['phone'] : ''; ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="bio" class="form-label">Biography</label>
                        <textarea name="bio" id="bio" class="form-control" rows="4"><?php echo $profile ? $profile['bio'] : ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
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