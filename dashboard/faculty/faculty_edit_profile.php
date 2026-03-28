<?php
// faculty_edit_profile.php
session_start();

$pageTitle  = "Edit Profile";
$activePage = "faculty_edit_profile";

// 🔒 Check if faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header("Location: /smartcampus/home.php");
    exit();
}

$faculty_id = $_SESSION['user_id'];

// === DB Config ===
$host = "localhost";
$dbname = "smartcampus_1";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === Fetch Faculty Info ===
$sql = "SELECT fname, lname, email, phone, department, designation, photo 
        FROM faculty WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Edit Profile</h1>

    <?php if ($faculty): ?>
      <form action="faculty_update_profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
        <div class="profile-card">
          <div class="avatar-wrapper">
            <img src="<?php echo !empty($faculty['photo']) ? 'smartcampus/uploads/faculty/' . htmlspecialchars($faculty['photo']) : '../assets/images/faculty.png'; ?>" 
                 alt="Profile" class="avatar">
            <input type="file" name="photo" accept="image/*">
          </div>

          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="fname" value="<?php echo htmlspecialchars($faculty['fname']); ?>" required>
          </div>

          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lname" value="<?php echo htmlspecialchars($faculty['lname']); ?>" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($faculty['email']); ?>" required>
          </div>

          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($faculty['phone']); ?>" required>
          </div>

          <div class="form-group">
            <label>Department</label>
            <input type="text" name="department" value="<?php echo htmlspecialchars($faculty['department']); ?>" required>
          </div>

          <div class="form-group">
            <label>Designation</label>
            <input type="text" name="designation" value="<?php echo htmlspecialchars($faculty['designation']); ?>" required>
          </div>

          <button type="submit" class="btn">Save Changes</button>
        </div>
      </form>
    <?php else: ?>
      <p class="error">Profile not found.</p>
    <?php endif; ?>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

