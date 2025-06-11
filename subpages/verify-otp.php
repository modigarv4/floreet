<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
?>


<?php
require_once ROOT . '/backend/connect.php';
require_once ROOT . '/PHPMailer/src/PHPMailer.php';
require_once ROOT . '/PHPMailer/src/SMTP.php';
require_once ROOT . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

session_start();
$email = $_SESSION['pending_signup']['email'] ?? '';
$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
  $enteredOtp = $_POST['otp'];

  if (!$email) {
    $error = "Session expired. Please sign up again.";
  } else {
    $stmt = $conn->prepare("SELECT otp, expires_at FROM otp_verifications WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
      $error = "No OTP found. Please resend.";
    } else {
      $currentTime = new DateTimeImmutable('now', new DateTimeZone('UTC'));
      $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
        ->modify('+10 minutes')
        ->format('Y-m-d H:i:s');



      if ($currentTime > $expiryTime) {
        $error = "OTP expired. Please resend.";
      } elseif ($enteredOtp !== $data['otp']) {
        $error = "Invalid OTP. Try again.";
      } else {
        $user = $_SESSION['pending_signup'];

        $insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $insert->bind_param("ssss", $user['first_name'], $user['last_name'], $user['email'], $user['password']);
        $insert->execute();

        // Send welcome email
        $smtp = require __DIR__ . '/config/smtp_config.php';
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $smtp['port'];

        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
        $mail->addAddress($user['email'], $user['first_name']);
        $mail->Subject = 'Welcome to Floreet!';
        $mail->Body = "Hi {$user['first_name']}, your account was successfully created.";

        try {
          $mail->send();
        } catch (Exception $e) {
          // You can log $mail->ErrorInfo if needed
        }


        unset($_SESSION['pending_signup']);
        $conn->query("DELETE FROM otp_verifications WHERE email = '$email'");
        header("Location: /index.php");
        exit;
      }
    }
  }
}
?>

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">

<style>
  .error-bubble {
    position: absolute;
    left: 105%;
    top: 27%;
    background-color: #7A9E7E;
    color: #fff;
    border-radius: 10px;
    padding: 1rem;
    font-size: 0.9rem;
    width: 17rem;
    line-height: 1.5rem;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    z-index: 100;
  }

  .message-box {
    position: absolute;
    left: 105%;
    top: 27%;
    background-color: #4CAF50;
    color: #fff;
    border-radius: 10px;
    padding: 1rem;
    font-size: 0.9rem;
    width: 17rem;
    line-height: 1.5rem;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
  }

  .btn.verify {
    margin-bottom: 1rem;
  }

  .back-arrow {
    position: absolute;
    top: 10px;
    left: 15px;
    font-size: 20px;
    cursor: pointer;
    color: white;
    font-weight: bold;
  }

  .login-container {
    display: flex;
    flex-direction: column;
  }
</style>

<body>
  <div class="login-container">
    <div class="form">
      <span class="back-arrow" onclick="window.location.href='/subpages/signup.php'">&#8592;</span>
      <a href="/index.php" class="close-btn" title="Close">&times;</a>
      <p id="heading">Enter OTP</p>
      <!-- OTP FORM -->
      <div class="otpf">
        <div>
          <form method="POST">
            <div class="field" style="position: relative;">
              <input
                name="otp"
                placeholder="6-digit OTP"
                class="input-field"
                type="text"
                inputmode="numeric"
                pattern="\d{6}"
                maxlength="6"
                autocomplete="one-time-code"
                oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6);" />
            </div>
            <?php if (!empty($error)): ?>
              <div class="error-bubble"><?= htmlspecialchars($error) ?></div>
            <?php elseif (!empty($message)): ?>
              <div class="message-box"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
        </div>
        <div class="btn verify">
          <button type="submit" class="button1">Verify</button>
        </div>
        </form>
      </div>
      <!-- Resend OTP Section (OUTSIDE the OTP form) -->
      <div class="resendf">
        <span class="resend-msg">Didn’t receive the OTP?</span>
        <form method="POST" action="/backend/send-otp.php?resend_started=1" novalidate>
          <button type="submit" class="resend-btn" id="resendbtn">Resend OTP</button>
        </form>
      </div>

    </div>
  </div>


  <script>
    // TIMER CONTROL
    const urlParams = new URLSearchParams(window.location.search);
    const shouldStartTimer = urlParams.has('resend_started');

    if (shouldStartTimer) {
      const btn = document.getElementById("resendbtn");
      let countdown = 120;
      btn.disabled = true;

      const interval = setInterval(() => {
        if (countdown > 0) {
          btn.innerText = `Wait (${countdown}s)`;
          countdown--;
        } else {
          btn.disabled = false;
          btn.innerText = "Resend OTP";
          clearInterval(interval);
        }
      }, 1000);
    }

    // AUTO-HIDE MESSAGE BUBBLE
    setTimeout(() => {
      const bubble = document.querySelector('.error-bubble');
      if (bubble) {
        bubble.style.transition = 'opacity 0.5s ease';
        bubble.style.opacity = '0';
        setTimeout(() => bubble.remove(), 500);
      }
    }, 4000);

    document.querySelector('input[name="otp"]').addEventListener('paste', function(e) {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData).getData('text');
      const digits = pasted.replace(/\D/g, '').slice(0, 6);
      this.value = digits;
    });

    document.addEventListener('DOMContentLoaded', () => {
      const otpInput = document.querySelector('input[name="otp"]');
      if (otpInput) otpInput.focus();
    });
  </script>
</body>

</html>