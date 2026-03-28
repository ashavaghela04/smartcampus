<?php
// includes/functions.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config/mail-config.php';

/**
 * Generate a random password (alphanumeric).
 */
function generatePassword($length = 8): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle(str_repeat($chars, 5)), 0, $length);
}

/**
 * Generate an enrollment/username with prefix (SC for students, FC for faculty).
 */
function generateEnrollmentNumber(int $id, string $type = 'student'): string {
    $prefix = ($type === 'faculty') ? 'FC' : 'SC';
    return $prefix . str_pad($id, 5, '0', STR_PAD_LEFT);
}

/**
 * Send email using PHPMailer (mail-config.php handles setup).
 */
function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send SMS via Twilio API.
 */
function sendSMS(string $to, string $message): bool {
    $sid   = getenv('TWILIO_SID');
    $token = getenv('TWILIO_TOKEN');
    $from  = getenv('TWILIO_FROM');

    if (!$sid || !$token || !$from) {
        error_log("Twilio credentials not set");
        return false;
    }

    $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
    $data = http_build_query([
        'From' => $from,
        'To'   => $to,
        'Body' => $message,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, "{$sid}:{$token}");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode >= 200 && $httpcode < 300) {
        return true;
    } else {
        error_log("Twilio response: " . $resp);
        return false;
    }
}
