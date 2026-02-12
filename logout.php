<?php
// Include functions file so session_start() is called
require_once "includes/functions.php";

// Unset all session variables
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Delete remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Set logout message
session_start();
$_SESSION['message'] = "You have been successfully logged out.";
$_SESSION['message_type'] = "info";

// Redirect to login page
header("location: login.php");
exit;
?>