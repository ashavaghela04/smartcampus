<?php
session_start();
$pageTitle  = "Manage Students";
$activePage = "students";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// ✅ DB connection
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// ✅ Fetch all approved students
$stmt = $pdo->query("
    SELECT 
        id,
        firstname,
        lastname,
        username,
        email,
        gender,
        phone,
        program,
        year,
        created_at
    FROM students
    WHERE approved = 1
    ORDER BY created_at DESC
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1>Student Management</h1>
      <a href="add-student.php" class="btn btn-success">Add New Student</a>
    </div>

    <!-- ✅ Show success/error messages -->
    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Gender</th>
              <th>Phone</th>
              <th>Program / Year</th>
              <th>Registered At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($students): ?>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?= htmlspecialchars($student['id']) ?></td>
                  <td><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) ?></td>
                  <td><?= htmlspecialchars($student['username']) ?></td>
                  <td><?= htmlspecialchars($student['email']) ?></td>
                  <td><?= htmlspecialchars($student['gender']) ?></td>
                  <td><?= htmlspecialchars($student['phone']) ?></td>
                  <td><?= htmlspecialchars($student['program'] . ' / ' . $student['year']) ?></td>
                  <td><?= htmlspecialchars($student['created_at']) ?></td>
                  <td>
                    <a href="edit-student.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete-student.php?id=<?= $student['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this student?');">
                       Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="text-center">No students found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
