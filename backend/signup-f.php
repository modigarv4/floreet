<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
?> 


<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /subpages/signup.php");
  exit;
}

require_once ROOT . '/PHPMailer/src/PHPMailer.php';
require_once ROOT . '/PHPMailer/src/SMTP.php';
require_once ROOT . '/PHPMailer/src/Exception.php';
require_once ROOT . '/backend/connect.php';
session_start();

$first_name = trim($_POST["first_name"]);
$last_name = trim($_POST["last_name"]);
$email = strtolower(trim($_POST["email"]));
$password = $_POST["password"];

if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
  header("Location: /subpages/signup.php?error=" . urlencode("Please fill all fields"));
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: /subpages/signup.php?error=" . urlencode("Invalid email format"));
  exit;
}

if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/", $password)) {
  header("Location: /subpages/signup.php?error=pwd_format ");
  exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  header("Location: /subpages/signup.php?error=email_exists");
  exit;
}
$stmt->close();

$_SESSION['pending_signup'] = [
  'first_name' => $first_name,
  'last_name' => $last_name,
  'email' => $email,
  'password' => password_hash($password, PASSWORD_DEFAULT),
  'resend_count' => 0,
  'otp_time' => time()
];

header("Location: /backend/send-otp.php");
exit;
