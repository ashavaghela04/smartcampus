<?php
// assets/get_notifications.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../db/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_GET['role'])) {
    echo json_encode(['notifications' => []]);
    exit;
}

$pdo = Database::getInstance();
$userId = $_SESSION['user_id'];
$role = $_GET['role']; // student, faculty, admin

$notifications = [];

try {
    if ($role === 'student') {
        // Example: Student notifications (new assignments, leave approval, results)
        $stmt = $pdo->prepare("
            SELECT title, link FROM notifications 
            WHERE target = 'student' OR target = 'all'
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'faculty') {
        // Example: Faculty notifications (attendance reminders, notices)
        $stmt = $pdo->prepare("
            SELECT title, link FROM notifications 
            WHERE target = 'faculty' OR target = 'all'
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'admin') {
        // Example: Admin notifications (new registrations, system alerts)
        $stmt = $pdo->prepare("
            SELECT title, link FROM notifications 
            WHERE target = 'admin' OR target = 'all'
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Return as JSON
    echo json_encode(['notifications' => $notifications]);

} catch (PDOException $e) {
    // On error, return empty notifications
    echo json_encode(['notifications' => []]);
}
