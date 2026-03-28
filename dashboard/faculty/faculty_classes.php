<?php

$pageTitle  = "My Classes";
$activePage = "faculty_classes";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  <main class="container">
    <h1>Assigned Classes</h1>
    <p>Here you can view and manage the classes assigned to you.</p>

    <!-- Example Table -->
    <table class="table">
      <thead>
        <tr>
          <th>Class</th>
          <th>Subject</th>
          <th>Strength</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>BCA 1st Year</td><td>Computer Networks</td><td>56</td></tr>
        <tr><td>BCA 2nd Year</td><td>Data Structures</td><td>49</td></tr>
      </tbody>
    </table>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
