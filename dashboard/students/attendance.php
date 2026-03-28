<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/../attedance/auth.php';
require_once __DIR__ . '/../attedance/functions.php';

require_login();

// ✅ Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: /smartcampus/home.php");
    exit();
}

$pdo = Database::getInstance();
$student_id = $_SESSION['user_id'];

// ✅ Selected month (via GET) or current month
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");
$today = new DateTime($date);
$year_month = $today->format("Y-m");
$daysInMonth = days_in_month($today->format("m"), $today->format("Y"));

// ✅ Get student details
$stmt = $pdo->prepare("SELECT firstname, lastname, department, semester FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Get attendance record
$record = get_attendance_record($pdo, $student_id, $year_month);
if ($record === false) {
    $record = array_fill(0, $daysInMonth, '-'); // no record = not taken
}

// ✅ Calculate summary
$totalPresent = count(array_filter($record, fn($s) => $s === 'P'));
$totalAbsent  = count(array_filter($record, fn($s) => $s === 'A'));
$totalNotTaken = count(array_filter($record, fn($s) => $s === '-'));

$effectiveDays = $totalPresent + $totalAbsent; // exclude "not taken"
$attendancePercent = $effectiveDays > 0 ? round(($totalPresent / $effectiveDays) * 100, 2) : 0;
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/sidebar.php'; ?>
<main class="container">
  <section class="card">
    <h3>My Attendance - <?= htmlspecialchars($today->format("F Y")) ?></h3>
    <p><strong>Name:</strong> <?= htmlspecialchars($student['firstname'] . " " . $student['lastname']) ?></p>
    <p><strong>Department:</strong> <?= htmlspecialchars($student['department']) ?></p>
    <p><strong>Semester:</strong> <?= htmlspecialchars($student['semester']) ?></p>

    <!-- Month selector -->
    <form method="get" style="margin:10px 0;">
      <label for="date"><strong>Select Month:</strong></label>
      <input type="month" name="date" id="date" value="<?= $today->format('Y-m') ?>" onchange="this.form.submit()">
    </form>

    <!-- Attendance Calendar -->
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;margin-top:20px;text-align:center;">
      <tr style="background:#f4f4f4;">
        <th>Day</th>
        <th>Status</th>
      </tr>
      <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
        <?php 
          $status = $record[$d - 1] ?? '-';
          $color = ($status === 'P') ? 'green' : (($status === 'A') ? 'red' : 'gray');
          $symbol = ($status === 'P') ? '✅ Present' : (($status === 'A') ? '❌ Absent' : '⏺ Not Taken');
        ?>
        <tr>
          <td><?= $d . " " . $today->format("M Y") ?></td>
          <td style="font-weight:bold; color:<?= $color ?>"><?= $symbol ?></td>
        </tr>
      <?php endfor; ?>

      <!-- Summary row -->
      <tr style="background:#eef;">
        <td><strong>Summary</strong></td>
        <td>
          ✅ Present: <?= $totalPresent ?> &nbsp; | 
          ❌ Absent: <?= $totalAbsent ?> &nbsp; | 
          ⏺ Not Taken: <?= $totalNotTaken ?> <br>
          📊 Attendance %: <strong><?= $attendancePercent ?>%</strong>
        </td>
      </tr>
    </table>
  </section>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
