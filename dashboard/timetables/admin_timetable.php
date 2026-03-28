<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// ✅ Get PDO instance
$pdo = Database::getInstance();

// ✅ Allow only admin/faculty
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ["admin", "faculty"])) {
    header("Location: ../login.php");
    exit();
}

// --- ADD timetable ---
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO timetables (class, subject, faculty, day, time_slot, room) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['class'], $_POST['subject'], $_POST['faculty'], $_POST['day'], $_POST['time_slot'], $_POST['room']]);
    $_SESSION['msg'] = "✅ Timetable added successfully!";
    header("Location: admin_timetable.php");
    exit();
}

// --- DELETE timetable ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM timetables WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['msg'] = "🗑️ Timetable deleted!";
    header("Location: admin_timetable.php");
    exit();
}

$pageTitle  = "Manage Timetable";
$activePage = "timetable";

include __DIR__ . '/../includes/header.php';

// ✅ Load correct sidebar
if ($_SESSION['user_type'] === "admin") {
    include __DIR__ . '/../includes/sidebar_admin.php';
} else {
    include __DIR__ . '/../includes/sidebar_faculty.php';
}
?>

<div class="shell">
    <?php include __DIR__ . '/../includes/topbar.php'; ?>

    <main class="container py-4">
        <h1 class="mb-4">📅 Timetable Management</h1>

        <!-- ✅ Messages -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>

        <!-- Add Timetable Form -->
        <div class="card shadow p-3 mb-4">
            <h5>Add New Timetable</h5>
            <form method="POST" class="row g-2">
                <div class="col-md-2"><input class="form-control" type="text" name="class" placeholder="Class" required></div>
                <div class="col-md-2"><input class="form-control" type="text" name="subject" placeholder="Subject" required></div>
                <div class="col-md-2"><input class="form-control" type="text" name="faculty" placeholder="Faculty" required></div>
                <div class="col-md-2">
                    <select class="form-select" name="day" required>
                        <option value="">Day</option>
                        <option>Monday</option><option>Tuesday</option><option>Wednesday</option>
                        <option>Thursday</option><option>Friday</option><option>Saturday</option>
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" type="text" name="time_slot" placeholder="Time Slot" required></div>
                <div class="col-md-2"><input class="form-control" type="text" name="room" placeholder="Room" required></div>
                <div class="col-12"><button class="btn btn-primary mt-2" type="submit" name="add">➕ Add</button></div>
            </form>
        </div>

        <!-- Timetable Table -->
        <div class="card shadow">
            <div class="card-body">
                <h5>All Timetables</h5>
                <table class="table table-bordered table-hover mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Class</th><th>Subject</th><th>Faculty</th>
                            <th>Day</th><th>Time</th><th>Room</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM timetables 
                                             ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), time_slot");
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($rows) {
                            foreach ($rows as $row) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['class']}</td>
                                    <td>{$row['subject']}</td>
                                    <td>{$row['faculty']}</td>
                                    <td>{$row['day']}</td>
                                    <td>{$row['time_slot']}</td>
                                    <td>{$row['room']}</td>
                                    <td>
                                        <a class='btn btn-sm btn-warning' href='edit_timetable.php?id={$row['id']}'>✏️ Edit</a>
                                        <a class='btn btn-sm btn-danger' href='admin_timetable.php?delete={$row['id']}' onclick='return confirm(\"Delete this entry?\")'>🗑️ Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No timetable found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
