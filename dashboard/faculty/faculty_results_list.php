<?php
session_start();
$pageTitle  = "My Uploaded Results";
$activePage = "results_list";

require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_faculty.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "faculty") {
  header("Location: ../login.php"); exit;
}

$pdo = Database::getInstance();
$facultyId = (int) $_SESSION['user_id'];

// Optional search/filter
$q = trim($_GET['q'] ?? '');

$sql = "SELECT r.id, r.student_id, r.subject, r.exam_type, r.marks, r.remarks, r.uploaded_at, r.updated_at,
               s.firstname, s.lastname
        FROM results r
        JOIN students s ON s.id = r.student_id
        WHERE r.faculty_id = :fid";

$params = [':fid' => $facultyId];

if ($q !== '') {
  $sql .= " AND (s.firstname LIKE :q OR s.lastname LIKE :q OR r.subject LIKE :q OR r.exam_type LIKE :q)";
  $params[':q'] = "%{$q}%";
}

$sql .= " ORDER BY r.uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h1>My Uploaded Results</h1>
      <a class="btn btn--primary" href="faculty_results.php">+ Upload New</a>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert--success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="get" style="margin:12px 0;">
      <input type="text" name="q" placeholder="Search student/subject/exam type…" value="<?= htmlspecialchars($q) ?>">
      <button class="btn">Search</button>
      <?php if ($q !== ''): ?><a class="btn" href="faculty_results_list.php">Reset</a><?php endif; ?>
    </form>

    <div class="card">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Subject</th>
            <th>Exam Type</th>
            <th>Marks</th>
            <th>Remarks</th>
            <th>Uploaded</th>
            <th>Updated</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="9">No results found.</td></tr>
        <?php else: foreach ($rows as $i => $r): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars(($r['firstname'] ?? '').' '.($r['lastname'] ?? '')." (ID: {$r['student_id']})") ?></td>
            <td><?= htmlspecialchars($r['subject']) ?></td>
            <td><?= htmlspecialchars($r['exam_type']) ?></td>
            <td><?= htmlspecialchars($r['marks']) ?></td>
            <td><?= htmlspecialchars($r['remarks']) ?></td>
            <td><?= htmlspecialchars($r['uploaded_at']) ?></td>
            <td><?= htmlspecialchars($r['updated_at']) ?></td>
            <td>
              <form action="faculty_results_delete.php" method="POST" onsubmit="return confirm('Delete this result?');" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn--danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
