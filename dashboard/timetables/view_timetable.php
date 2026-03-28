<?php
session_start();
require_once __DIR__ . "/../../db/db.php";

// ✅ Only Admin/Faculty
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo      = Database::getInstance();
$userId   = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

// ✅ Handle delete request
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Fetch timetable info
    $stmt = $pdo->prepare("SELECT timetable_file, faculty_id FROM timetables WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Only admin OR the faculty who uploaded it can delete
        if ($userType === "admin" || $row['faculty_id'] == $userId) {
            $filePath = __DIR__ . "/../../" . $row['timetable_file'];

            if (file_exists($filePath)) {
                unlink($filePath); // delete file
            }

            // Delete DB row
            $stmt = $pdo->prepare("DELETE FROM timetables WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $_SESSION['msg'] = "✅ Timetable deleted successfully!";
        } else {
            $_SESSION['msg'] = "❌ You are not allowed to delete this timetable.";
        }
    }
    header("Location: view_timetable.php");
    exit;
}

// ✅ Fetch timetables
if ($userType === "faculty") {
    $stmt = $pdo->prepare("SELECT * FROM timetables WHERE faculty_id = :faculty_id ORDER BY id DESC");
    $stmt->execute([':faculty_id' => $userId]);
} else {
    $stmt = $pdo->query("SELECT * FROM timetables ORDER BY id DESC");
}
$timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle  = "View Timetables";
$activePage = "timetable";
include __DIR__ . '/../includes/header.php';

// ✅ Sidebar
if ($userType === "admin") {
    include __DIR__ . '/../includes/sidebar_admin.php';
} else {
    include __DIR__ . '/../includes/sidebar_faculty.php';
}
?>


<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>📖 Uploaded Timetables</h1>

    <?php if (!empty($_SESSION['msg'])): ?>
      <div style="margin-bottom:10px; color:<?= strpos($_SESSION['msg'], '✅') !== false ? 'green' : 'red'; ?>;">
        <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
      </div>
    <?php endif; ?>

    <?php if (empty($timetables)) : ?>
        <p>No timetables uploaded yet.</p>
    <?php else : ?>
        <div class="timetable-grid" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-top:20px;">
            <?php foreach ($timetables as $tt) : 
                $filePath = "/smartcampus/" . $tt['timetable_file'];
            ?>
                <div class="card">
                    <p><strong>Department:</strong> <?= htmlspecialchars($tt['department']) ?></p>
                    <p><strong>Semester:</strong> <?= htmlspecialchars($tt['semester']) ?></p>
                    <p><strong>Section:</strong> <?= htmlspecialchars($tt['section']) ?></p>
                    <p><strong>Uploaded By:</strong> <?= htmlspecialchars($tt['uploaded_by']) ?></p>

                    <div style="margin-top:10px; display:flex; gap:10px;">
                        <a href="<?= htmlspecialchars($filePath) ?>" target="_blank" class="action-btn view-btn">View / Download</a>
                        
                        <?php if ($userType === "admin" || $tt['faculty_id'] == $userId): ?>
                          <a href="?delete=<?= $tt['id'] ?>" onclick="return confirm('Are you sure you want to delete this timetable?');" class="action-btn delete-btn">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
