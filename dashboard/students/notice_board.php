<?php
$pageTitle  = "Student Announcements";
$activePage = "student_announcements";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../../db/db.php';

// ✅ Fetch announcements
$pdo = Database::getInstance();
$stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Featured & others
$featured = $announcements[0] ?? null;
$others   = array_slice($announcements, 1);

// ✅ Helper: Check if announcement is "new"
function isNew($date) {
  $created = strtotime($date);
  return (time() - $created) < 86400; // 24 hours
}
?>

<style>
  .announcement-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s;
    background-color: var(--bs-card-bg);
    color: var(--bs-body-color);
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
  .featured-card {
  background: var(--bs-primary-bg-subtle);
  color: var(--bs-body-color);
  padding: 2rem;
  border-radius: 1rem;
}

.featured-card h2 {
  font-size: 1.8rem;
  font-weight: bold;
  color: var(--bs-heading-color, var(--bs-body-color));
}

.featured-card p {
  font-size: 1.1rem;
  color: var(--bs-body-color);
}

  .badge-new {
    background-color: #ff4757;
    font-size: 0.8rem;
    padding: 0.4em 0.6em;
    border-radius: 0.5rem;
    margin-left: 8px;
  }
</style>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container py-5">
    <h1 class="display-6 mb-4">📢 Latest Announcements</h1>

    <!-- ✅ Featured Announcement -->
    <?php if ($featured): ?>
      <div class="featured-card shadow-lg mb-5">
        <h2>
          <?= htmlspecialchars($featured['title']) ?>
          <?php if (isNew($featured['created_at'])): ?>
            <span class="badge-new">🆕 New</span>
          <?php endif; ?>
        </h2>
        <p class="mt-3"><?= nl2br(htmlspecialchars($featured['message'])) ?></p>
        <small class="d-block mt-3">📅 <?= date("d M Y, h:i A", strtotime($featured['created_at'])) ?></small>
      </div>
    <?php endif; ?>

    <!-- ✅ Other Announcements -->
    <div class="row g-4">
      <?php if (count($others) > 0): ?>
        <?php foreach ($others as $row): ?>
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card announcement-card shadow-sm rounded-4 h-100">
              <div class="card-body d-flex flex-column">
                <h5 class="announcement-title">
                  <?= htmlspecialchars($row['title']) ?>
                  <?php if (isNew($row['created_at'])): ?>
                    <span class="badge-new">🆕 New</span>
                  <?php endif; ?>
                </h5>
                <p class="announcement-message mt-2"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                <div class="mt-auto">
                  <small class="text-muted">
                    📅 <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?>
                  </small>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center text-muted">No more announcements available.</p>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
