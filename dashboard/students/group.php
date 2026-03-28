<?php
session_start();
$pageTitle = "Group";
$activePage = "group";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>

<main class="container">
  <section class="card">
    <h3>Group</h3>
    <p>Join or manage your student groups here.</p>
    <!-- Group chat or members list -->
  </section>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
