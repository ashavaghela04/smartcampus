<?php
session_start();
$pageTitle  = "Manage Materials";
$activePage = "materials";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_admin.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>

  <main class="container">
    <h1>Materials Management</h1>
    <p>Manage all study materials uploaded by faculty.</p>
    <div class="card">
      <p>[Materials table here]</p>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
