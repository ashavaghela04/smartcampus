<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM results WHERE id=?");
$stmt->execute([$id]);

header("Location: admin_results_manage.php");
exit;
