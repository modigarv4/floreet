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
$redirect_page = '/subpages/login.php'; // fallback
if (isset($_SESSION['pending_signup']['email'])) {
    $email = $_SESSION['pending_signup']['email'];
    $redirect_page = '/subpages/verify-otp.php';
} elseif (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
    $redirect_page = '/subpages/forgot.php'; // Correct for forgot
} else {
    $from = $_SERVER['HTTP_REFERER'] ?? $redirect_page;
    header("Location: {$from}?error=" . urlencode("Session expired. Please enter your email again."));
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

    $mail->setFrom($smtp['from_email'], $smtp['from_name'] ?? 'Floreet Support'); // ✅
    $mail->addAddress($email);


    $mail->isHTML(true);
    if (isset($_SESSION['reset_email'])) {
        $_SESSION['reset_otp'] = $otp;
        $mail->Subject = 'Your Password Reset OTP';
        $mail->Body = "
        You requested to reset your password.<br>
        Your OTP is: <strong>$otp</strong><br>
        This code will expire in 5 minutes.<br><br>
        If you didn’t request this, please ignore this email.
    ";
    } else {
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "
        Welcome to Floreet!<br>
        Your OTP is: <strong>$otp</strong><br>
        This code will expire in 5 minutes.<br><br>
        If you didn’t initiate this request, you can safely ignore this message.
    ";
    }

    $mail->send();

    $_SESSION['last_otp_sent_at'] = $last_sent_at;

    header("Location: {$redirect_page}?message=" . urlencode("OTP sent successfully.") . "&resend_started=1");
    exit;
} catch (Exception $e) {
    header("Location: {$redirect_page}?error=" . urlencode("Failed to send OTP. " . $mail->ErrorInfo));
    exit;
}
