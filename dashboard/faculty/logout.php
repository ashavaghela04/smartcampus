<?php
// smartcampus/assets/logout.php
session_start();

// Prevent accidental output (extra spaces/newlines)
if (ob_get_length()) ob_clean();

// Clear all session variables
$_SESSION = [];
session_unset();
session_destroy();

// Always return JSON
header('Content-Type: application/json');
echo json_encode(["success" => true]);
exit;
