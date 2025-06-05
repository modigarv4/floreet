<?php
require '/backend/connect.php';
require '/PHPMailer/src/PHPMailer.php';
require '/PHPMailer/src/SMTP.php';
require '/PHPMailer/src/Exception.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_SESSION['pending_signup']['email'] ?? '';
if (!$email) {
    header("Location: /subpages/verify-otp.php?error=" . urlencode("Session expired. Please sign up again."));
    exit;
}


// Limit resend attempts to 3 within session
$email = $_SESSION['pending_signup']['email'] ?? '';

if (!$email) {
    header("Location: /subpages/verify-otp.php?error=" . urlencode("Session expired. Please sign up again."));
    exit;
}

// Track resend attempts per email
if (!isset($_SESSION['resend_attempts'])) {
    $_SESSION['resend_attempts'] = [];
}

$_SESSION['resend_attempts'][$email] = ($_SESSION['resend_attempts'][$email] ?? 0) + 1;

if ($_SESSION['resend_attempts'][$email] > 3) {
    header("Location: /subpages/verify-otp.php?error=" . urlencode("Too many attempts. Try again after 10 minutes."));
    exit;
}
 

// Generate OTP and expiry
$otp = strval(rand(100000, 999999));
$expires_at = (new DateTime())->modify('+5 minutes')->format('Y-m-d H:i:s');


if (count($resendAttempts) >= 3) {
    header("Location: /subpages/verify-otp.php?error=" . urlencode("Too many OTP requests. Try again after 10 minutes."));
    exit;
}

// ✅ Add current time to attempts
$resendAttempts[] = $currentTime;
$_SESSION['resend_attempts'] = $resendAttempts;
$_SESSION['last_otp_sent_at'] = $currentTime;

// ✅ Generate OTP (valid for 5 minutes)
$otp = strval(rand(100000, 999999));
$expires_at = (new DateTime())->modify('+5 minutes')->format('Y-m-d H:i:s');
$last_sent_at = (new DateTime())->format('Y-m-d H:i:s');

// ✅ Store OTP in database (overwrite previous for that email)
$stmt = $conn->prepare("REPLACE INTO otp_verifications (email, otp, expires_at, last_sent_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $email, $otp, $expires_at, $last_sent_at);
$stmt->execute();

// ✅ Load SMTP config
$smtp = require __DIR__ . '/config/smtp-config.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $smtp['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $smtp['username'];
    $mail->Password = $smtp['password'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = $smtp['port'];

    $mail->setFrom($smtp['from_email'], $smtp['from_name']);
    $mail->addAddress($email);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is: $otp\nIt will expire in 5 minutes.";

    $mail->send();
    header("Location: /subpages/verify-otp.php?message=" . urlencode("OTP sent successfully.") . "&resend_started=1");
    exit;
} catch (Exception $e) {
    header("Location: /subpages/verify-otp.php?error=" . urlencode("Failed to send OTP. " . $mail->ErrorInfo));
    exit;
}
