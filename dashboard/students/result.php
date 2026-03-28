<?php
session_start();
$pageTitle  = "My Results";
$activePage = "results";

require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "student") {
  header("Location: ../login.php"); exit;
}

$pdo = Database::getInstance();
$studentId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT r.subject, r.marks, r.exam_type, r.remarks, r.uploaded_at, r.updated_at,
         f.fname, f.lname
  FROM results r
  JOIN faculty f ON f.id = r.faculty_id
  WHERE r.student_id = ?
  ORDER BY r.uploaded_at DESC
");
$stmt->execute([$studentId]);
$rows = $stmt->fetchAll();
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>My Results</h1>

    <div class="card">
      <table class="table">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Exam Type</th>
            <th>Marks</th>
            <th>Remarks</th>
            <th>Faculty</th>
            <th>Uploaded</th>
            <th>Updated</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="7">No results yet.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['subject']) ?></td>
              <td><?= htmlspecialchars($r['exam_type']) ?></td>
              <td><?= htmlspecialchars($r['marks']) ?></td>
              <td><?= htmlspecialchars($r['remarks']) ?></td>
              <td><?= htmlspecialchars(($r['fname'] ?? '').' '.($r['lname'] ?? '')) ?></td>
              <td><?= htmlspecialchars($r['uploaded_at']) ?></td>
              <td><?= htmlspecialchars($r['updated_at']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>