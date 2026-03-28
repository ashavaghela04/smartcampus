<?php
session_start();
$pageTitle  = "Manage Results";
$activePage = "results";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// ✅ Filters
$department = $_GET['department'] ?? '';
$semester   = $_GET['semester'] ?? '';
$subject    = $_GET['subject'] ?? '';

$where = [];
$params = [];

if ($department) {
    $where[] = "s.department = :department";
    $params[':department'] = $department;
}
if ($semester) {
    $where[] = "s.semester = :semester";
    $params[':semester'] = $semester;
}
if ($subject) {
    $where[] = "r.subject LIKE :subject";
    $params[':subject'] = "%$subject%";
}

$sql = "
    SELECT r.id, s.enrollment_number, s.firstname, s.lastname, s.department, s.semester,
           r.subject, r.marks, r.exam_type, r.remarks, r.uploaded_at,
           f.fname AS faculty_fname, f.lname AS faculty_lname
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN faculty f ON r.faculty_id = f.id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY r.uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Results Management</h1>

    <!-- 🔎 Filter Form -->
    <form method="get" class="filter-form">
      <input type="text" name="subject" placeholder="Search subject..." value="<?= htmlspecialchars($subject) ?>">
      <input type="text" name="department" placeholder="Department..." value="<?= htmlspecialchars($department) ?>">
      <input type="text" name="semester" placeholder="Semester..." value="<?= htmlspecialchars($semester) ?>">
      <button type="submit">Filter</button>
      <a href="admin_results_manage.php">Reset</a>
    </form>

    <div class="card">
      <?php if ($results): ?>
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Enrollment No</th>
              <th>Student Name</th>
              <th>Department</th>
              <th>Semester</th>
              <th>Subject</th>
              <th>Marks</th>
              <th>Exam Type</th>
              <th>Remarks</th>
              <th>Faculty</th>
              <th>Uploaded At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $i => $row): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['enrollment_number']) ?></td>
                <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td><?= htmlspecialchars($row['subject']) ?></td>
                <td><?= htmlspecialchars($row['marks']) ?></td>
                <td><?= htmlspecialchars($row['exam_type']) ?></td>
                <td><?= $row['remarks'] ? htmlspecialchars($row['remarks']) : '-' ?></td>
                <td><?= htmlspecialchars($row['faculty_fname'] . " " . $row['faculty_lname']) ?></td>
                <td><?= htmlspecialchars($row['uploaded_at']) ?></td>
                <td>
                  <a href="view_result.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                  <a href="edit_result.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_result.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Delete this result?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No results found.</p>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
