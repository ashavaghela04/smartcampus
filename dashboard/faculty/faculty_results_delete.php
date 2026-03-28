<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "faculty") {
  http_response_code(403);
  exit("Unauthorized.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: faculty_results_list.php");
  exit;
}

$pdo = Database::getInstance();
$facultyId = (int) $_SESSION['user_id'];
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
  header("Location: faculty_results_list.php?msg=Invalid request");
  exit;
}

// Only allow delete of rows created by this faculty
$stmt = $pdo->prepare("DELETE FROM results WHERE id = ? AND faculty_id = ?");
$stmt->execute([$id, $facultyId]);

header("Location: faculty_results_list.php?msg=Deleted");
exit;
