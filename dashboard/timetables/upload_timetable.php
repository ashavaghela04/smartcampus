<?php
session_start();
require_once __DIR__ . "/../../db/db.php";

// ✅ Only Admin/Faculty
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$userId = $_SESSION['user_id'];
$uploadedBy = $_SESSION['username'] ?? '';

if (isset($_POST['upload'])) {
    $department = $_POST['department'] ?? '';
    $semester   = $_POST['semester'] ?? '';
    $section    = $_POST['section'] ?? '';
    $file       = $_FILES['timetable_file'] ?? null;

    if ($file && $file['error'] === 0) {
        // ✅ Upload folder path
        $uploadDir = __DIR__ . '/../../uploads/timetables/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // create folder if not exists
        }

        $filename = "timetable_" . time() . "_" . basename($file['name']);
        $fullPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            // Save relative path in DB
            $dbFilePath = "uploads/timetables/" . $filename;

            $stmt = $pdo->prepare("INSERT INTO timetables 
                (department, semester, section, timetable_file, uploaded_by, faculty_id) 
                VALUES (:department, :semester, :section, :file, :uploaded_by, :faculty_id)");
            $stmt->execute([
                ':department'   => $department,
                ':semester'     => $semester,
                ':section'      => $section,
                ':file'         => $dbFilePath,
                ':uploaded_by'  => $uploadedBy,
                ':faculty_id'   => $userId
            ]);

            $_SESSION['msg'] = "✅ Timetable uploaded successfully!";
            header("Location: view_timetable.php");
            exit;
        } else {
            $error = "❌ Failed to move uploaded file.";
        }
    } else {
        $error = "❌ Please select a valid file.";
    }
}

$pageTitle  = "Upload Timetable";
$activePage = "timetable";
include __DIR__ . '/../includes/header.php';

// ✅ Dynamic sidebar
if ($_SESSION['user_type'] === "admin") {
    include __DIR__ . '/../includes/sidebar_admin.php';
} else {
    include __DIR__ . '/../includes/sidebar_faculty.php';
}
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>📤 Upload Timetable</h1>

    <?php if (!empty($error)) echo "<div style='color:red;'>$error</div>"; ?>
    <?php if (!empty($_SESSION['msg'])) echo "<div style='color:green;'>".$_SESSION['msg']."</div>"; unset($_SESSION['msg']); ?>

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="department" placeholder="Department" required><br><br>
      <input type="text" name="semester" placeholder="Semester" required><br><br>
      <input type="text" name="section" placeholder="Section" required><br><br>
      <input type="file" name="timetable_file" required><br><br>
      <button type="submit" name="upload">Upload Timetable</button>
    </form>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
