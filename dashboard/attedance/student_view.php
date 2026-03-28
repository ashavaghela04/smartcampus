<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

require_login();
if (!is_student()) {
    die("Access denied. Only students can view attendance.");
}

$pdo = Database::getInstance();

// ✅ Get student record linked to logged-in user
$stmt = $pdo->prepare("SELECT id, fname, lname FROM students WHERE user_id = ? LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();
if (!$student) {
    die("Student record not found.");
}

$today = new DateTime();
$period = $today->format("Y-m");
$daysInMonth = days_in_month($today->format("m"), $today->format("Y"));

// ✅ Fetch student attendance for this month
$record = get_attendance_record($pdo, $student['id'], $period);
if ($record === false) {
    $record = create_attendance_record($pdo, $student['id'], $period, $daysInMonth);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Attendance</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-top: 20px;
        }
        .day {
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }
        .present { background-color: #c8f7c5; color: green; }
        .absent  { background-color: #f7c5c5; color: red; }
        .empty   { background-color: #f1f1f1; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?= htmlspecialchars($student['fname'] . " " . $student['lname']) ?></h2>
        <h3>Attendance for <?= $today->format("F Y") ?></h3>

        <div class="calendar">
            <?php
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $status = $record[$day - 1] ?? '-'; // default -
                $class = ($status === 'P') ? "present" : "absent";
                echo "<div class='day $class'>$day<br>$status</div>";
            }
            ?>
        </div>

        <p>Total Present: <?= count_present($record) ?> / <?= $daysInMonth ?></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
