<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$pageTitle = "Admin Attendance";
$activePage = "attendance";

require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/../attedance/auth.php';
require_once __DIR__ . '/../attedance/functions.php';

require_login();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /smartcampus/home.php");
    exit();
}

$pdo = Database::getInstance();

// ✅ Handle selected filters
$searchName   = isset($_GET['name']) ? trim($_GET['name']) : '';
$selectedYear = isset($_GET['student_year']) ? (int)$_GET['student_year'] : 0;
$selectedDept = isset($_GET['department']) ? trim($_GET['department']) : '';
$selectedDate = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// ✅ Today (or selected date)
$today = new DateTime($selectedDate);
$period = $today->format("Y-m");
$daysInMonth = days_in_month($today->format("m"), $today->format("Y"));
$currentDay = $today->format("j");

// ✅ Get departments for filter
$deptStmt = $pdo->query("SELECT DISTINCT department FROM students ORDER BY department ASC");
$departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);

// ✅ Build SQL query dynamically
$sql = "SELECT id, firstname, lastname, semester, department FROM students WHERE 1=1";
$params = [];

if ($searchName !== '') {
    $sql .= " AND (firstname LIKE ? OR lastname LIKE ?)";
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}
if ($selectedYear > 0) {
    $sql .= " AND CEIL(semester / 2) = ?";
    $params[] = $selectedYear;
}
if ($selectedDept !== '') {
    $sql .= " AND department = ?";
    $params[] = $selectedDept;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Count present/absent
$presentCount = 0;
$absentCount  = 0;
foreach ($students as $stu) {
    $record = get_attendance_record($pdo, $stu['id'], $period);
    if ($record === false) {
        $record = create_attendance_record($pdo, $stu['id'], $period, $daysInMonth);
    }
    $todayStatus = $record[$currentDay - 1] ?? 'A';
    if ($todayStatus === 'P') {
        $presentCount++;
    } else {
        $absentCount++;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>

<main class="container">
  <section class="card">
    <h3>Admin Attendance - <?= htmlspecialchars($today->format("F Y")) ?></h3>

    <!-- ✅ Filters -->
    <div style="margin:15px 0; padding:10px; background:#f9f9f9; border-radius:8px;">
      <form method="get" style="display:flex; flex-wrap:wrap; gap:20px; align-items:center;">

        <!-- Name Search -->
        <div>
          <label for="name"><strong>Student Name:</strong></label><br>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($searchName) ?>"
                 placeholder="Enter name...">
        </div>

        <!-- Student Year -->
        <div>
          <label for="student_year"><strong>Year:</strong></label><br>
          <select name="student_year" id="student_year">
            <option value="0">All Years</option>
            <option value="1" <?= $selectedYear === 1 ? 'selected' : '' ?>>1st Year</option>
            <option value="2" <?= $selectedYear === 2 ? 'selected' : '' ?>>2nd Year</option>
            <option value="3" <?= $selectedYear === 3 ? 'selected' : '' ?>>3rd Year</option>
            <option value="4" <?= $selectedYear === 4 ? 'selected' : '' ?>>4th Year</option>
          </select>
        </div>

        <!-- Department -->
        <div>
          <label for="department"><strong>Department:</strong></label><br>
          <select name="department" id="department">
            <option value="">All Departments</option>
            <?php foreach ($departments as $dept): ?>
              <option value="<?= htmlspecialchars($dept) ?>" <?= $selectedDept === $dept ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Calendar Date -->
        <div>
          <label for="date"><strong>Date:</strong></label><br>
          <input type="date" id="date" name="date" value="<?= htmlspecialchars($today->format('Y-m-d')) ?>">
        </div>

        <div style="align-self:flex-end;">
          <button type="submit" style="padding:6px 12px; background:#2196F3; color:white; border:none; border-radius:5px;">
            Filter
          </button>
        </div>

      </form>
    </div>

    <p><strong>Date:</strong> <?= $today->format("d M Y") ?></p>

    <!-- ✅ Attendance Count -->
    <div style="margin:15px 0; padding:12px; background:#eef7ff; border:1px solid #ccc; border-radius:8px;">
      <strong>Attendance Summary:</strong> 
      Present: <span style="color:green; font-weight:bold;"><?= $presentCount ?></span> | 
      Absent: <span style="color:red; font-weight:bold;"><?= $absentCount ?></span> | 
      Total: <strong><?= $presentCount + $absentCount ?></strong>
    </div>

    <!-- ✅ Attendance Table -->
    <form method="POST" action="../attedance/save_attendance.php">
      <input type="hidden" name="date" value="<?= $today->format('Y-m-d') ?>">

      <?php foreach ($students as $stu): ?>
        <input type="hidden" name="students[]" value="<?= htmlspecialchars($stu['id']) ?>">
      <?php endforeach; ?>

      <div style="margin-bottom:15px;">
        <button type="submit" name="mark_all_present" value="1"
          style="padding:8px 16px; background:#4CAF50; color:white; border:none; border-radius:6px; font-weight:bold;">
          Mark All Present
        </button>
        <button type="submit" name="mark_all_absent" value="1"
          style="padding:8px 16px; background:#f44336; color:white; border:none; border-radius:6px; font-weight:bold;">
          Mark All Absent
        </button>
      </div>



      <table border="1" cellpadding="8" cellspacing="0" style="width:100%;margin-top:10px;text-align:center;">
        <tr style="background:#f4f4f4;">
          <th>Student Name</th>
          <th>Semester</th>
          <th>Department</th>
          <th>Status (<?= $today->format("d M") ?>)</th>
        </tr>

        <?php if (count($students) === 0): ?>
          <tr><td colspan="4">No students found for this filter.</td></tr>
        <?php endif; ?>

        <?php foreach ($students as $stu): ?>
          <?php
          $record = get_attendance_record($pdo, $stu['id'], $period);
          if ($record === false) {
              $record = create_attendance_record($pdo, $stu['id'], $period, $daysInMonth);
          }
          $todayStatus = $record[$currentDay - 1] ?? 'A';
          ?>
          <tr>
            <td><?= htmlspecialchars($stu['firstname'] . " " . $stu['lastname']) ?></td>
            <td><?= htmlspecialchars($stu['semester']) ?></td>
            <td><?= htmlspecialchars($stu['department']) ?></td>
            <td>
              <button type="submit" name="mark[<?= $stu['id'] ?>]" value="P"
                style="padding:6px 14px; background:<?= $todayStatus==='P'?'#4CAF50':'#ddd' ?>; color:white; border:none; border-radius:5px;">
                P
              </button>
              <button type="submit" name="mark[<?= $stu['id'] ?>]" value="A"
                style="padding:6px 14px; background:<?= $todayStatus==='A'?'#f44336':'#ddd' ?>; color:white; border:none; border-radius:5px;">
                A
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </form>
  </section>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
