<?php
require 'common_db.php';

// Fetch pending students
$students = $mysqli->query("SELECT id, firstname, email FROM students WHERE approved=0");

// Fetch pending faculty
$faculty = $mysqli->query("SELECT id, firstname, email FROM faculty WHERE approved=0");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Pending Approvals</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h2>Pending Students</h2>
  <table border="1" cellpadding="5">
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php while($s = $students->fetch_assoc()): ?>
      <tr>
        <td><?= $s['id'] ?></td>
        <td><?= $s['firstname'] ?></td>
        <td><?= $s['email'] ?></td>
        <td><button onclick="approveUser(<?= $s['id'] ?>, 'student')">Approve</button></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h2>Pending Faculty</h2>
  <table border="1" cellpadding="5">
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php while($f = $faculty->fetch_assoc()): ?>
      <tr>
        <td><?= $f['id'] ?></td>
        <td><?= $f['firstname'] ?></td>
        <td><?= $f['email'] ?></td>
        <td><button onclick="approveUser(<?= $f['id'] ?>, 'faculty')">Approve</button></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <script>
    function approveUser(id, type) {
      $.ajax({
        url: "approve_user.php",
        type: "POST",
        data: JSON.stringify({ id: id, type: type }),
        contentType: "application/json",
        success: function(res) {
          alert(res.message);
          location.reload(); // reload list after approval
        }
      });
    }
  </script>
</body>
</html>
