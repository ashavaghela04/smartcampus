<?php
// assets/vendor/auth.php

// ✅ Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Force user to login
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");
        exit;
    }
}

/**
 * Check if logged-in user is a student
 */
function is_student() {
    return (isset($_SESSION['user_type']) && $_SESSION['user_type'] === "student");
}

/**
 * Check if logged-in user is faculty or admin
 */
function is_staff() {
    return (isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ["faculty", "admin"]));
}
