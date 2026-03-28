<?php
session_start();
$pageTitle  = "Manage Faculty";
$activePage = "faculty";

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

// ✅ Fetch all faculty
$stmt = $pdo->query("
    SELECT 
        id,
        fname,
        lname,
        email,
        department,
        phone,
        created_at
    FROM faculty
    ORDER BY created_at DESC
");
$faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Faculty Management</h1>
    <p>View, edit, or remove faculty records.</p>

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
              <th>Email</th>
              <th>Department</th>
              <th>Phone</th>
              <th>Registered At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($faculties): ?>
              <?php foreach ($faculties as $faculty): ?>
                <tr>
                  <td><?= htmlspecialchars($faculty['id']) ?></td>
                  <td><?= htmlspecialchars($faculty['fname'] . ' ' . $faculty['lname']) ?></td>
                  <td><?= htmlspecialchars($faculty['email']) ?></td>
                  <td><?= htmlspecialchars($faculty['department']) ?></td>
                  <td><?= htmlspecialchars($faculty['phone']) ?></td>
                  <td><?= htmlspecialchars($faculty['created_at']) ?></td>
                  <td>
                    <!-- View Button -->
                    <a href="view-faculty.php?id=<?= $faculty['id'] ?>" 
                      class="btn btn-sm btn-info">
                      View
                      </a>
                    <!-- Delete Button -->
                    <a href="delete-faculty.php?id=<?= $faculty['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this faculty?');">
                      Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">No faculty records found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
