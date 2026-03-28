<?php
session_start();
$pageTitle  = "Admin Profile";
$activePage = "profile";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../../db/db.php'; // adjust db connection file path

// ✅ Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getInstance();
$userId = $_SESSION['user_id'];

// ✅ Fetch admin details
$stmt = $pdo->prepare("SELECT username, email FROM admin WHERE id = ?");
$stmt->execute([$userId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Handle form submission
$successMsg = $errorMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name === "" || $email === "") {
        $errorMsg = "All fields are required!";
    } else {
        $update = $pdo->prepare("UPDATE admin SET username = ?, email = ? WHERE id = ?");
        if ($update->execute([$name, $email, $userId])) {
            $successMsg = "Profile updated successfully!";
            // Refresh data
            $stmt->execute([$userId]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errorMsg = "Error updating profile.";
        }
    }
}
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Admin Profile</h1>
    <p>Update your personal details.</p>
    
    <?php if ($successMsg): ?>
      <div class="alert success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
      <div class="alert error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($admin['username']) ?>" required />

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required />

        <button type="submit">Update</button>
      </form>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
