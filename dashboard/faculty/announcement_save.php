<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

$pdo = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $faculty_id = $_SESSION['user_id'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // ✅ Update
        $stmt = $pdo->prepare("UPDATE announcements SET title=?, message=? WHERE id=? AND faculty_id=?");
        $stmt->execute([$title, $message, $_POST['id'], $faculty_id]);
    } else {
        // ✅ Insert
        $stmt = $pdo->prepare("INSERT INTO announcements (faculty_id, title, message) VALUES (?, ?, ?)");
        $stmt->execute([$faculty_id, $title, $message]);
    }

    header("Location: faculty_announcements.php");
    exit;
}
