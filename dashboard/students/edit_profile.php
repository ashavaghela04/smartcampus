<?php
session_start();
$pageTitle = "My Profile";
$activePage = "student_profile";

require_once __DIR__ . '/../../db/db.php';

// 🔒 Only allow students
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$pdo = Database::getInstance();

// Fetch student info
$stmt = $pdo->prepare("SELECT firstname, lastname, email, phone, gender, dob, program, year, photo 
                       FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Student Profile</h1>

    <?php if ($student): ?>
      <!-- Profile Header -->
      <div class="profile-header">
        <img src="<?= !empty($student['photo']) ? '../../forms/' . htmlspecialchars($student['photo']) : '/smartcampus/assets/images/student.png'; ?>" 
     alt="Profile" class="profile-pic">

        <div class="profile-info">
          <h2><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) ?></h2>
          <p><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($student['email']) ?></p>
          <p><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($student['program']) ?></p>
          <p><i class="fa-solid fa-calendar-check"></i> Year <?= htmlspecialchars($student['year']) ?></p>
        </div>
      </div>

      <!-- Personal Details Card -->
      <div class="details-card">
        <h3>Personal Details</h3>
        <div class="details-grid">
          <div><i class="fa-solid fa-id-card"></i> <strong>Full Name:</strong> <?= htmlspecialchars($student['firstname'] . " " . $student['lastname']) ?></div>
          <div><i class="fa-solid fa-venus-mars"></i> <strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></div>
          <div><i class="fa-solid fa-cake-candles"></i> <strong>Date of Birth:</strong> <?= htmlspecialchars($student['dob']) ?></div>
          <div><i class="fa-solid fa-phone"></i> <strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></div>
          <div><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></div>
          <div><i class="fa-solid fa-graduation-cap"></i> <strong>Program:</strong> <?= htmlspecialchars($student['program']) ?></div>
          <div><i class="fa-solid fa-calendar-check"></i> <strong>Year:</strong> <?= htmlspecialchars($student['year']) ?></div>
        </div>
        <a href="student_edit_profile.php" class="btn">Edit Profile</a>
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
  border: 2px solid #0A4FA3;
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
