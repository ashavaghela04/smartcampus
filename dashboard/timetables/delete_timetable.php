<?php
session_start();
require_once __DIR__ . "/../../db/db.php";

if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT timetable_file FROM timetables WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $timetable = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($timetable) {
        if (file_exists(__DIR__ . "/" . $timetable['timetable_file'])) {
            unlink(__DIR__ . "/" . $timetable['timetable_file']);
        }

        $stmt = $pdo->prepare("DELETE FROM timetables WHERE id=:id");
        $stmt->execute([':id' => $id]);

        $_SESSION['msg'] = "🗑 Timetable deleted successfully!";
    }
}

header("Location: view_timetable.php");
exit;
