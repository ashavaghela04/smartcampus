<?php
session_start();
$pageTitle  = "Upload Result";
$activePage = "results_upload";

require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_faculty.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header("Location: /smartcampus/home.php");
    exit();
}
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>Upload Result</h1>
    <p>Enter a student's result. If a result for the same (Student, Subject, Exam Type) exists, it will be updated.</p>

    <section class="card" style="max-width:700px;">
      <form action="faculty_results_save.php" method="POST" autocomplete="off">
        <div class="grid" style="grid-template-columns:1fr 1fr; gap:16px;">
          <div>
            <label>Student ID <span style="color:#c00">*</span></label>
            <input type="number" name="student_id" required>
            <small>Tip: This is the internal <b>students.id</b></small>
          </div>

          <div>
            <label>Subject <span style="color:#c00">*</span></label>
            <input type="text" name="subject" maxlength="100" required>
          </div>

          <div>
            <label>Marks (0–100) <span style="color:#c00">*</span></label>
            <input type="number" name="marks" min="0" max="100" required>
          </div>

          <div>
            <label>Exam Type <span style="color:#c00">*</span></label>
            <select name="exam_type" required>
              <option value="Midterm">Midterm</option>
              <option value="Final">Final</option>
              <option value="Assignment">Assignment</option>
              <option value="Quiz">Quiz</option>
            </select>
          </div>

          <div style="grid-column:1 / -1">
            <label>Remarks (optional)</label>
            <input type="text" name="remarks" maxlength="255">
          </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:8px;">
          <button type="submit" class="btn btn--primary">Save Result</button>
          <a class="btn" href="faculty_results_list.php">View My Uploaded Results</a>
        </div>
      </form>
    </section>
  </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>