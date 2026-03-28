<?php
header('Content-Type: application/json');

// DB connection
$conn = new mysqli("localhost", "root", "", "smartcampus");
if ($conn->connect_error) {
    die(json_encode(["status"=>"error","message"=>"DB connection failed"]));
}

// Collect inputs safely
$email   = $_POST['email']   ?? '';
$aadhaar = $_POST['aadhaar'] ?? '';
$phone   = $_POST['phone']   ?? '';
$pan     = $_POST['pan']     ?? '';
$type    = $_POST['type']    ?? 'student'; // default student

// Whitelist table + columns
$tables = [
    "student" => "students",
    "faculty" => "faculty"
];

$columns = [
    "email"   => "email",
    "aadhaar" => "aadhaar",
    "phone"   => "phone",
    "pan"     => "pan"
];

if (!isset($tables[$type])) {
    echo json_encode(["status"=>"error", "message"=>"Invalid type"]);
    exit;
}

$table = $tables[$type];

// Helper function to check duplicates
function checkDuplicate($conn, $table, $column, $value, $fieldName){
    if ($value === '') return false;

    $query = "SELECT id FROM $table WHERE $column = ?";
    $stmt = $conn->prepare($query);
    if(!$stmt){
        echo json_encode(["status"=>"error", "message"=>"DB prepare failed"]);
        exit;
    }
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res && $res->num_rows > 0){
        echo json_encode(["status"=>"error", "field"=>$fieldName, "message"=>"This $fieldName is already registered."]);
        exit;
    }
    return false;
}

// Run checks
checkDuplicate($conn, $table, $columns['email'], $email, 'email');
checkDuplicate($conn, $table, $columns['aadhaar'], $aadhaar, 'aadhaar');
if ($type === "faculty") {
    checkDuplicate($conn, $table, $columns['phone'], $phone, 'phone');
    checkDuplicate($conn, $table, $columns['pan'], $pan, 'pan');
}

// All good
echo json_encode(["status"=>"ok", "type"=>$type]);
