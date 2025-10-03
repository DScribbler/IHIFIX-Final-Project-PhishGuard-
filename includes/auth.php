<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Example hardcoded credentials (replace with DB check in real apps)
    $valid_username = 'admin';
    $valid_password = 'admin123';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin_id'] = 1; // Set logged in session
        header('Location: ../campaign.php'); // Redirect to campaign page
        exit;
    } else {
        // Invalid login - redirect back with error flag
        header('Location: ../login.php?error=invalid');
        exit;
    }
}

// Define reusable login-checking functions

function require_login() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

function is_logged_in() {
    return !empty($_SESSION['admin_id']);
}
