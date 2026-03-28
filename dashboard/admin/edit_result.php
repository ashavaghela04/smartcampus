<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject   = $_POST['subject'];
    $marks     = $_POST['marks'];
    $exam_type = $_POST['exam_type'];
    $remarks   = $_POST['remarks'];

    $stmt = $pdo->prepare("UPDATE results 
                           SET subject=?, marks=?, exam_type=?, remarks=?, updated_at=NOW() 
                           WHERE id=?");
    $stmt->execute([$subject, $marks, $exam_type, $remarks, $id]);

    header("Location: manage_results.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM results WHERE id=?");
$stmt->execute([$id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Result not found.");
}
?>
<?php
$pageTitle = "Edit Result";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <div class="card">
      <div class="card-body">
        <h2>Edit Result</h2>
        <form method="post">

          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject" value="<?= htmlspecialchars($result['subject']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Marks</label>
            <input type="number" class="form-control" name="marks" value="<?= htmlspecialchars($result['marks']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Exam Type</label>
            <input type="text" class="form-control" name="exam_type" value="<?= htmlspecialchars($result['exam_type']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" name="remarks"><?= htmlspecialchars($result['remarks']) ?></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Save Changes</button>
          <a href="manage_results.php" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
