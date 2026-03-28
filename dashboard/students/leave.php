
<?php
session_start();
require_once __DIR__ . '/../../db/db.php';  
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: /smartcampus/home.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['student_id'];
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    $reason     = $_POST['reason'];

    // File upload
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $filename = time() . '_' . $_FILES['attachment']['name'];
        move_uploaded_file($_FILES['attachment']['tmp_name'], "uploads/$filename");
        $attachment = $filename;
    }

    $stmt = $pdo->prepare("INSERT INTO leave_requests 
        (student_id, leave_type_id, start_date, end_date, reason, attachment)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$student_id, $leave_type, $start_date, $end_date, $reason, $attachment]);

    echo "Leave request submitted!";
}   
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/sidebar.php'; ?>
<main class="container">
<h2>Apply for Leave</h2>
<div class="card">
<form action="apply_leave.php" method="post" enctype="multipart/form-data">
    <label>Leave Type:</label>
    <select name="leave_type">
        <option value="1">Casual Leave</option>
        <option value="2">Sick Leave</option>
        <option value="3">Emergency Leave</option>
    </select><br><br>

    <label>Start Date:</label>
    <input type="date" name="start_date" required><br><br>

    <label>End Date:</label>
    <input type="date" name="end_date" required><br><br>

    <label>Reason:</label><br>
    <textarea name="reason" rows="4" cols="50" required></textarea><br><br>

    <label>Attachment (optional):</label>
    <input type="file" name="attachment"><br><br>

    <button type="submit">Apply Leave</button>
</form>
<a href="view_leave.php">View My Leave Requests</a>
</div>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
