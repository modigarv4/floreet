<?php
require 'connect.php'; // DB connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';
require_once dirname(__DIR__) . '/PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;

    if ($name && $email && $message && $rating !== null && $rating >= 1 && $rating <= 5) {
        // Store in DB
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message, rating) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $message, $rating);
        if ($stmt->execute()) {
            // Email confirmation
            $mail = new PHPMailer(true);

            try {
                define('ROOT', dirname(__DIR__));
                $smtp = require ROOT . '/config/smtp-config.php';

                $mail->isSMTP();
                $mail->Host       = $smtp['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp['username'];
                $mail->Password   = $smtp['password'];
                $mail->SMTPSecure = 'tls';
                $mail->Port       = $smtp['port'];

                $mail->setFrom($smtp['from_email'], $smtp['from_name'] ?? 'Floreet');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'We have received your message - Floreet';
                $mail->Body    = "
                    <p>Hi <strong>$name</strong>,</p>
                    <p>Thanks for reaching out to us! We have received your message and will get back to you shortly.</p>
                    <p><strong>Your Message:</strong></p>
                    <blockquote>$message</blockquote>
                    <p>Best regards,<br>Team Floreet</p>
                ";

                $mail->send();
                header("Location: contact.php?success=1");
                exit();
            } catch (Exception $e) {
                error_log("Mail error: {$mail->ErrorInfo}");
                header("Location: /subpages/contact.php?success=0&error=email");
                exit();
            }
        } else {
            header("Location: /subpages/contact.php?success=0&error=db");
            exit();
        }
    } else {
        header("Location: /subapages/contact.php?success=0&error=invalid");
        exit();
    }
} else {
    header("Location: /subpages/contact.php");
    exit();
}
