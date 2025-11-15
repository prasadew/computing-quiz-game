<?php
// logout.php - Handle user logout
session_start();

// Include required files
require_once 'config/database.php';
require_once 'includes/auth.php';

// Create Auth instance
$auth = new Auth();

// Perform logout
$auth->logout();

// Destroy the session
session_destroy();

// Clear any existing cookies
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to index page
header('Location: index.php');
exit();
?>