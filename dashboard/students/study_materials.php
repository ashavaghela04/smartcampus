<?php
// student_materials.php
session_start();
$pageTitle  = "View Study Materials";
$activePage = "student_materials";

// ✅ Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "student") {
    header("Location: ../login.php");
    exit;
}

// DB config
$host = "localhost";
$dbname = "smartcampus_1";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Fetch uploaded materials with faculty info
$sql = "SELECT m.id, m.subject, m.file_name, m.file_path, m.uploaded_at, 
               CONCAT(f.fname, ' ', f.lname) AS faculty_name
        FROM materials m
        JOIN faculty f ON m.faculty_id = f.id
        ORDER BY m.uploaded_at DESC";

$result = $conn->query($sql);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Available Study Materials</h1>
    <p>Browse, preview, or download study materials uploaded by your faculty.</p>

    <div class="card">
      <table id="materialsTable" data-search="materials">
        <thead>
          <tr>
            <th>Subject</th>
            <th>File</th>
            <th>Faculty</th>
            <th>Uploaded On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['subject']) ?></td>
                <td><?= htmlspecialchars($row['file_name']) ?></td>
                <td><?= htmlspecialchars($row['faculty_name']) ?></td>
                <td><?= date("d M Y", strtotime($row['uploaded_at'])) ?></td>
                <td>
                  <!-- ✅ Download button -->
                  <a href="../<?= htmlspecialchars($row['file_path']) ?>" download
                     class="btn btn-download">Download</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No study materials available yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<style>
/* ✅ Rounded Action Buttons */
.btn {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 14px;
  text-decoration: none;
  margin: 2px;
  transition: background 0.2s;
}
.btn-preview {
  background: #2196F3;
  color: white;
}
.btn-preview:hover {
  background: #1976D2;
}
.btn-download {
  background: #4CAF50;
  color: white;
}
.btn-download:hover {
  background: #388E3C;
}
</style>
