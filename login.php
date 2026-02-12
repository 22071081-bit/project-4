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
$page_title = "Login - Member Management System";

// Process login form
$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate login credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare select statement
        $sql = "SELECT user_id, username, password, role, is_active FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement
            mysqli_stmt_bind_param($stmt, "s", $username);
            
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind results to variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role, $is_active);
                    
                    if (mysqli_stmt_fetch($stmt)) {
                        // Check if account is active
                        if ($is_active == 0) {
                            $login_err = "Your account has been disabled. Please contact administrator.";
                        } 
                        // Verify password
                        elseif (password_verify($password, $hashed_password)) {
                            // Password correct, start new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["user_id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["user_role"] = $role;
                            
                            // Remember login (if selected)
                            if (isset($_POST['remember_me']) && $_POST['remember_me'] == 1) {
                                $token = bin2hex(random_bytes(16));
                                $expires = time() + 3600 * 24 * 30; // 30 days
                                
                                // Save cookie
                                setcookie('remember_token', $token, $expires, '/');
                                
                                // Save token to database (in a separate table in practice)
                                // Full remember me functionality would require additional table and code
                            }
                            
                            // Set success message
                            $_SESSION['message'] = "Welcome back, $username!";
                            $_SESSION['message_type'] = "success";
                            
                            // Redirect user to home page
                            header("location: index.php");
                            exit;
                        } else {
                            // Invalid password
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist
                    $login_err = "Invalid username or password.";
                }
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
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login</h3>
            </div>
            <div class="card-body">
                <?php 
                if (!empty($login_err)) {
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <div class="invalid-feedback"><?php echo $username_err; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <div class="invalid-feedback"><?php echo $password_err; ?></div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input" value="1">
                        <label class="form-check-label" for="remember_me">Remember me</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include "includes/footer.php";
?>