<?php
// Fail fast with errors (in production, log instead of displaying)
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/faculty_errors.log');

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

header("Content-Type: application/json");

// Sanitize helper
$sanitize = fn($v) => trim($v);

// Collect inputs
$fname  = $sanitize($_POST['fname'] ?? '');
$lname  = $sanitize($_POST['lname'] ?? '');
$email  = $sanitize($_POST['email'] ?? '');
$phone  = $sanitize($_POST['phone'] ?? '');
$dob    = $sanitize($_POST['dob'] ?? '');
$gender = $sanitize($_POST['gender'] ?? '');
$address= $sanitize($_POST['address'] ?? '');
$department = $sanitize($_POST['department'] ?? '');
$designation= $sanitize($_POST['designation'] ?? '');
$qualification= $sanitize($_POST['qualification'] ?? '');
$experience = $sanitize($_POST['experience'] ?? '');
$aadhaar = $sanitize($_POST['aadhaar'] ?? '');
$pan     = strtoupper($sanitize($_POST['pan'] ?? ''));

// Validate required
$required = compact(
  "fname","lname","email","phone","dob","gender","address",
  "department","designation","qualification","experience","aadhaar","pan"
);
foreach ($required as $field=>$val) {
    if ($val==='') {
        echo json_encode(["status"=>"error","field"=>$field,"message"=>"Missing field: $field"]);
        exit;
    }
}

// Format checks
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status"=>"error","field"=>"email","message"=>"Invalid email"]);
    exit;
}
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode(["status"=>"error","field"=>"phone","message"=>"Phone must be 10 digits"]);
    exit;
}
if (!preg_match('/^\d{12}$/', $aadhaar)) {
    echo json_encode(["status"=>"error","field"=>"aadhaar","message"=>"Aadhaar must be 12 digits"]);
    exit;
}
if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan)) {
    echo json_encode(["status"=>"error","field"=>"pan","message"=>"Invalid PAN format"]);
    exit;
}
if (!in_array($gender, ["Male","Female","Other"])) {
    echo json_encode(["status"=>"error","field"=>"gender","message"=>"Invalid gender"]);
    exit;
}
if (!is_numeric($experience) || (int)$experience < 0) {
    echo json_encode(["status"=>"error","field"=>"experience","message"=>"Invalid experience"]);
    exit;
}
$experience = (int)$experience;

// Check duplicates
$dup = $conn->prepare("SELECT email, phone, aadhaar, pan FROM faculty WHERE email=? OR phone=? OR aadhaar=? OR pan=?");
$dup->bind_param("ssss",$email,$phone,$aadhaar,$pan);
$dup->execute();
$res = $dup->get_result();
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['email']===$email) {
        echo json_encode(["status"=>"error","field"=>"email","message"=>"Email already exists"]); exit;
    }
    if ($row['phone']===$phone) {
        echo json_encode(["status"=>"error","field"=>"phone","message"=>"Phone already exists"]); exit;
    }
    if ($row['aadhaar']===$aadhaar) {
        echo json_encode(["status"=>"error","field"=>"aadhaar","message"=>"Aadhaar already exists"]); exit;
    }
    if ($row['pan']===$pan) {
        echo json_encode(["status"=>"error","field"=>"pan","message"=>"PAN already exists"]); exit;
    }
}
$dup->close();

// Upload dir
$upload_dir = __DIR__ . "/uploads/faculty/";
if (!is_dir($upload_dir)) mkdir($upload_dir,0755,true);

// === PHOTO upload ===
if (!isset($_FILES['photo']) || $_FILES['photo']['error']!==UPLOAD_ERR_OK) {
    echo json_encode(["status"=>"error","field"=>"photo","message"=>"Faculty photo required"]); exit;
}
$photo=$_FILES['photo'];
if ($photo['size']>2*1024*1024) { echo json_encode(["status"=>"error","field"=>"photo","message"=>"Photo too big"]); exit; }
$finfo=finfo_open(FILEINFO_MIME_TYPE);
$mime=finfo_file($finfo,$photo['tmp_name']);
finfo_close($finfo);
$allowed_img=["image/jpeg"=>".jpg","image/png"=>".png","image/gif"=>".gif"];
if (!isset($allowed_img[$mime])) {
    echo json_encode(["status"=>"error","field"=>"photo","message"=>"Invalid photo type"]); exit;
}
$photoName=bin2hex(random_bytes(8)).$allowed_img[$mime];
$photoPathAbs=$upload_dir.$photoName;
move_uploaded_file($photo['tmp_name'],$photoPathAbs);
chmod($photoPathAbs,0644);
$photoPath="uploads/faculty/".$photoName;

// === RESUME upload ===
if (!isset($_FILES['resume']) || $_FILES['resume']['error']!==UPLOAD_ERR_OK) {
    @unlink($photoPathAbs);
    echo json_encode(["status"=>"error","field"=>"resume","message"=>"Resume required"]); exit;
}
$resume=$_FILES['resume'];
if ($resume['size']>5*1024*1024) {
    @unlink($photoPathAbs);
    echo json_encode(["status"=>"error","field"=>"resume","message"=>"Resume too big"]); exit;
}
$finfo=finfo_open(FILEINFO_MIME_TYPE);
$mime=finfo_file($finfo,$resume['tmp_name']);
finfo_close($finfo);
if ($mime!=="application/pdf") {
    @unlink($photoPathAbs);
    echo json_encode(["status"=>"error","field"=>"resume","message"=>"Resume must be PDF"]); exit;
}
$resumeName=bin2hex(random_bytes(8)).".pdf";
$resumePathAbs=$upload_dir.$resumeName;
move_uploaded_file($resume['tmp_name'],$resumePathAbs);
chmod($resumePathAbs,0644);
$resumePath="uploads/faculty/".$resumeName;

// Insert
$sql="INSERT INTO faculty (fname,lname,email,phone,dob,gender,address,department,designation,qualification,experience,aadhaar,pan,photo,resume) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt=$conn->prepare($sql);
$stmt->bind_param("ssssssssssissss",$fname,$lname,$email,$phone,$dob,$gender,$address,$department,$designation,$qualification,$experience,$aadhaar,$pan,$photoPath,$resumePath);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Faculty registered successfully!"]);
} else {
    @unlink($photoPathAbs);
    @unlink($resumePathAbs);
    echo json_encode(["status"=>"error","message"=>"Insert failed"]);
}

$stmt->close();
$conn->close();
?>
