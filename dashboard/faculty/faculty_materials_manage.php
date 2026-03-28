<?php
session_start();
$pageTitle  = "My Materials";
$activePage = "faculty_materials";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

// ✅ Ensure faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "faculty") {
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
    die("DB connection failed: " . $conn->connect_error);
}

$facultyId = $_SESSION['user_id'];

// ✅ Fetch materials uploaded by this faculty
$stmt = $conn->prepare("SELECT id, subject, file_name, file_path, uploaded_at FROM materials WHERE faculty_id = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $facultyId);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container py-5">
    <section class="card shadow-lg p-4 rounded-3">
      <h1 class="mb-4 text-primary">📂 My Uploaded Materials</h1>

      <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Subject</th>
              <th>File Name</th>
              <th>Uploaded At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="materialsTableBody">
  <?php 
  $i = 1;
  while ($row = $result->fetch_assoc()): 
  ?>
    <tr data-id="<?= $row['id'] ?>">
      <td><?= $i++ ?></td>
      <td><?= htmlspecialchars($row['subject']) ?></td>
      <td><?= htmlspecialchars($row['file_name']) ?></td>
      <td><?= date("d M Y, h:i A", strtotime($row['uploaded_at'])) ?></td>
      <td>
        <!-- Download Button -->
        <form action="../<?= $row['file_path'] ?>" method="get" style="display:inline;">
          <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
            <i class="fas fa-download"></i> Download
          </button>
        </form>

        <!-- Delete Button (AJAX) -->
        <button type="button" 
                class="btn btn-danger btn-sm rounded-pill px-3 deleteBtn" 
                data-id="<?= $row['id'] ?>">
          <i class="fas fa-trash"></i> Delete
        </button>
      </td>
    </tr>
  <?php endwhile; ?>
</tbody>

        </table>
      <?php else: ?>
        <div class="alert alert-info">No materials uploaded yet.</div>
      <?php endif; ?>

    </section>
  </main>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".deleteBtn").forEach(button => {
    button.addEventListener("click", async function() {
      const materialId = this.getAttribute("data-id");
      const row = this.closest("tr");

      if (!confirm("Are you sure you want to delete this material?")) {
        return;
      }

      try {
        const response = await fetch("materials_delete.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "id=" + materialId
        });

        const result = await response.json();

        if (result.success) {
          row.remove(); // ✅ instantly remove row from table
        } else {
          alert("Error: " + result.message);
        }
      } catch (error) {
        alert("⚠️ Could not delete file. Please try again.");
      }
    });
  });
});
</script>


<?php include __DIR__ . '/../includes/footer.php'; ?>
