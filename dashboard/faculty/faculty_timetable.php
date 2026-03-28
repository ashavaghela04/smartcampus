<?php

$pageTitle  = "Weekly Timetable";
$activePage = "faculty_timetable";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>Weekly Timetable</h1>
    <table class="table">
      <thead>
        <tr><th>Day</th><th>9-11</th><th>11-1</th><th>2-4</th></tr>
      </thead>
      <tbody>
        <tr><td>Monday</td><td>DS</td><td>CN</td><td>Lab</td></tr>
        <tr><td>Tuesday</td><td>Math</td><td>DS</td><td>Free</td></tr>
      </tbody>
    </table>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
