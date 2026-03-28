<?php
session_start();
$pageTitle  = "Admin Dashboard";
$activePage = "dashboard";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// DB connection
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// ---- Fetch Dynamic Data ----
try {
    // Count Students
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM students");
    $studentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Count Faculty
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM faculty");
    $facultyCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Count Announcements
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM announcements");
    $announcementsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Recent 5 announcements
    $stmt = $pdo->query("SELECT title, created_at FROM announcements ORDER BY created_at DESC LIMIT 5");
    $recentAnnouncements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ----- Attendance per Student -----
    require_once __DIR__ . '/../attedance/functions.php';
    $today = new DateTime();
    $period = $today->format("Y-m");
    $daysInMonth = days_in_month($today->format("m"), $today->format("Y"));

    // Fetch all students with attendance %
    $stmt = $pdo->query("SELECT id, firstname, lastname, department FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $deptData = [];
    $deptMap = [];

    foreach ($students as $stu) {
        $record = get_attendance_record($pdo, $stu['id'], $period);
        if ($record === false) {
            $record = create_attendance_record($pdo, $stu['id'], $period, $daysInMonth);
        }
        $presentDays = count(array_filter($record, fn($status) => $status === 'P'));
        $attendancePercent = $daysInMonth > 0 ? round(($presentDays / $daysInMonth) * 100, 2) : 0;

        // Aggregate per department
        $dept = $stu['department'];
        if (!isset($deptMap[$dept])) {
            $deptMap[$dept] = ['total' => 0, 'sumPercent' => 0];
        }
        $deptMap[$dept]['total'] += 1;
        $deptMap[$dept]['sumPercent'] += $attendancePercent;
    }

    foreach ($deptMap as $dept => $val) {
        $avgAttendance = $val['total'] > 0 ? round($val['sumPercent'] / $val['total'], 2) : 0;
        $deptData[] = ['dept' => $dept, 'avgAttendance' => $avgAttendance];
    }

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container py-4">
    <h1 class="mb-4 fw-bold">Welcome, Admin 👋</h1>

    <!-- Dashboard Stats -->
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card dashboard-card shadow rounded-4 p-3 text-center">
          <h2 class="fw-bold"><?= $studentsCount ?></h2>
          <p class="text-muted">Students</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card dashboard-card shadow rounded-4 p-3 text-center">
          <h2 class="fw-bold"><?= $facultyCount ?></h2>
          <p class="text-muted">Faculty</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card dashboard-card shadow rounded-4 p-3 text-center">
          <h2 class="fw-bold"><?= $announcementsCount ?></h2>
          <p class="text-muted">Announcements</p>
        </div>
      </div>
    </div>

    <!-- Attendance Chart -->
    <section class="card shadow rounded-4 p-4 mt-5">
      <h3 class="fw-bold mb-3">Average Attendance by Department (<?= $today->format("F Y") ?>)</h3>
      <canvas id="attendanceChart" width="400" height="150"></canvas>
    </section>

    <!-- Recent Announcements -->
    <div class="card shadow rounded-4 p-4 mt-5">
      <h3 class="fw-bold mb-3">Recent Announcements</h3>
      <ul class="list-group list-group-flush">
        <?php if ($recentAnnouncements): ?>
          <?php foreach ($recentAnnouncements as $a): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= htmlspecialchars($a['title']) ?></span>
              <small class="text-muted"><?= date("M d, Y", strtotime($a['created_at'])) ?></small>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="list-group-item">No announcements yet.</li>
        <?php endif; ?>
      </ul>
      <a href="/smartcampus/dashboard/announcements/announcements.php" class="btn btn-primary mt-3">View All</a>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('attendanceChart').getContext('2d');
const labels = <?= json_encode(array_column($deptData, 'dept')) ?>;
const data = <?= json_encode(array_column($deptData, 'avgAttendance')) ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Avg Attendance (%)',
            data: data,
            backgroundColor: 'rgba(33, 150, 243, 0.7)',
            borderColor: 'rgba(33, 150, 243, 1)',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, max: 100, title: { display: true, text: 'Attendance (%)' } },
            x: { title: { display: true, text: 'Department' } }
        }
    }
});
</script>

<!-- Extra Styling -->
<style>
.dashboard-card {
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}
.dashboard-card:hover {
    transform: translateY(-5px);
}
body.dark-mode {
    background-color: #121212;
    color: #eaeaea;
}
body.dark-mode .card {
    background-color: #1f1f1f;
    color: #f1f1f1;
}
</style>
