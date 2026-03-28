<?php
session_start();
require_once __DIR__ . "/../../db/db.php";

if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: view_timetable.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM timetables WHERE id = :id");
$stmt->execute([':id' => $id]);
$timetable = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$timetable) {
    header("Location: view_timetable.php");
    exit;
}

if (isset($_POST['update'])) {
    $department = $_POST['department'] ?? '';
    $semester   = $_POST['semester'] ?? '';
    $section    = $_POST['section'] ?? '';
    $file       = $_FILES['timetable_file'] ?? null;

    $filename = $timetable['timetable_file'];

    if ($file && $file['error'] === 0) {
        $filename = "uploads/timetable_" . time() . "_" . basename($file['name']);
        move_uploaded_file($file['tmp_name'], __DIR__ . "/" . $filename);
    }

    $stmt = $pdo->prepare("UPDATE timetables SET department=:department, semester=:semester, section=:section, timetable_file=:file WHERE id=:id");
    $stmt->execute([
        ':department' => $department,
        ':semester'   => $semester,
        ':section'    => $section,
        ':file'       => $filename,
        ':id'         => $id
    ]);

    $_SESSION['msg'] = "✏️ Timetable updated successfully!";
    header("Location: view_timetable.php");
    exit;
}

$pageTitle  = "Edit Timetable";
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
    <h1>✏ Edit Timetable</h1>

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="department" value="<?= htmlspecialchars($timetable['department']) ?>" required><br><br>
      <input type="text" name="semester" value="<?= htmlspecialchars($timetable['semester']) ?>" required><br><br>
      <input type="text" name="section" value="<?= htmlspecialchars($timetable['section']) ?>" required><br><br>
      <input type="file" name="timetable_file"><br><small>Leave empty to keep existing file.</small><br><br>
      <button type="submit" name="update">Update Timetable</button>
    </form>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
