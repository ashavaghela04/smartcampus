<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// 🔹 Initialize PDO from Singleton     
$pdo = Database::getInstance();

// 🔹 Make sure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: /smartcampus/home.php");
    exit();
}

// 🔹 Use the session user_id
$student_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT lr.*, lt.name AS leave_type 
    FROM leave_requests lr 
    JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE student_id = ?
");
$stmt->execute([$student_id]);
$leaves = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/sidebar.php'; ?>
<main class="container">
<h2>My Leave Requests</h2>
<div class="card">  
<table border="1" cellpadding="5" style="width:100%; border-collapse:collapse; margin-top:20px;">
    <tr style="background:#f4f4f4;">
        <th>Leave Type</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Attachment</th>
    </tr>
    <?php if (count($leaves) === 0): ?>
        <tr>
            <td colspan="6" style="text-align:center;">No leave requests found.</td>
        </tr>
    <?php else: ?>
        <?php foreach($leaves as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['leave_type']) ?></td>
            <td><?= htmlspecialchars($l['start_date']) ?></td>
            <td><?= htmlspecialchars($l['end_date']) ?></td>
            <td><?= htmlspecialchars($l['reason']) ?></td>
            <td><?= htmlspecialchars($l['status']) ?></td>
            <td>
                <?php if($l['attachment']): ?>
                    <a href="uploads/<?= htmlspecialchars($l['attachment']) ?>" target="_blank">View</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</div>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
  </div>

