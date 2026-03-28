<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// ✅ Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// ✅ Validate student ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_students_manage.php?error=Invalid student ID");
    exit;
}

$id = intval($_GET['id']);

try {
    // ✅ Delete student
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin_students_manage.php?msg=Student deleted successfully");
    exit;
} catch (PDOException $e) {
    header("Location: admin_students_manage.php?error=Error deleting student: " . urlencode($e->getMessage()));
    exit;
}
