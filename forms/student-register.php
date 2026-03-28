<?php
// student-register.php

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// DB config
$host = "localhost";
$dbname = "smartcampus_1";
$username = "root";
$password = "";

// Connect
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status"=>"error","message"=>"DB connection failed"]);
    exit;
}
$conn->set_charset('utf8mb4');

// Helper to sanitize
$sanitize = fn($v) => trim($v);

// Collect inputs
$firstname   = $sanitize($_POST['firstname'] ?? '');
$lastname    = $sanitize($_POST['lastname'] ?? '');
$fathername  = $sanitize($_POST['fathername'] ?? '');
$mothername  = $sanitize($_POST['mothername'] ?? '');
$dob         = $sanitize($_POST['dob'] ?? '');
$gender      = $sanitize($_POST['gender'] ?? '');
$email       = $sanitize($_POST['email'] ?? '');
$phone       = $sanitize($_POST['phone'] ?? '');
$address     = $sanitize($_POST['address'] ?? '');
$city        = $sanitize($_POST['city'] ?? '');
$district    = $sanitize($_POST['district'] ?? '');
$state       = $sanitize($_POST['state'] ?? '');
$nation      = $sanitize($_POST['nation'] ?? '');
$aadhaar     = $sanitize($_POST['aadhaar'] ?? '');
$program     = $sanitize($_POST['program'] ?? '');
$department  = $sanitize($_POST['department'] ?? '');
$year        = $sanitize($_POST['year'] ?? '');
$semester    = $sanitize($_POST['semester'] ?? '');

// Required fields check
$required = compact(
    "firstname","lastname","fathername","mothername","dob","gender",
    "email","phone","address","city","district","state","nation",
    "aadhaar","program","department","year","semester"
);

foreach ($required as $field=>$val) {
    if ($val === '') {
        echo json_encode(["status"=>"error","message"=>"Missing field: $field"]);
        exit;
    }
}

// Format validations
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status"=>"error","message"=>"Invalid email"]);
    exit;
}
if (!preg_match('/^\d{12}$/', $aadhaar)) {
    echo json_encode(["status"=>"error","message"=>"Aadhaar must be 12 digits"]);
    exit;
}
if (!preg_match('/^\d{10}$/', $phone)) {
    echo json_encode(["status"=>"error","message"=>"Phone must be 10 digits"]);
    exit;
}
$allowed_genders = ['Male','Female','Other'];
if (!in_array($gender, $allowed_genders, true)) {
    echo json_encode(["status"=>"error","message"=>"Invalid gender"]);
    exit;
}

// Duplicate guard
$dupStmt = $conn->prepare("SELECT email, aadhaar FROM students WHERE email = ? OR aadhaar = ?");
$dupStmt->bind_param("ss", $email, $aadhaar);
$dupStmt->execute();
$res = $dupStmt->get_result();
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['email'] === $email) {
        echo json_encode(["status"=>"error","field"=>"email","message"=>"Email already registered"]);
        exit;
    }
    if ($row['aadhaar'] === $aadhaar) {
        echo json_encode(["status"=>"error","field"=>"aadhaar","message"=>"Aadhaar already registered"]);
        exit;
    }
}
$dupStmt->close();

// Handle photo upload
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status"=>"error","message"=>"Photo is required"]);
    exit;
}

$photo = $_FILES['photo'];
$maxSize = 2 * 1024 * 1024;
if ($photo['size'] > $maxSize) {
    echo json_encode(["status"=>"error","message"=>"Photo exceeds 2MB limit"]);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $photo['tmp_name']);
finfo_close($finfo);
$allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/gif'=>'.gif'];
if (!array_key_exists($mime, $allowed)) {
    echo json_encode(["status"=>"error","message"=>"Photo must be JPG, PNG, or GIF"]);
    exit;
}

$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}
$ext = $allowed[$mime];
$basename = bin2hex(random_bytes(8)) . $ext;
$target_path = $upload_dir . $basename;
if (!move_uploaded_file($photo['tmp_name'], $target_path)) {
    echo json_encode(["status"=>"error","message"=>"Failed to save uploaded photo"]);
    exit;
}
chmod($target_path, 0644);
$photoPath = 'uploads/' . $basename;

// Insert
$sql = "INSERT INTO students (
    firstname, lastname, fathername, mothername, dob, gender, email, phone,
    address, city, district, state, nation, photo, aadhaar,
    program, department, year, semester
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insStmt = $conn->prepare($sql);
if (!$insStmt) {
    @unlink($target_path);
    echo json_encode(["status"=>"error","message"=>"Server error (insert prepare failed)"]);
    exit;
}

$insStmt->bind_param("sssssssssssssssssss",
    $firstname, $lastname, $fathername, $mothername, $dob, $gender, $email, $phone,
    $address, $city, $district, $state, $nation, $photoPath, $aadhaar,
    $program, $department, $year, $semester
);

if ($insStmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Registration successful"]);
} else {
    @unlink($target_path);
    echo json_encode(["status"=>"error","message"=>"Failed to register"]);
}

$insStmt->close();
$conn->close();
