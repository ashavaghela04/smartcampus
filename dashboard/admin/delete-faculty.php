<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// ✅ Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// ✅ Check for ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_faculty_manage.php?error=Invalid faculty ID");
    exit;
}

$id = $_GET['id'];

try {
    // ✅ Delete faculty
    $stmt = $pdo->prepare("DELETE FROM faculty WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin_faculty_manage.php?msg=Faculty deleted successfully");
    exit;
} catch (PDOException $e) {
    header("Location: admin_faculty_manage.php?error=Error deleting faculty: " . $e->getMessage());
    exit;
}
