<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
require_once dirname(__DIR__) . '/backend/connect.php';
require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';
require_once dirname(__DIR__) . '/PHPMailer/src/Exception.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 🧠 Determine context and get email
if (isset($_SESSION['pending_signup']['email'])) {
    $email = $_SESSION['pending_signup']['email'];
    $redirect_page = '/subpages/verify-otp.php';
} elseif (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
    $redirect_page = '/subpages/forgot.php';
} else {
    header("Location: /login.php?error=" . urlencode("Session expired or invalid access."));
    exit;
}

// ✅ Track resend attempts using session
if (!isset($_SESSION['resend_attempts'])) {
    $_SESSION['resend_attempts'] = [];
}
$_SESSION['resend_attempts'][$email] = ($_SESSION['resend_attempts'][$email] ?? 0) + 1;

if ($_SESSION['resend_attempts'][$email] > 3) {
    header("Location: {$redirect_page}?error=" . urlencode("Too many OTP requests. Try again after 10 minutes."));
    exit;
}

// ✅ Generate OTP and store expiry
$otp = strval(rand(100000, 999999));
$expires_at = (new DateTime())->modify('+5 minutes')->format('Y-m-d H:i:s');
$last_sent_at = (new DateTime())->format('Y-m-d H:i:s');

// ✅ Save OTP in DB
$stmt = $conn->prepare("REPLACE INTO otp_verifications (email, otp, expires_at, last_sent_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $email, $otp, $expires_at, $last_sent_at);
$stmt->execute();

// ✅ Load SMTP config
$smtp = require_once ROOT . '/config/smtp-config.php';

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

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is: <strong>$otp</strong><br>It will expire in 5 minutes.";

    $mail->send();

    $_SESSION['last_otp_sent_at'] = $last_sent_at;

    header("Location: {$redirect_page}?message=" . urlencode("OTP sent successfully.") . "&resend_started=1");
    exit;
} catch (Exception $e) {
    header("Location: {$redirect_page}?error=" . urlencode("Failed to send OTP. " . $mail->ErrorInfo));
    exit;
}
