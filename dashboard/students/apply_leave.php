<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

$pdo = Database::getInstance(); // 🔹 This is required

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: /smartcampus/home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    $reason     = $_POST['reason'];

    // File upload
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $filename = time() . '_' . $_FILES['attachment']['name'];
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $filename);
        $attachment = $filename;
    }

    $stmt = $pdo->prepare("INSERT INTO leave_requests 
        (student_id, leave_type_id, start_date, end_date, reason, attachment)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$student_id, $leave_type, $start_date, $end_date, $reason, $attachment]);

    echo "Leave request submitted!";
}
?>
