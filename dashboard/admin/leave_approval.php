<?php
session_start();
require_once __DIR__ . '/../../db/db.php';
$pdo = Database::getInstance();

// Ensure admin/faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /smartcampus/home.php");
    exit();
}

/* -------------------- HANDLE UPDATE ACTION -------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_id'], $_POST['action'])) {
    $leave_id   = (int) $_POST['leave_id'];
    $action     = $_POST['action']; // Approved, Rejected, Pending
    $comments   = trim($_POST['comments'] ?? '');
    $approver_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        UPDATE leave_requests
        SET status = ?, 
            approved_by = ?, 
            approved_on = CASE WHEN ? IN ('Approved','Rejected') THEN NOW() ELSE NULL END,
            comments = ?
        WHERE id = ?
    ");
    $stmt->execute([$action, $approver_id, $action, $comments, $leave_id]);

    $success_message = "Leave request updated to <strong>$action</strong> successfully!";
}

/* -------------------- PENDING LEAVES -------------------- */
$stmt = $pdo->query("
    SELECT lr.*, 
           CONCAT(s.firstname, ' ', s.lastname) AS student_name, 
           s.department, s.semester, s.section, 
           lt.name AS leave_type
    FROM leave_requests lr
    JOIN students s ON lr.student_id = s.id
    JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE lr.status = 'Pending'
    ORDER BY lr.applied_on ASC
");
$pending_leaves = $stmt->fetchAll();

/* -------------------- LEAVE HISTORY WITH FILTERS -------------------- */
$whereClauses = [];
$params = [];

// Filter by status
if (!empty($_GET['status']) && $_GET['status'] !== 'All') {
    $whereClauses[] = "lr.status = ?";
    $params[] = $_GET['status'];
}

// Filter by department
if (!empty($_GET['department'])) {
    $whereClauses[] = "s.department = ?";
    $params[] = $_GET['department'];
}

// Filter by date range
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $whereClauses[] = "lr.start_date >= ? AND lr.end_date <= ?";
    $params[] = $_GET['from_date'];
    $params[] = $_GET['to_date'];
}

$whereSQL = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

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

// Fetch distinct departments for filter dropdown
$deptStmt = $pdo->query("SELECT DISTINCT department FROM students ORDER BY department ASC");
$departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="shell">
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<main class="container">
    <div class="grid">
    <section class="card">

<h1>Leave Management Dashboard</h1>

<?php if (!empty($success_message)): ?>
    <p class="success"><?= $success_message ?></p>
<?php endif; ?>

<!-- ================== PENDING REQUESTS ================== -->
<h2>Pending Leave Requests</h2>
<table class="styled-table">
    <tr>
        <th>Student</th>
        <th>Department</th>
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
        <td><?= htmlspecialchars($l['department']) ?></td>
        <td><?= htmlspecialchars($l['semester']) ?></td>
        <td><?= htmlspecialchars($l['section']) ?></td>
        <td><?= htmlspecialchars($l['leave_type']) ?></td>
        <td><?= htmlspecialchars($l['start_date']) ?></td>
        <td><?= htmlspecialchars($l['end_date']) ?></td>
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

    <label>Department:</label>
    <select name="department">
        <option value="">All</option>
        <?php foreach($departments as $dept): ?>
            <option value="<?= htmlspecialchars($dept) ?>" 
                <?= (($_GET['department'] ?? '') === $dept) ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept) ?>
            </option>
        <?php endforeach; ?>
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
        <th>Department</th>
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
        <th>Update</th>
    </tr>
    <?php foreach($history_leaves as $l): ?>
    <tr>
        <td><?= htmlspecialchars($l['student_name']) ?></td>
        <td><?= htmlspecialchars($l['department']) ?></td>
        <td><?= htmlspecialchars($l['semester']) ?></td>
        <td><?= htmlspecialchars($l['section']) ?></td>
        <td><?= htmlspecialchars($l['leave_type']) ?></td>
        <td><?= htmlspecialchars($l['start_date']) ?></td>
        <td><?= htmlspecialchars($l['end_date']) ?></td>
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
        <td><?= htmlspecialchars($l['applied_on']) ?></td>
        <td><?= $l['approved_on'] ?? '—' ?></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="leave_id" value="<?= $l['id'] ?>">
                <select name="action">
                    <option value="Approved" <?= ($l['status'] === 'Approved') ? 'selected' : '' ?>>Approve</option>
                    <option value="Rejected" <?= ($l['status'] === 'Rejected') ? 'selected' : '' ?>>Reject</option>
                    <option value="Pending"  <?= ($l['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                </select>
                <br>
                <textarea name="comments" placeholder="Comments (optional)"><?= htmlspecialchars($l['comments']) ?></textarea>
                <br>
                <button type="submit">Update</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
    </section>
    </div>
</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
