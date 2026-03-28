<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT r.*, s.enrollment_number, s.firstname, s.lastname, s.department, s.semester,
           f.fname AS faculty_fname, f.lname AS faculty_lname
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN faculty f ON r.faculty_id = f.id
    WHERE r.id = :id
");
$stmt->execute([':id' => $id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Result not found.");
}
?>
<?php
$pageTitle = "View Result";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <div class="card">
      <div class="card-body">
    
<h2>Result Details</h2>
<p><strong>Student:</strong> <?= htmlspecialchars($result['firstname'] . " " . $result['lastname']) ?> (<?= $result['enrollment_number'] ?>)</p>
<p><strong>Department:</strong> <?= htmlspecialchars($result['department']) ?></p>
<p><strong>Semester:</strong> <?= htmlspecialchars($result['semester']) ?></p>
<p><strong>Subject:</strong> <?= htmlspecialchars($result['subject']) ?></p>
<p><strong>Marks:</strong> <?= htmlspecialchars($result['marks']) ?></p>
<p><strong>Exam Type:</strong> <?= htmlspecialchars($result['exam_type']) ?></p>
<p><strong>Remarks:</strong> <?= $result['remarks'] ? htmlspecialchars($result['remarks']) : '-' ?></p>
<p><strong>Faculty:</strong> <?= htmlspecialchars($result['faculty_fname'] . " " . $result['faculty_lname']) ?></p>
<p><strong>Uploaded:</strong> <?= htmlspecialchars($result['uploaded_at']) ?></p>
<a href="admin_results_manage.php">Back</a>
        </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>



