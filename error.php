<?php
// Include required files
require_once "includes/config.php";
require_once "includes/functions.php";

// Set default error code
$error_code = 404;
$error_title = "Page Not Found";
$error_message = "The page you are looking for does not exist or has been moved.";

// Check error code passed
if (isset($_GET['code'])) {
    switch ($_GET['code']) {
        case 403:
            $error_code = 403;
            $error_title = "Access Denied";
            $error_message = "You do not have permission to access this resource.";
            break;
        case 500:
            $error_code = 500;
            $error_title = "Server Error";
            $error_message = "An error occurred on the server. Please try again later.";
            break;
        default:
            // Keep default 404
            break;
    }
}

// Set page title
$page_title = $error_title . " - Member Management System";

// Include header
include "includes/header.php";
?>

<div class="text-center error-container">
    <h1 class="error-code"><?php echo $error_code; ?></h1>
    <h2 class="mb-4"><?php echo $error_title; ?></h2>
    <p class="lead"><?php echo $error_message; ?></p>
    <div class="mt-4">
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Return to Homepage
        </a>
    </div>
</div>

<?php
// Include footer
include "includes/footer.php";
?>