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



// 🧠 Init Resend counter in session
if (!isset($_SESSION['resend_attempt_log'])) {
    $_SESSION['resend_attempt_log'] = [];
}

$emailLog = $_SESSION['resend_attempt_log'][$email] ?? [];
$currentTimestamp = time();

// 🧹 Keep only last 10 minutes of attempts
$emailLog = array_filter($emailLog, fn($ts) => ($currentTimestamp - $ts) <= 600);
$emailLog[] = $currentTimestamp;
$_SESSION['resend_attempt_log'][$email] = $emailLog;

if (count($emailLog) > 3) {
    header("Location: {$redirect_page}?error=" . urlencode("Too many OTP requests. Please wait 10 minutes."));
    exit;
}

// ✅ Generate OTP
$otp = strval(rand(100000, 999999));
$expires_at = (new DateTime())->modify('+5 minutes')->format('Y-m-d H:i:s');
$last_sent_at = (new DateTime())->format('Y-m-d H:i:s');

// ✅ Fetch existing resend data
$stmt = $conn->prepare("SELECT resend_count, last_resend FROM otp_verifications WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    $lastResend = new DateTimeImmutable($data['last_resend'], new DateTimeZone('UTC'));
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $diff = $now->getTimestamp() - $lastResend->getTimestamp(); // seconds

    // ⛔ Server-side lockout if needed
    if ($diff <= 600 && $data['resend_count'] >= 3) {
        header("Location: {$redirect_page}?error=" . urlencode("Too many attempts. Try again after 10 minutes."));
        exit;
    }

    $newCount = ($diff > 600) ? 1 : $data['resend_count'] + 1;

    // ✅ Update existing OTP record
    $update = $conn->prepare("UPDATE otp_verifications SET otp = ?, expires_at = ?, resend_count = ?, last_resend = ? WHERE email = ?");
    $update->bind_param("ssiss", $otp, $expires_at, $newCount, $last_sent_at, $email);
    $update->execute();
} else {
    // 🆕 Insert fresh OTP record
    $resend_count = 1;
    $insert = $conn->prepare("INSERT INTO otp_verifications (email, otp, expires_at, resend_count, last_resend) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("sssis", $email, $otp, $expires_at, $resend_count, $last_sent_at);
    $insert->execute();
}



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

    try {
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }

    $_SESSION['last_resend'] = $last_sent_at;

    header("Location: {$redirect_page}?message=" . urlencode("OTP sent successfully.") . "&resend_started=1");
    exit;
} catch (Exception $e) {
    header("Location: {$redirect_page}?error=" . urlencode("Failed to send OTP. " . $mail->ErrorInfo));
    exit;
}
