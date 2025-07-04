<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/connect.php';

if (isset($_SESSION['first_name'])) {
    header('Location: /index.php'); // or your dashboard/homepage
    exit();
}

$email = '';
$step = 'email'; // Default step
$message = '';
$showOtpField = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_email'])) {
        $email = strtolower(trim($_POST['email']));
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $_SESSION['reset_email'] = $email;
            header("Location: /subpages/send-otp.php?from=forgot");
            exit;
        } else {
            $message = "No account found with that email.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $userOtp = trim($_POST['otp']);
        if ($userOtp == $_SESSION['reset_otp']) {
            header("Location: reset.php");
            exit();
        } else {
            $step = 'otp';
            $showOtpField = true;
            $message = "Incorrect OTP. Try again.";
        }
    } elseif (isset($_POST['change_email'])) {
        session_unset();
        session_destroy();
        header("Location: forgot.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Floreet - Forgot Password</title>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php'; ?>
    <style>
        .error-bubble.forgot {
            display: <?= empty($message) ? 'none' : 'block' ?>;
        }
    </style>
</head>

<body>
    <script>
        window.addEventListener('pageshow', function(event) {
            // For Safari and Firefox (bfcache), and Chrome/Edge (navigation type)
            const navType = performance.getEntriesByType("navigation")[0]?.type;
            if (event.persisted || navType === "back_forward") {
                window.location.reload();
            }
        });
    </script>
    <div class="login-container">
        <form method="POST" class="form">
            <p id="heading">Reset Password</p>

            <div class="field" style="position: relative;">
                <input type="email" name="email" class="input-field" placeholder="Enter your email"
                    value="<?= htmlspecialchars($email) ?>" <?= ($step === 'otp') ? 'readonly' : '' ?> required>
            </div>

            <?php if ($showOtpField): ?>
                <div class="field" style="position: relative;">
                    <input type="text" name="otp" class="input-field" placeholder="Enter OTP" required>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="error-bubble forgot"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <div class="btn">
                <?php if ($step === 'otp'): ?>
                    <button class="button1" type="submit" name="verify_otp">Verify OTP</button>
                    <button class="button2" type="submit" name="change_email">Change Email</button>
                <?php else: ?>
                    <button class="button1" type="submit" name="check_email">Send OTP</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>

</html>