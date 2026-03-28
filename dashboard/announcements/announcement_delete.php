<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// ✅ Only Admin/Faculty allowed
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$id  = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: announcements.php");
exit;
