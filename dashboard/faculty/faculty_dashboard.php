<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /smartcampus/home.php");
    exit;
}

$pageTitle  = "Faculty Dashboard";
$activePage = "faculty_dashboard"; 

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();
require_once __DIR__ . '/../attedance/functions.php';

// Get today's date
$today = new DateTime();
$period = $today->format("Y-m");
$daysInMonth = days_in_month($today->format("m"), $today->format("Y"));

// Fetch courses assigned to this faculty
$facultyId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT DISTINCT subject AS course_name, section FROM timetables WHERE faculty_id = ?");
$stmt->execute([$facultyId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare attendance data for chart
$attendanceData = [];
foreach ($courses as $course) {
    $stmt = $pdo->prepare("SELECT id FROM students WHERE section = ?");
    $stmt->execute([$course['section']]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPercent = 0;
    $count = 0;

    foreach ($students as $stu) {
        $record = get_attendance_record($pdo, $stu['id'], $period);
        if ($record === false) {
            $record = create_attendance_record($pdo, $stu['id'], $period, $daysInMonth);
        }
        $presentDays = count(array_filter($record, fn($status) => $status === 'P'));
        $percent = $daysInMonth > 0 ? round(($presentDays / $daysInMonth) * 100, 2) : 0;
        $totalPercent += $percent;
        $count++;
    }

    $avgAttendance = $count > 0 ? round($totalPercent / $count, 2) : 0;
    $attendanceData[] = [
        'label' => $course['course_name'] . ' (' . $course['section'] . ')',
        'attendance' => $avgAttendance
    ];
}
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">

    <!-- Attendance Chart -->
    <section class="card">
      <h3>Average Student Attendance per Course/Section (<?= $today->format("F Y") ?>)</h3>
      <canvas id="attendanceChart" width="400" height="150"></canvas>
    </section>

    <div class="spacer"></div>

    <!-- Results Management -->
    <section class="card">
      <h3>Results Management</h3>
      <table>
        <thead>
          <tr><th>Exam</th><th>Course</th><th>Section</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
          <tr><td>Midterm</td><td>DBMS</td><td>CSE-5A</td><td>Published</td>
              <td><a class="badge" href="#">View</a></td></tr>
          <tr><td>Quiz 1</td><td>OS</td><td>CSE-5B</td><td>Pending</td>
              <td><a class="badge success" href="#">Upload</a></td></tr>
        </tbody>
      </table>
    </section>

  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('attendanceChart').getContext('2d');
const labels = <?= json_encode(array_column($attendanceData, 'label')) ?>;
const data = <?= json_encode(array_column($attendanceData, 'attendance')) ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Avg Attendance (%)',
            data: data,
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, max: 100, title: { display: true, text: 'Attendance (%)' } },
            x: { title: { display: true, text: 'Course / Section' } }
        }
    }
});
</script>

<style>
body.dark-mode { background-color: #121212; color: #eaeaea; }
body.dark-mode .card { background-color: #1f1f1f; color: #f1f1f1; }
.spacer { height: 20px; }
</style>
