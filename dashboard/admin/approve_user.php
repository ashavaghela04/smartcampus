<?php
// approve_user.php

$pageTitle  = "Approve Users";
$activePage = "approve_user";

require_once __DIR__ . "/../../db/db.php";                 // DB connection
require_once __DIR__ . "/../../assets/config/mail-config.php"; // Mail function

$pdo = Database::getInstance();

// ==================== Approve user action ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $id    = intval($_POST['approve_id']);
    $userType = $_POST['user_type'] === 'faculty' ? 'faculty' : 'students';

    // Fetch user details
    if ($userType === 'students') {
        $stmt = $pdo->prepare("SELECT id, firstname, lastname, email FROM students WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT id, fname as firstname, lname as lastname, email FROM faculty WHERE id = ?");
    }
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate enrollment/username
        $enrollment_number = strtoupper(substr($userType,0,3)) . date("Y") . $id;

        // Generate password
        $password_plain = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $hashed   = password_hash($password_plain, PASSWORD_DEFAULT);

        // Update DB with enrollment number + hashed password
        $update = $pdo->prepare("UPDATE $userType SET username = ?, password = ?, approved = 1 WHERE id = ?");
        $update->execute([$enrollment_number, $hashed, $id]);

        // ==================== Email Content ====================
        $email_subject = "Your Smart Campus Credentials";
        $htmlBody = "
          <p>Dear {$user['firstname']} {$user['lastname']},</p>
          <p>Your registration has been approved.</p>
          <p><strong>Enrollment Number:</strong> {$enrollment_number}<br>
             <strong>Password:</strong> {$password_plain}</p>
          <p>Please login here: <a href='https://yourdomain.com/login-form.php'>Student Portal</a></p>
          <p>Regards,<br>Smart Campus Team</p>
        ";

        if (sendEmail($user['email'], $email_subject, $htmlBody)) {
            $successMessage = "✅ Approved! Credentials sent to {$user['email']}.";
        } else {
            $successMessage = "⚠️ Approved, but email could not be sent.";
        }
    }
}

// ==================== Fetch pending users ====================
$students = $pdo->query("SELECT id, firstname, lastname, email FROM students WHERE approved = 0")->fetchAll(PDO::FETCH_ASSOC);
$faculty  = $pdo->query("SELECT id, fname, lname, email FROM faculty WHERE approved = 0")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1 class="page-title">Approve Users</h1>

    <?php if (!empty($successMessage)): ?>
      <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>

    <h2>Pending Students</h2>
    <table border="1" cellpadding="8" cellspacing="0">
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Action</th>
      </tr>
      <?php foreach ($students as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><?= htmlspecialchars($s['firstname'] . " " . $s['lastname']) ?></td>
          <td><?= htmlspecialchars($s['email']) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="approve_id" value="<?= $s['id'] ?>">
              <input type="hidden" name="user_type" value="students">
              <button type="submit">Approve</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h2>Pending Faculty</h2>
    <table border="1" cellpadding="8" cellspacing="0">
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Action</th>
      </tr>
      <?php foreach ($faculty as $f): ?>
        <tr>
          <td><?= $f['id'] ?></td>
          <td><?= htmlspecialchars($f['fname'] . " " . $f['lname']) ?></td>
          <td><?= htmlspecialchars($f['email']) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="approve_id" value="<?= $f['id'] ?>">
              <input type="hidden" name="user_type" value="faculty">
              <button type="submit">Approve</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>

  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
