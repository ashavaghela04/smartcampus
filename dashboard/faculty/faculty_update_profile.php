<?php
// faculty_update_profile.php
session_start();

// 🔒 Ensure logged-in faculty
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header("Location: /smartcampus/home.php");
    exit();
}

$faculty_id = $_SESSION['user_id'];

// === DB Config ===
$host = "localhost";
$dbname = "smartcampus_1";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === Collect form data ===
$fname       = trim($_POST['fname']);
$lname       = trim($_POST['lname']);
$email       = trim($_POST['email']);
$phone       = trim($_POST['phone']);
$department  = trim($_POST['department']);
$designation = trim($_POST['designation']);
$photoName   = null;

// === Handle Profile Photo Upload ===
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/faculty/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // create folder if not exists
    }

    $fileTmp  = $_FILES['photo']['tmp_name'];
    $ext      = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $photoName = 'faculty_' . $faculty_id . '_' . time() . '.' . strtolower($ext);
    $target   = $uploadDir . $photoName;

    // validate image type
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (in_array(strtolower($ext), $allowed)) {
        move_uploaded_file($fileTmp, $target);

        // delete old photo if exists
        $oldPhotoQuery = $conn->prepare("SELECT photo FROM faculty WHERE id = ?");
        $oldPhotoQuery->bind_param("i", $faculty_id);
        $oldPhotoQuery->execute();
        $oldResult = $oldPhotoQuery->get_result();
        if ($old = $oldResult->fetch_assoc()) {
            if (!empty($old['photo']) && file_exists($uploadDir . $old['photo'])) {
                unlink($uploadDir . $old['photo']);
            }
        }
        $oldPhotoQuery->close();
    }
}

// === Update Faculty Profile ===
if ($photoName) {
    $sql = "UPDATE faculty 
            SET fname=?, lname=?, email=?, phone=?, department=?, designation=?, photo=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $fname, $lname, $email, $phone, $department, $designation, $photoName, $faculty_id);
} else {
    $sql = "UPDATE faculty 
            SET fname=?, lname=?, email=?, phone=?, department=?, designation=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $fname, $lname, $email, $phone, $department, $designation, $faculty_id);
}

if ($stmt->execute()) {
    $_SESSION['success_msg'] = "Profile updated successfully!";
    header("Location: faculty_profile.php"); // ✅ redirect to profile page
} else {
    $_SESSION['error_msg'] = "Error updating profile: " . $conn->error;
    header("Location: faculty_edit_profile.php"); // if error, stay on edit page
}

$stmt->close();
$conn->close();
exit();
?>
