<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db/db.php';
require_once __DIR__ . '/../../assets/config/mail-config.php';  // ✅ use centralized mail config

function log_error($msg) {
    error_log("[approve_student.php] " . $msg . "\n", 3, __DIR__ . "/approve_student.log");
}
function respond($arr) {
    echo json_encode($arr);
    exit;
}

// Decode input
$input = json_decode(file_get_contents('php://input'), true);
$student_id = intval($input['student_id'] ?? 0);
if (!$student_id) {
    respond(['status'=>'error','message'=>'Invalid student ID']);
}

// Fetch student (not yet approved)
$stmt = $mysqli->prepare('SELECT id, firstname, email, phone FROM students WHERE id=? AND approved=0');
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) {
    respond(['status'=>'error','message'=>'Student not found or already approved']);
}

// === Transaction (credentials + approval) ===
$mysqli->begin_transaction();
$enrollment_number = 'SC' . str_pad($student['id'], 5, '0', STR_PAD_LEFT);
$password_plain = bin2hex(random_bytes(3));
$password_hash  = password_hash($password_plain, PASSWORD_DEFAULT);

try {
    $chk = $mysqli->prepare('SELECT approved FROM students WHERE id=? FOR UPDATE');
    $chk->bind_param('i', $student_id);
    $chk->execute();
    $row = $chk->get_result()->fetch_assoc();
    if (!$row || $row['approved'] == 1) {
        throw new Exception('Already approved by another process.');
    }

    $ins = $mysqli->prepare('INSERT INTO logins (student_id, enrollment_number, password_hash) VALUES (?,?,?)');
    $ins->bind_param('iss', $student['id'], $enrollment_number, $password_hash);
    if (!$ins->execute()) throw new Exception('Login insert failed: ' . $ins->error);

    $upd = $mysqli->prepare('UPDATE students SET approved=1, enrollment_number=? WHERE id=?');
    $upd->bind_param('si', $enrollment_number, $student['id']);
    if (!$upd->execute()) throw new Exception('Student update failed: ' . $upd->error);

    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    log_error($e->getMessage());
    respond(['status'=>'error','message'=>'Approval failed']);
}

// === Prepare messages ===
$sms_message = "Hello {$student['firstname']}, your enrollment number: {$enrollment_number}, password: {$password_plain}. Please login at your portal.";
$email_subject = "Your Smart Campus Credentials";
$htmlBody = "
  <p>Dear {$student['firstname']},</p>
  <p>Your registration has been approved.</p>
  <p><strong>Enrollment Number:</strong> {$enrollment_number}<br>
     <strong>Password:</strong> {$password_plain}</p>
  <p>Please login here: <a href=\"https://yourdomain.com/login-form.php\">Student Portal</a></p>
  <p>Regards,<br>Smart Campus Team</p>
";

// === Send Email (via mail-config.php) ===
$email_sent = false;
try {
    $mail = getMailer(); // ✅ from mail-config.php
    $mail->addAddress($student['email'], $student['firstname']);
    $mail->Subject = $email_subject;
    $mail->Body    = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);
    $mail->send();
    $email_sent = true;
} catch (Exception $e) {
    log_error("Email send failed: " . $e->getMessage());
}

// (SMS sending code can stay here as-is)

respond([
    'status'     => 'success',
    'message'    => 'Student approved successfully',
    'email_sent' => $email_sent
]);
