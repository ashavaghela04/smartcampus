<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// ✅ Only Admin/Faculty allowed
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$id  = $_GET['id'] ?? null;
if (!$id) { header("Location: announcements.php"); exit; }

// ✅ Fetch existing
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$announcement) {
    die("Announcement not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $message = trim($_POST['message']);

    if ($title && $message) {
        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, message = ? WHERE id = ?");
        $stmt->execute([$title, $message, $id]);
        header("Location: announcements.php");
        exit;
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/sidebar_' . $_SESSION['user_type'] . '.php'; ?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>

<main class="container py-5">
  <h1 class="mb-4">✏️ Edit Announcement</h1>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($announcement['title']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control" rows="5" required><?= htmlspecialchars($announcement['message']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="announcements.php" class="btn btn-secondary">Cancel</a>
  </form>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
