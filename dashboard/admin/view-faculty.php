<?php
session_start();
$pageTitle  = "View Faculty";
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

// ✅ Get faculty by ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM faculty WHERE id = ?");
$stmt->execute([$id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    die("Faculty not found.");
}
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Faculty Details</h1>

    <div class="card">
      <div class="card-body">
        <table class="table table-bordered">
          <tr><th>ID</th><td><?= htmlspecialchars($faculty['id']) ?></td></tr>
          <tr><th>First Name</th><td><?= htmlspecialchars($faculty['fname']) ?></td></tr>
          <tr><th>Last Name</th><td><?= htmlspecialchars($faculty['lname']) ?></td></tr>
          <tr><th>Email</th><td><?= htmlspecialchars($faculty['email']) ?></td></tr>
          <tr><th>Phone</th><td><?= htmlspecialchars($faculty['phone']) ?></td></tr>
          <tr><th>Date of Birth</th><td><?= htmlspecialchars($faculty['dob']) ?></td></tr>
          <tr><th>Gender</th><td><?= htmlspecialchars($faculty['gender']) ?></td></tr>
          <tr><th>Address</th><td><?= nl2br(htmlspecialchars($faculty['address'])) ?></td></tr>
          <tr><th>Department</th><td><?= htmlspecialchars($faculty['department']) ?></td></tr>
          <tr><th>Designation</th><td><?= htmlspecialchars($faculty['designation']) ?></td></tr>
          <tr><th>Qualification</th><td><?= htmlspecialchars($faculty['qualification']) ?></td></tr>
          <tr><th>Experience</th><td><?= htmlspecialchars($faculty['experience']) ?> years</td></tr>
          <tr><th>Aadhaar</th><td><?= htmlspecialchars($faculty['aadhaar']) ?></td></tr>
          <tr><th>PAN</th><td><?= htmlspecialchars($faculty['pan']) ?></td></tr>
          <tr>
            <th>Photo</th>
            <td>
              <?php if (!empty($faculty['photo'])): ?>
                <img src="../uploads/photos/<?= htmlspecialchars($faculty['photo']) ?>" alt="Faculty Photo" style="max-width:150px;">
              <?php else: ?>
                No Photo
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>Resume</th>
            <td>
              <?php if (!empty($faculty['resume'])): ?>
                <a href="../uploads/resumes/<?= htmlspecialchars($faculty['resume']) ?>" target="_blank">View Resume</a>
              <?php else: ?>
                No Resume
              <?php endif; ?>
            </td>
          </tr>
          <tr><th>Registered At</th><td><?= htmlspecialchars($faculty['created_at']) ?></td></tr>
        </table>
      </div>
    </div>

    <a href="admin_faculty_manage.php" class="btn btn-secondary mt-3">Back</a>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
