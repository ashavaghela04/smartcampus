<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
header('Content-Type: application/json');

$pdo = Database::getInstance();
$id = $_POST['id'] ?? '';
$faculty_id = $_SESSION['user_id'] ?? null;

if ($id && $faculty_id) {
  $stmt = $pdo->prepare("DELETE FROM announcements WHERE id=? AND faculty_id=?");
  $stmt->execute([$id, $faculty_id]);
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => "Invalid request"]);
}
