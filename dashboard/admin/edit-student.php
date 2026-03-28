<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// ✅ Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// ✅ Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname   = $_POST['firstname'];
    $lastname    = $_POST['lastname'];
    $fathername  = $_POST['fathername'];
    $mothername  = $_POST['mothername'];
    $dob         = $_POST['dob'];
    $gender      = $_POST['gender'];
    $email       = $_POST['email'];
    $username    = $_POST['username'];
    $department  = $_POST['department'];
    $semester    = $_POST['semester'];
    $phone       = $_POST['phone'];
    $address     = $_POST['address'];
    $city        = $_POST['city'];
    $district    = $_POST['district'];
    $state       = $_POST['state'];
    $aadhaar     = $_POST['aadhaar'];

    // ✅ Prepare update query
    $stmt = $pdo->prepare("UPDATE students SET 
        firstname=?, lastname=?, fathername=?, mothername=?, dob=?, gender=?, 
        email=?, username=?, department=?, semester=?, 
        phone=?, address=?, city=?, district=?, state=?, 
        aadhaar=?
        WHERE id=?");

    $stmt->execute([
        $firstname, $lastname, $fathername, $mothername, $dob, $gender,
        $email, $username, $department, $semester,
        $phone, $address, $city, $district, $state,
        $aadhaar, $id
    ]);

    header("Location: admin_students_manage.php?msg=Student updated successfully");
    exit;
}

// ✅ Fetch student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id=?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}
?>

<?php
$pageTitle = "Edit Student";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <div class="card">
      <div class="card-body">
        <h2>Edit Student</h2>
        <form method="post">

          <!-- Name Fields -->
          <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="firstname" value="<?= htmlspecialchars($student['firstname']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="lastname" value="<?= htmlspecialchars($student['lastname']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Father Name</label>
            <input type="text" class="form-control" name="fathername" value="<?= htmlspecialchars($student['fathername']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Mother Name</label>
            <input type="text" class="form-control" name="mothername" value="<?= htmlspecialchars($student['mothername']) ?>">
          </div>

          <!-- Personal Info -->
          <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($student['dob']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Gender</label>
            <select class="form-control" name="gender">
              <option value="Male"   <?= $student['gender']=='Male'?'selected':'' ?>>Male</option>
              <option value="Female" <?= $student['gender']=='Female'?'selected':'' ?>>Female</option>
              <option value="Other"  <?= $student['gender']=='Other'?'selected':'' ?>>Other</option>
            </select>
          </div>

          <!-- Contact & Account -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student['email']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($student['username']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($student['phone']) ?>">
          </div>

          <!-- Academic Info -->
          <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="text" class="form-control" name="department" value="<?= htmlspecialchars($student['department']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Semester</label>
            <input type="text" class="form-control" name="semester" value="<?= htmlspecialchars($student['semester']) ?>">
          </div>

          <!-- Address Info -->
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="address"><?= htmlspecialchars($student['address']) ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($student['city']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">District</label>
            <input type="text" class="form-control" name="district" value="<?= htmlspecialchars($student['district']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">State</label>
            <input type="text" class="form-control" name="state" value="<?= htmlspecialchars($student['state']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Aadhaar</label>
            <input type="text" class="form-control" name="aadhaar" value="<?= htmlspecialchars($student['aadhaar']) ?>">
          </div>

          <button type="submit" class="btn btn-primary">Save Changes</button>
          <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
