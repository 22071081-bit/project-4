<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Set page title
$page_title = "Register - Member Management System";

// Process registration form
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
        
        // Check length and valid characters
        if (!validateUsername($username)) {
            $username_err = "Username can only contain letters, numbers, and underscores, and must be at least 3 characters long.";
        } else {
            // Check if username already exists
            $sql = "SELECT user_id FROM users WHERE username = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $username_err = "This username is already taken.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } else {
        $email = trim($_POST["email"]);
        
        // Check email format
        if (!validateEmail($email)) {
            $email_err = "Please enter a valid email address.";
        } else {
            // Check if email already exists
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must be at least 8 characters long.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // Check input errors before inserting into database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Prepare insert statement
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
            
            // Execute statement
            if (mysqli_stmt_execute($stmt)) {
                // Get new user ID
                $user_id = mysqli_insert_id($conn);
                
                // Create user profile
                $sql = "INSERT INTO user_profiles (user_id) VALUES (?)";
                $profile_stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($profile_stmt, "i", $user_id);
                mysqli_stmt_execute($profile_stmt);
                mysqli_stmt_close($profile_stmt);
                
                // Set success message
                $_SESSION['message'] = "Registration successful! You can now log in.";
                $_SESSION['message_type'] = "success";
                
                // Redirect to login page
                header("location: login.php");
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

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Create New Account</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <div class="form-text">Username must be 3-64 characters long and unique.</div>
                        <div class="invalid-feedback"><?php echo $username_err; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                        <div class="form-text">Please enter a valid email address.</div>
                        <div class="invalid-feedback"><?php echo $email_err; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                        <div class="invalid-feedback"><?php echo $password_err; ?></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                        <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include "includes/footer.php";
?>