<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
include __DIR__ . '/../includes/header.php';
// ✅ Only Admin/Faculty allowed
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$uploadedBy = $_SESSION['username'] ?? 'Unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $message = trim($_POST['message']);

    if ($title && $message) {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, message, uploaded_by, created_at) VALUES (:title, :message, :uploaded_by, NOW())");
        $stmt->execute([
            ':title'       => $title,
            ':message'     => $message,
            ':uploaded_by' => $uploadedBy
        ]);
        header("Location: announcements.php");
        exit;
    }
}
?>


<?php include __DIR__ . '/../includes/sidebar_' . $_SESSION['user_type'] . '.php'; ?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>

<main class="container py-5">
  <h1 class="mb-4">➕ Add Announcement</h1>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Save</button>
    <a href="announcements.php" class="btn btn-secondary">Cancel</a>
  </form>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
