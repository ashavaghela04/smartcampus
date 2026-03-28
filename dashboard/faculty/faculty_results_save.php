<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "faculty") {
  http_response_code(403);
  exit("Unauthorized.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: faculty_results_upload.php");
  exit;
}

$pdo = Database::getInstance();
$facultyId = (int) $_SESSION['user_id'];

$studentId = (int) ($_POST['student_id'] ?? 0);
$subject   = trim($_POST['subject'] ?? '');
$marks     = trim($_POST['marks'] ?? '');
$examType  = trim($_POST['exam_type'] ?? '');
$remarks   = trim($_POST['remarks'] ?? '');

// Basic validation
$errors = [];
if ($studentId <= 0)      $errors[] = "Invalid student ID.";
if ($subject === '')      $errors[] = "Subject is required.";
if ($examType === '')     $errors[] = "Exam type is required.";
if ($marks === '' || !is_numeric($marks)) $errors[] = "Marks must be a number.";
$marks = (int) $marks;
if ($marks < 0 || $marks > 100) $errors[] = "Marks must be between 0 and 100.";

try {
  // Ensure student exists (and optionally approved = 1)
  $s = $pdo->prepare("SELECT id, approved FROM students WHERE id = ?");
  $s->execute([$studentId]);
  $student = $s->fetch();

  if (!$student) $errors[] = "Student not found.";
  // If you want to restrict uploading to approved students only, uncomment:
  // if (!$student['approved']) $errors[] = "Student not approved yet.";

  if ($errors) {
    // Simple fallback error UI
    echo "<h3>Cannot save:</h3><ul>";
    foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>";
    echo "</ul><p><a href='faculty_results_upload.php'>Go back</a></p>";
    exit;
  }

  // If you added the UNIQUE KEY (student_id, subject, exam_type),
  // this UPSERT will update marks/remarks when duplicate exists.
  $sql = "INSERT INTO results (student_id, faculty_id, subject, marks, exam_type, remarks)
          VALUES (:student_id, :faculty_id, :subject, :marks, :exam_type, :remarks)
          ON DUPLICATE KEY UPDATE
            marks = VALUES(marks),
            remarks = VALUES(remarks),
            updated_at = CURRENT_TIMESTAMP";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':student_id' => $studentId,
    ':faculty_id' => $facultyId,
    ':subject'    => $subject,
    ':marks'      => $marks,
    ':exam_type'  => $examType,
    ':remarks'    => ($remarks !== '' ? $remarks : null),
  ]);

  // Redirect back with a success message
  header("Location: faculty_results_list.php?msg=Result saved");
  exit;

} catch (Throwable $e) {
  // If UNIQUE KEY not present, duplicate entries may be allowed
  // Or you can show the exact DB error for debugging (careful in production)
  echo "<h3>Error</h3><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
  echo "<p><a href='faculty_results_upload.php'>Go back</a></p>";
  exit;
}
