<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

require_login();

$pdo = Database::getInstance();

// ✅ Ensure students exist
if ((!isset($_POST['mark']) || empty($_POST['mark'])) 
    && !isset($_POST['mark_all_present']) 
    && !isset($_POST['mark_all_absent'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ Today or selected date
$date = isset($_POST['date']) ? $_POST['date'] : date("Y-m-d");
$today = new DateTime($date);

$period = $today->format("Y-m");   // Example: 2025-09
$daysInMonth = days_in_month($today->format("m"), $today->format("Y"));
$currentDay  = (int)$today->format("j");

// ✅ Bulk Mark (All Present / All Absent)
if (isset($_POST['mark_all_present']) || isset($_POST['mark_all_absent'])) {
    if (!isset($_POST['students']) || empty($_POST['students'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $status = isset($_POST['mark_all_present']) ? 'P' : 'A';

    foreach ($_POST['students'] as $student_id) {
        // 1. Get existing attendance record
        $stmt = $pdo->prepare("SELECT record FROM attendance_monthly WHERE student_id = ? AND `year_month` = ?");
        $stmt->execute([$student_id, $period]);
        $row = $stmt->fetch();

        if ($row) {
            $record = str_split($row['record']);
        } else {
            $record = array_fill(0, $daysInMonth, '-');
            $stmt = $pdo->prepare("INSERT INTO attendance_monthly (student_id, `year_month`, record, updated_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$student_id, $period, implode("", $record)]);
        }

        // 2. Update today’s status
        $record[$currentDay - 1] = $status;

        // 3. Save back to DB
        $recordString = implode("", $record);
        $stmt = $pdo->prepare("UPDATE attendance_monthly 
                               SET record = ?, updated_at = NOW() 
                               WHERE student_id = ? AND `year_month` = ?");
        $stmt->execute([$recordString, $student_id, $period]);
    }
}

// ✅ Individual Marking
if (isset($_POST['mark']) && !empty($_POST['mark'])) {
    foreach ($_POST['mark'] as $student_id => $status) {
        $stmt = $pdo->prepare("SELECT record FROM attendance_monthly WHERE student_id = ? AND `year_month` = ?");
        $stmt->execute([$student_id, $period]);
        $row = $stmt->fetch();

        if ($row) {
            $record = str_split($row['record']);
        } else {
            $record = array_fill(0, $daysInMonth, '-');
            $stmt = $pdo->prepare("INSERT INTO attendance_monthly (student_id, `year_month`, record, updated_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$student_id, $period, implode("", $record)]);
        }

        $record[$currentDay - 1] = $status;

        $recordString = implode("", $record);
        $stmt = $pdo->prepare("UPDATE attendance_monthly 
                               SET record = ?, updated_at = NOW() 
                               WHERE student_id = ? AND `year_month` = ?");
        $stmt->execute([$recordString, $student_id, $period]);
    }
}

// ✅ Redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
