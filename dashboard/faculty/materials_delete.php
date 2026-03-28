<?php
session_start();
header("Content-Type: application/json");

// ✅ Ensure faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "faculty") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

$host = "localhost";
$dbname = "smartcampus_1";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$facultyId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // ✅ Fetch file path
    $stmt = $conn->prepare("SELECT file_path FROM materials WHERE id = ? AND faculty_id = ?");
    $stmt->bind_param("ii", $id, $facultyId);
    $stmt->execute();
    $stmt->bind_result($filePath);

    if ($stmt->fetch()) {
        $stmt->close();

        // ✅ Delete file from server
        $fullPath = __DIR__ . "/../" . $filePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // ✅ Delete from DB
        $stmt = $conn->prepare("DELETE FROM materials WHERE id = ? AND faculty_id = ?");
        $stmt->bind_param("ii", $id, $facultyId);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Database error"]);
        }
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Material not found"]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "Invalid request"]);
