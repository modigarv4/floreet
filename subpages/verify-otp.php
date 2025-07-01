  <?php
  session_start();
  $failed = isset($_GET['error']) && $_GET['error'] === 'failed';
  $sessionexpired = isset($_GET['error']) && $_GET['error'] === 'sessionexpired';
  $toomanyattempts = isset($_GET['error']) && $_GET['error'] === 'toomanyattempts';
  $otpsuccess = isset($_GET['message']) && $_GET['message'] === 'otpsuccess';



  // Force browser not to cache this page
  header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
  header("Pragma: no-cache"); // HTTP 1.0
  header("Expires: 0"); // Proxies

  if (isset($_SESSION['first_name'])) {
    header("Location: /index.php");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
  ?>


  <?php
  require_once ROOT . '/backend/connect.php';
  require_once ROOT . '/PHPMailer/src/PHPMailer.php';
  require_once ROOT . '/PHPMailer/src/SMTP.php';
  require_once ROOT . '/PHPMailer/src/Exception.php';

  use PHPMailer\PHPMailer\PHPMailer;

  $email = $_SESSION['pending_signup']['email'] ?? '';
  $error = $_GET['error'] ?? '';

  $lockoutSecondsLeft = 0;
  if ($toomanyattempts && isset($_SESSION['resend_attempt_log'][$email])) {
    $attempts = $_SESSION['resend_attempt_log'][$email];
    $oldest = min($attempts);
    $elapsed = time() - $oldest;
    $lockoutSecondsLeft = max(600 - $elapsed, 0); // max 10 mins
  }

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
        try {
          $currentTime = new DateTimeImmutable('now', new DateTimeZone('UTC'));
          $expiryTime = new DateTimeImmutable($data['expires_at'], new DateTimeZone('UTC'));

          if ($currentTime > $expiryTime) {
            $error = "OTP expired. Please resend.";
          } elseif (!hash_equals($data['otp'], $enteredOtp)) {
            $error = "Invalid OTP. Try again.";
          } else {
            $user = $_SESSION['pending_signup'];

            $insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'customer')");
            $insert->bind_param("ssss", $user['first_name'], $user['last_name'], $user['email'], $user['password']);
            $insert->execute();

            // ✅ Start session for logged-in user
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'customer';

            // ✅ Delete OTP entry
            $delete = $conn->prepare("DELETE FROM otp_verifications WHERE email = ?");
            $delete->bind_param("s", $email);
            $delete->execute();

            // Send welcome email
            $smtp = require_once ROOT . '/config/smtp-config.php';
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
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Floreet!';
            $mail->Body = "
          <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2 style='color: #7A9E7E;'>Welcome to Floreet, {$user['first_name']}!</h2>
            <p>We’re thrilled to have you as part of our community 🌸</p>
            <p>
              Your account has been successfully created. You can now explore and order beautifully curated flower bouquets directly from our website.
            </p>
            <p>
              If you ever need help, our team is just a message away.
            </p>
            <p style='margin-top: 1.5rem;'>Let the blooming begin,<br><strong>The Floreet Team</strong></p>
            <hr style='margin-top: 2rem;'>
            <small style='color: #999;'>This is an automated email. If you did not sign up for Floreet, please ignore this message.</small>
          </div>
          ";

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
        } catch (Exception $e) {
          $error = "An unexpected error occurred.";
        }
      }
    }
  }
  ?>

  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">

  <body>
    <div class="login-container">
      <div class="form">
        <span class="back-arrow" onclick="window.location.href='/subpages/signup.php'">&#8592;</span>
        <a href="/index.php" class="close-btn" title="Close">&times;</a>
        <p id="heading">Enter OTP</p>

        <!-- OTP FORM -->
        <div class="otpf">
          <div>
            <p class="otp-email">OTP sent to: <strong><?= htmlspecialchars($email) ?></strong></p>
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
          </div>
          <div class="btn verify">
            <button type="submit" class="button1">Verify</button>
          </div>
          </form>
        </div>

        <div id="failed" class="error-bubble" style="<?= $failed ? 'display: block;' : 'display: none;' ?>">
          Failed to send OTP.
        </div>
        <div id="sessionexpired" class="error-bubble" style="<?= $sessionexpired ? 'display: block;' : 'display: none;' ?>">
          Session expired. Please enter your email again.
        </div>
        <div id="toomanyattempts" class="error-bubble" style="<?= $toomanyattempts ? 'display: block;' : 'display: none;' ?>">
          Too many OTP requests. Please wait 10 minutes.
        </div>
        <?php if ($otpsuccess): ?>
          <div class="message-box verify" id="otp-success">OTP sent successfully!</div>
        <?php endif; ?>


        <?php if ($toomanyattempts && $lockoutSecondsLeft > 0): ?>
          <div id="lockout-timer" class="error-bubble" style="display:block; top:40%">
            <span id="lockout-mins"><?= floor($lockoutSecondsLeft / 60) ?></span>:<span id="lockout-secs"><?= str_pad($lockoutSecondsLeft % 60, 2, '0', STR_PAD_LEFT) ?></span>
            seconds.
          </div>
        <?php endif; ?>

        <?php if ($error && $error !== 'toomanyattempts'): ?>
          <div class="error-bubble otp" style="display:block;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>


        <!-- Resend OTP Section (OUTSIDE the OTP form) -->
        <div class="resendf">
          <span class="resend-msg">Didn’t receive the OTP?</span>
          <form method="POST" action="/backend/send-otp.php?resend_started=1" novalidate onsubmit="document.getElementById('resendbtn').disabled = true;">
            <button type="submit" class="resend-btn" id="resendbtn" disabled>
              Resend &nbsp; (<span id="resend-timer">90</span>)
            </button>
          </form>
        </div>


      </div>
    </div>
    <script>
      const successMsg = document.getElementById('otp-success');
      if (successMsg) {
        setTimeout(() => {
          successMsg.style.opacity = '0';
          setTimeout(() => successMsg.style.display = 'none', 500);
        }, 5000); // 3 seconds visible
      }
      // let seconds = 10;
      // const resendBtn = document.getElementById("resendbtn");
      // const timerSpan = document.getElementById("resend-timer");

      // function updateTimer() {
      //   timerSpan.textContent = seconds;
      //   if (seconds > 0) {
      //     seconds--;
      //     setTimeout(updateTimer, 1000);
      //   } else {
      //     resendBtn.disabled = false;
      //   }
      // }

      // updateTimer();

      let countdown = 10;
      const resendBtn = document.getElementById('resendbtn');
      const countdownOtp = document.getElementById('countdown-otp');

      function startCountdown() {
        resendBtn.disabled = true;
        const timer = setInterval(() => {
          countdown--;
          countdownOtp.textContent = `Resend (${countdown})`;
          if (countdown <= 0) {
            clearInterval(timer);
            resendBtn.disabled = false;
            countdownOtp.textContent = 'Resend ?'; // 👈 Fix here
          }
        }, 1000);
      }
    </script>



  </body>