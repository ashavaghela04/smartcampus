<?php
session_start();
require_once __DIR__ . "/../../db/db.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "student") {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();

// ✅ Show ALL timetables
$stmt = $pdo->query("SELECT * FROM timetables ORDER BY id DESC");
$timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle  = "My Timetable";
$activePage = "timetable";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>


<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>📅 My Timetable</h1>

    <?php if (empty($timetables)): ?>
      <p>No timetable uploaded yet.</p>
    <?php else: ?>
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-top:20px;">
        <?php foreach ($timetables as $tt): 
          $filePath = "/smartcampus/" . $tt['timetable_file'];
        ?>
          <div class="card">
            <p><strong>Department:</strong> <?= htmlspecialchars($tt['department']) ?></p>
            <p><strong>Semester:</strong> <?= htmlspecialchars($tt['semester']) ?></p>
            <p><strong>Section:</strong> <?= htmlspecialchars($tt['section']) ?></p>

            <a href="<?= htmlspecialchars($filePath) ?>" target="_blank" class="action-btn">
              View / Download
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
