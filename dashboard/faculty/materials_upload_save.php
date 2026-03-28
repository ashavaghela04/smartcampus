<?php
// materials_upload_save.php
session_start();
header("Content-Type: application/json");

// ✅ Ensure faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "faculty") {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit;
}

// DB config
$host = "localhost";
$dbname = "smartcampus_1";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// ✅ Validate request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"] ?? "");
    $file    = $_FILES["material_file"] ?? null;

    if (empty($subject) || !$file) {
        echo json_encode(["success" => false, "message" => "Subject and file are required."]);
        exit;
    }

    // ✅ File validation
    $allowedExt = ["pdf", "doc", "docx", "ppt", "pptx"];
    $fileName   = basename($file["name"]);
    $fileTmp    = $file["tmp_name"];
    $fileSize   = $file["size"];
    $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        echo json_encode(["success" => false, "message" => "Invalid file type. Allowed: PDF, DOCX, PPT"]);
        exit;
    }

    if ($fileSize > 10 * 1024 * 1024) { // 10MB limit
        echo json_encode(["success" => false, "message" => "File size exceeds 10MB limit."]);
        exit;
    }

    // ✅ Save file
    $uploadDir = __DIR__ . "/../uploads/materials/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newFileName = uniqid("mat_") . "." . $ext;
    $targetPath  = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmp, $targetPath)) {
        echo json_encode(["success" => false, "message" => "Failed to upload file."]);
        exit;
    }

    // ✅ Insert into DB
    $facultyId = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO materials (faculty_id, subject, file_name, file_path, uploaded_at) 
                            VALUES (?, ?, ?, ?, NOW())");
    $filePathForDb = "uploads/materials/" . $newFileName;
    $stmt->bind_param("isss", $facultyId, $subject, $fileName, $filePathForDb);

    if ($stmt->execute()) {
        $newId = $stmt->insert_id;
        $uploadedAt = date("d M Y, h:i A");

        // ✅ Generate HTML row so AJAX can inject it into the table
        $rowHtml = '
          <tr data-id="'.$newId.'">
            <td>NEW</td>
            <td>'.htmlspecialchars($subject).'</td>
            <td>'.htmlspecialchars($fileName).'</td>
            <td>'.$uploadedAt.'</td>
            <td>
              <form action="../'.$filePathForDb.'" method="get" style="display:inline;">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                  <i class="fas fa-download"></i> Download
                </button>
              </form>
              <button type="button" 
                      class="btn btn-danger btn-sm rounded-pill px-3 deleteBtn" 
                      data-id="'.$newId.'">
                <i class="fas fa-trash"></i> Delete
              </button>
            </td>
          </tr>';

        echo json_encode([
            "success" => true,
            "message" => "File uploaded successfully!",
            "rowHtml" => $rowHtml
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
