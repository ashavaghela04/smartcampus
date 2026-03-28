<?php
session_start();
$pageTitle = "Time Table";
$activePage = "timetable";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>

<main class="container">
  <section class="card">
    <h3>Time Table</h3>
    <p>Your academic timetable will be shown here.</p>
    <!-- Timetable table -->
  </section>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
