<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// ✅ Only Admin/Faculty allowed
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$userType   = $_SESSION['user_type'];       // "admin" or "faculty"
$userId     = $_SESSION['user_id'];
$uploadedBy = $_SESSION['username'] ?? 'Unknown';

// ✅ Page title & sidebar
$pageTitle  = ($userType === 'admin') ? "Admin Announcements" : "Faculty Announcements";
$activePage = "announcements";

include __DIR__ . '/../includes/header.php';
if ($userType === 'admin') {
    include __DIR__ . '/../includes/sidebar_admin.php';
} else {
    include __DIR__ . '/../includes/sidebar_faculty.php';
}

// ✅ Fetch announcements
$pdo = Database::getInstance();
$stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  .announcement-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s;
  }
  .announcement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
  }
  .announcement-title {
    font-size: 1.3rem;
    font-weight: bold;
  }
  .announcement-message {
    font-size: 1rem;
    min-height: 80px;
  }
</style>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="display-6">📢 <?= $pageTitle ?></h1>
      <a href="announcement_form.php" class="btn btn-success btn-lg">➕ Add New</a>
    </div>

    <div class="row g-4">
      <?php if ($announcements): foreach ($announcements as $row): ?>
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card announcement-card shadow-sm rounded-4 h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="announcement-title"><?= htmlspecialchars($row['title']) ?></h5>
              <p class="announcement-message mt-2"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <small class="text-muted">
                  📅 <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?>
                  <?php if (!empty($row['uploaded_by'])): ?>
                      <br>👤 <?= htmlspecialchars($row['uploaded_by']) ?>
                  <?php endif; ?>
                </small>
                <div>
                  <a href="announcement_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                  <a href="announcement_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this announcement?');">🗑 Delete</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <p class="text-center text-muted">No announcements posted yet.</p>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
