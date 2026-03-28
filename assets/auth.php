<?php
// includes/auth.php
session_start();

/**
 * Require user to be logged in.
 * Redirects to login page if not authenticated.
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /public/login.php");
        exit;
    }
}

/**
 * Check if logged-in user is admin.
 */
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: /public/index.php");
        exit;
    }
}

/**
 * Log in the user (after verifying credentials).
 */
function loginUser(int $userId, string $role, string $name) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['role']    = $role;
    $_SESSION['name']    = $name;
}

/**
 * Log out the user.
 */
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: /smartcampus/home.php");
    exit;
}
