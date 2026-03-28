<?php 
session_start();  
$pageTitle = "Student Dashboard"; 
$activePage = "dashboard";  

require_once __DIR__ . '/../../db/db.php'; 
require_once __DIR__ . '/../attedance/functions.php'; 
require_once __DIR__ . '/../attedance/auth.php'; 

require_login();  // Ensure student logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: /smartcampus/home.php");
    exit();
}

$pdo = Database::getInstance();
$student_id = $_SESSION['user_id'];

// Current or selected month
if (isset($_GET['month'])) {
    $year_month = $_GET['month'];
    $today = new DateTime($year_month . "-01");
} else {
    $today = new DateTime();
    $year_month = $today->format("Y-m");
}
$daysInMonth = days_in_month($today->format("m"), $today->format("Y"));

// Get student info
$stmt = $pdo->prepare("SELECT firstname, lastname FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Get attendance record
$record = get_attendance_record($pdo, $student_id, $year_month);
if ($record === false) {
    $record = array_fill(0, $daysInMonth, '-'); // default
}

// Attendance summary
$totalPresent   = count(array_filter($record, fn($s) => $s === 'P'));
$totalAbsent    = count(array_filter($record, fn($s) => $s === 'A'));
$totalNotTaken  = count(array_filter($record, fn($s) => $s === '-'));
$effectiveDays  = $totalPresent + $totalAbsent;
$attendancePercent = $effectiveDays > 0 ? round(($totalPresent / $effectiveDays) * 100, 2) : 0;

// Fetch recent announcements (limit 5)
$stmt = $pdo->query("SELECT title, created_at FROM announcements ORDER BY created_at DESC LIMIT 5");
$recentAnnouncements = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="shell">
    <?php include __DIR__ . '/../includes/topbar.php'; ?>

    <main class="container">
        <!-- KPI Cards -->
        <section class="grid cols-3" aria-label="Quick stats">
            <div class="card">
                <span class="pill">Attendance</span>
                <h3>Current Month</h3>
                <div class="stat">
                    <?= $attendancePercent ?>% 
                    <span class="muted">(<?= $totalPresent ?>/<?= $effectiveDays ?>)</span>
                </div>
            </div>
            <div class="card">
                <span class="pill">Notices</span>
                <h3>Unread</h3>
                <div class="stat">4 <span class="muted">since Monday</span></div>
            </div>
            <div class="card">
                <span class="pill">GPA</span>
                <h3>Semester 5</h3>
                <div class="stat">8.7 <span class="muted">updated today</span></div>
            </div>
        </section>

        <div class="spacer"></div>

        <!-- Month Selector -->
        <section class="card">
            <h3>Select Month</h3>
            <form method="GET" id="monthForm">
                <input type="month" name="month" value="<?= $year_month ?>" onchange="document.getElementById('monthForm').submit()">
            </form>
        </section>

        <div class="spacer"></div>

        <!-- Attendance Chart -->
        <section class="card" aria-label="Attendance Overview">
            <h3>Attendance Overview (<?= $today->format("F Y") ?>)</h3>
            <div style="width:40%; max-width:300px; margin:20px auto;">
                <canvas id="attendanceChart"></canvas>
            </div>
            <p style="text-align:center; margin-top:10px;">
                ✅ Present: <?= $totalPresent ?> &nbsp; | 
                ❌ Absent: <?= $totalAbsent ?> &nbsp; | 
                ⏺ Not Taken: <?= $totalNotTaken ?> <br>
                📊 Attendance %: <strong><?= $attendancePercent ?>%</strong>
            </p>
        </section>

        <div class="spacer"></div>

        <!-- Timetable -->
        <section class="card" aria-label="Today timetable">
            <h3>Today • Time Table (Friday)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Course</th>
                        <th>Faculty</th>
                        <th>Room</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>09:00</td><td>DBMS</td><td>Dr. Rao</td><td>B-204</td><td><span class="badge success">On time</span></td></tr>
                    <tr><td>11:00</td><td>OS</td><td>Mrs. Kapoor</td><td>B-102</td><td><span class="badge success">On time</span></td></tr>
                    <tr><td>14:00</td><td>CN</td><td>Mr. Singh</td><td>Lab-3</td><td><span class="badge warn">Lab prep</span></td></tr>
                </tbody>
            </table>
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
            <a href="/smartcampus/dashboard/students/notice_board.php" class="btn btn-primary mt-3">View All</a>
        </div>

        <div class="spacer"></div>
    </main>
</div>

<!-- ✅ Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Present', 'Absent', 'Not Taken'],
        datasets: [{
            data: [<?= $totalPresent ?>, <?= $totalAbsent ?>, <?= $totalNotTaken ?>],
            backgroundColor: ['green', 'red', 'gray']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { 
                position: 'bottom', 
                labels: { font: { size: 12 } } 
            },
            title: { display: false }
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
