<?php
// faculty_profile.php
session_start();

$pageTitle  = "My Profile";
$activePage = "faculty_profile";

// 🔒 Only allow faculty
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
$sql = "SELECT fname, lname, email, phone, department, designation, dob, gender, photo 
        FROM faculty WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';
?>

<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Profile</h1>

    <?php if ($faculty): ?>
      <!-- Profile Header Card -->
      <div class="profile-header">
       <img src="<?php echo !empty($faculty['photo']) ? '../../forms/' . htmlspecialchars($faculty['photo']) : '../../assets/images/faculty.png'; ?>" 
     alt="Profile" class="profile-pic">

        <div class="profile-info">
          <h2><?php echo htmlspecialchars($faculty['fname'] . " " . $faculty['lname']); ?></h2>
          <p><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($faculty['email']); ?></p>
          <p><i class="fa-solid fa-building-columns"></i> <?php echo htmlspecialchars($faculty['department']); ?></p>
          <p><i class="fa-solid fa-user-tie"></i> <?php echo htmlspecialchars($faculty['designation']); ?></p>
        </div>
      </div>

      <!-- Personal Details Card -->
      <div class="details-card">
        <h3>Personal Details</h3>
        <div class="details-grid">
          <div><i class="fa-solid fa-id-card"></i> <strong>Full Name:</strong> <?php echo htmlspecialchars($faculty['fname'] . " " . $faculty['lname']); ?></div>
          <div><i class="fa-solid fa-venus-mars"></i> <strong>Gender:</strong> <?php echo htmlspecialchars($faculty['gender']); ?></div>
          <div><i class="fa-solid fa-cake-candles"></i> <strong>Date of Birth:</strong> <?php echo htmlspecialchars($faculty['dob']); ?></div>
          <div><i class="fa-solid fa-phone"></i> <strong>Phone:</strong> <?php echo htmlspecialchars($faculty['phone']); ?></div>
          <div><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($faculty['email']); ?></div>
          <div><i class="fa-solid fa-building-columns"></i> <strong>Department:</strong> <?php echo htmlspecialchars($faculty['department']); ?></div>
          <div><i class="fa-solid fa-user-tie"></i> <strong>Designation:</strong> <?php echo htmlspecialchars($faculty['designation']); ?></div>
        </div>
        <a href="faculty_edit_profile.php" class="btn">Edit Profile</a>
      </div>

    <?php else: ?>
      <p class="error">Profile not found.</p>
    <?php endif; ?>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<style>
/* Profile Header */
.profile-header {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}
.profile-pic {
  width: 120px;
  height: 120px;
  border-radius: 8px;
  object-fit: cover;
  margin-right: 20px;
}
.profile-info h2 {
  margin: 0;
  color: #0A4FA3;
}
.profile-info p {
  margin: 6px 0;
  color: #555;
  font-size: 14px;
}
.profile-info i {
  margin-right: 6px;
  color: #0A4FA3;
}

/* Personal Details */
.details-card {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.details-card h3 {
  margin-bottom: 15px;
  color: #0A4FA3;
}
.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 12px;
}
.details-grid div {
  background: #f9f9f9;
  padding: 10px;
  border-radius: 6px;
  font-size: 14px;
  color: #333;
  display: flex;
  align-items: center;
}
.details-grid i {
  margin-right: 8px;
  color: #0A4FA3;
}
.btn {
  display: inline-block;
  margin-top: 15px;
  padding: 10px 18px;
  border-radius: 6px;
  text-decoration: none;
  background: #0A4FA3;
  color: #fff;
  font-weight: bold;
}
.btn:hover {
  background: #083a7d;
}
.error {
  text-align: center;
  color: red;
}
</style>
