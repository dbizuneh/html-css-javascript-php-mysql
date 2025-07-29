<?php
require_once 'includes/auth.php';

$auth = new Auth();

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    // Redirect to dashboard if already logged in
    header('Location: dashboard.php');
    exit();
} else {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}
?>