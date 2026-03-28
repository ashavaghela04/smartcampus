<?php
$pageTitle  = "Add/Edit Announcement";
$activePage = "faculty_announcements";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';
?>

<style>
  /* Extra styling for big input fields */
  .big-input {
    font-size: 1.2rem;
    padding: 1rem;
  }
  .big-textarea {
    font-size: 1.2rem;
    padding: 1rem;
    min-height: 250px; /* Bigger message box */
    resize: vertical;  /* Allow resizing only vertically */
  }
</style>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10 col-sm-12">
        <div class="card shadow-lg rounded-4 p-5">
          <h1 class="mb-5 text-center display-5">📢 Add Announcement</h1> <!-- added more bottom margin -->

          <form method="post" action="announcement_save.php">
            <!-- Title -->
            <div class="mb-5"> <!-- increased space -->
              <label class="form-label fw-bold fs-5">Title</label>
              <input type="text" name="title" class="form-control form-control-lg big-input" placeholder="Enter announcement title" required>
            </div>

            <!-- Message -->
            <div class="mb-5"> <!-- increased space -->
              <label class="form-label fw-bold fs-5">Message</label>
              <textarea name="message" class="form-control form-control-lg big-textarea" placeholder="Write your announcement here..." required></textarea>
            </div>

            <!-- Buttons -->
<div class="d-flex justify-content-center gap-4 mt-5"> 
  <button type="submit" class="btn btn-primary btn-lg px-5">✅ Post</button>
  <a href="faculty_announcements.php" class="btn btn-outline-secondary btn-lg px-5">❌ Cancel</a>
</div>

          </form>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
