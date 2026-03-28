<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// Ensure faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header("Location: /smartcampus/home.php");
    exit();
}

// Get faculty department
$stmt = $pdo->prepare("SELECT department FROM faculty WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$faculty_department = $stmt->fetchColumn();

if (!$faculty_department) {
    die("Faculty department not found.");
}

// Handle approve/reject action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_id'])) {
    $leave_id = $_POST['leave_id'];
    $action = $_POST['action']; // 'Approved' or 'Rejected'
    $comments = $_POST['comments'];
    $approver_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        UPDATE leave_requests
        SET status = ?, approved_by = ?, approved_on = NOW(), comments = ?
        WHERE id = ?
    ");
    $stmt->execute([$action, $approver_id, $comments, $leave_id]);

    $success_message = "Leave request $action successfully!";
}

/* -------------------- PENDING LEAVES (Only Faculty Dept) -------------------- */
$stmt = $pdo->prepare("
    SELECT lr.*, 
           CONCAT(s.firstname, ' ', s.lastname) AS student_name, 
           s.department, s.semester, s.section, 
           lt.name AS leave_type
    FROM leave_requests lr
    JOIN students s ON lr.student_id = s.id
    JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE lr.status = 'Pending' AND s.department = ?
    ORDER BY lr.applied_on ASC
");
$stmt->execute([$faculty_department]);
$pending_leaves = $stmt->fetchAll();

/* -------------------- LEAVE HISTORY (Only Faculty Dept) -------------------- */
$whereClauses = ["s.department = ?"];
$params = [$faculty_department];

// Filter by status
if (!empty($_GET['status']) && $_GET['status'] !== 'All') {
    $whereClauses[] = "lr.status = ?";
    $params[] = $_GET['status'];
}

// Filter by date range
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $whereClauses[] = "lr.start_date >= ? AND lr.end_date <= ?";
    $params[] = $_GET['from_date'];
    $params[] = $_GET['to_date'];
}

$whereSQL = "WHERE " . implode(" AND ", $whereClauses);

$stmt = $pdo->prepare("
    SELECT lr.*, 
           CONCAT(s.firstname, ' ', s.lastname) AS student_name, 
           s.department, s.semester, s.section, 
           lt.name AS leave_type
    FROM leave_requests lr
    JOIN students s ON lr.student_id = s.id
    JOIN leave_types lt ON lr.leave_type_id = lt.id
    $whereSQL
    ORDER BY lr.applied_on DESC
");
$stmt->execute($params);
$history_leaves = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php'; // Faculty sidebar
?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<main class="container">
    <section class="card">

<h1>Faculty Leave Dashboard (<?= htmlspecialchars($faculty_department) ?> Department)</h1>

<?php if (!empty($success_message)) echo "<p class='success'>$success_message</p>"; ?>

<!-- ================== PENDING REQUESTS ================== -->
<h2>Pending Leave Requests</h2>
<table class="styled-table">
    <tr>
        <th>Student</th>
        <th>Semester</th>
        <th>Section</th>
        <th>Leave Type</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Attachment</th>
        <th>Action</th>
    </tr>
    <?php foreach($pending_leaves as $l): ?>
    <tr>
        <td><?= htmlspecialchars($l['student_name']) ?></td>
        <td><?= htmlspecialchars($l['semester']) ?></td>
        <td><?= htmlspecialchars($l['section']) ?></td>
        <td><?= htmlspecialchars($l['leave_type']) ?></td>
        <td><?= $l['start_date'] ?></td>
        <td><?= $l['end_date'] ?></td>
        <td>
            <button type="button" class="view-reason" data-reason="<?= htmlspecialchars($l['reason']) ?>">View Reason</button>
            <div class="reason-text" style="display:none;"></div>
        </td>
        <td>
            <?php if($l['attachment']): ?>
                <a href="../students/uploads/<?= htmlspecialchars($l['attachment']) ?>" target="_blank">View</a>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="leave_id" value="<?= $l['id'] ?>">
                <select name="action">
                    <option value="Approved">Approve</option>
                    <option value="Rejected">Reject</option>
                </select>
                <br>
                <textarea name="comments" placeholder="Comments (optional)"></textarea>
                <br>
                <button type="submit">Submit</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- ================== LEAVE HISTORY ================== -->
<h2>Leave History</h2>

<!-- Filter Form -->
<form method="get" class="filter-box">
    <label>Status:</label>
    <select name="status">
        <option value="All">All</option>
        <option value="Pending" <?= (($_GET['status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
        <option value="Approved" <?= (($_GET['status'] ?? '') === 'Approved') ? 'selected' : '' ?>>Approved</option>
        <option value="Rejected" <?= (($_GET['status'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
    </select>

    <label>From:</label>
    <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
    <label>To:</label>
    <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">

    <button type="submit">Filter</button>
</form>

<table class="styled-table">
    <tr>
        <th>Student</th>
        <th>Semester</th>
        <th>Section</th>
        <th>Leave Type</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Comments</th>
        <th>Attachment</th>
        <th>Applied On</th>
        <th>Approved On</th>
    </tr>
    <?php foreach($history_leaves as $l): ?>
    <tr>
        <td><?= htmlspecialchars($l['student_name']) ?></td>
        <td><?= htmlspecialchars($l['semester']) ?></td>
        <td><?= htmlspecialchars($l['section']) ?></td>
        <td><?= htmlspecialchars($l['leave_type']) ?></td>
        <td><?= $l['start_date'] ?></td>
        <td><?= $l['end_date'] ?></td>
        <td>
            <button type="button" class="view-reason" data-reason="<?= htmlspecialchars($l['reason']) ?>">View Reason</button>
            <div class="reason-text" style="display:none;"></div>
        </td>
        <td>
            <?php 
            $status = strtolower(trim($l['status'] ?? ''));
            $map = [
                'approved' => ['class' => 'approved', 'label' => 'Approved'],
                'rejected' => ['class' => 'rejected', 'label' => 'Rejected'],
                'pending'  => ['class' => 'pending',  'label' => 'Pending'],
            ];
            $class = $map[$status]['class'] ?? 'pending';
            $label = $map[$status]['label'] ?? ucfirst($status);
            ?>
            <span class="<?= $class ?>"><?= htmlspecialchars($label) ?></span>
        </td>
        <td><?= htmlspecialchars($l['comments']) ?></td>
        <td>
            <?php if($l['attachment']): ?>
                <a href="../students/uploads/<?= htmlspecialchars($l['attachment']) ?>" target="_blank">View</a>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </td>
        <td><?= $l['applied_on'] ?></td>
        <td><?= $l['approved_on'] ?? '—' ?></td>
    </tr>
    <?php endforeach; ?>
</table>
    </section>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

<style>
/* Table Styling */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px;
}
.styled-table th, .styled-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
.styled-table th {
    background: #f4f6f9;
    font-weight: bold;
}

/* Status Badges */
.badge {
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}
.badge.approved { background: #d4edda; color: #155724; }
.badge.rejected { background: #f8d7da; color: #721c24; }
.badge.pending  { background: #fff3cd; color: #856404; }

/* View Reason Button */
.view-reason {
    background: #6c757d;
    color: #fff;
    border: none;
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
}
.view-reason:hover { background: #5a6268; }
.reason-text {
    margin-top: 5px;
    padding: 6px;
    background: #f8f9fa;
    border-left: 3px solid #6c757d;
    font-size: 13px;
    color: #333;
}
</style>

<script>
// Toggle Reason View/Hide
document.querySelectorAll(".view-reason").forEach(btn => {
  btn.addEventListener("click", () => {
    const reasonBox = btn.nextElementSibling;
    if (reasonBox.style.display === "none" || reasonBox.style.display === "") {
      reasonBox.textContent = btn.dataset.reason || "No reason provided";
      reasonBox.style.display = "block";
      btn.textContent = "Hide Reason";
    } else {
      reasonBox.style.display = "none";
      btn.textContent = "View Reason";
    }
  });
});
</script>
