<?php
session_start();
require_once __DIR__ . "/db/db.php";

header('Content-Type: application/json');

try {
    $pdo = Database::getInstance();

    // Get values
    $username = $_POST['enrollment_number'] ?? ''; // can be enrollment / username
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Both fields are required."]);
        exit;
    }

    $user = null;
    $userType = null;

    // 1️⃣ First check student table
    $stmt = $pdo->prepare("SELECT id, username, password, approved FROM students WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userType = "student";

    // 2️⃣ If not found, check faculty table
    if (!$user) {
        $stmt = $pdo->prepare("SELECT id, username, password, approved FROM faculty WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userType = "faculty";
    }

    // 3️⃣ If not found, check admin table (admins don’t need approval)
    if (!$user) {
        $stmt = $pdo->prepare("SELECT id, username, password, role, status FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userType = "admin";
    }

    // If no user found in any table
    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found."]);
        exit;
    }

    // For students/faculty check approval
    if ($userType !== "admin" && isset($user['approved']) && !$user['approved']) {
        echo json_encode(["status" => "error", "message" => "Account is not yet approved."]);
        exit;
    }

    // For admin check active status
    if ($userType === "admin" && $user['status'] !== "active") {
        echo json_encode(["status" => "error", "message" => "Admin account is inactive."]);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
        exit;
    }

    // ✅ Store session
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $username;
    $_SESSION['user_type'] = $userType;

    if ($userType === "admin") {
        $_SESSION['admin_role'] = $user['role'] ?? "admin";
    }

    // Redirect URLs
    $redirectUrl = "index.php"; // default fallback
    if ($userType === "student") {
        $redirectUrl = "dashboard/students/dashboard.php";
    } elseif ($userType === "faculty") {
        $redirectUrl = "dashboard/faculty/faculty_dashboard.php";
    } elseif ($userType === "admin") {
        $redirectUrl = "dashboard/admin/admin_dashboard.php";
    }

    // ✅ Send success JSON
    echo json_encode([
        "status"   => "success",
        "message"  => "Login successful!",
        "redirect" => $redirectUrl
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
    exit;
}
