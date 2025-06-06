<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/send-otp.php'; // optional helper for generateOTP()

if (!isset($_SESSION['reset_email'])) {
    header("Location: /include/forgot.php");
    exit();
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];

    if ($newPass !== $confirmPass) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $newPass)) {
        $error = "Password does not meet the required criteria.";
    } else {
        $hashed = password_hash($newPass, PASSWORD_BCRYPT);
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $email);
        $stmt->execute();

        sendResetSuccessEmail($email); // define this in PHPMailer helpers
        session_unset();
        session_destroy();

        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php'; ?>
    <style>
        .error-bubble {
            position: absolute;
            left: 105%;
            background-color: #7A9E7E;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            top: 40%;
        }

        .success-bubble {
            background-color: #7A9E7E;
            padding: 1rem;
            color: white;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
    </style>
    <script>
        function togglePassword(id, iconId) {
            const pwd = document.getElementById(id);
            const icon = document.getElementById(iconId);
            pwd.type = pwd.type === "password" ? "text" : "password";
            icon.style.fillOpacity = pwd.type === "password" ? "1" : "0.4";
        }

        function updateChecklist() {
            const pwd = document.getElementById("password").value;
            const checklist = document.getElementById("pwd-checklist");

            const checks = {
                length: pwd.length >= 8,
                uppercase: /[A-Z]/.test(pwd),
                lowercase: /[a-z]/.test(pwd),
                digit: /[0-9]/.test(pwd),
                special: /[^A-Za-z0-9]/.test(pwd),
            };

            document.getElementById("check-length").className = checks.length ? "done" : "";
            document.getElementById("check-uppercase").className = checks.uppercase ? "done" : "";
            document.getElementById("check-lowercase").className = checks.lowercase ? "done" : "";
            document.getElementById("check-digit").className = checks.digit ? "done" : "";
            document.getElementById("check-special").className = checks.special ? "done" : "";

            checklist.style.display = (pwd && !Object.values(checks).every(Boolean)) ? "block" : "none";
        }

        <?php if ($success): ?>
        setTimeout(() => {
            window.location.href = "/subpages/login.php";
        }, 3000);
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="login-container">
        <?php if ($success): ?>
            <div class="success-bubble">Password reset successful! Redirecting to login...</div>
        <?php else: ?>
            <form class="form" method="POST">
                <p id="heading">Set New Password</p>

                <div class="field" style="position: relative;">
                    <input name="password" id="password" placeholder="New Password" type="password"
                        class="input-field" oninput="updateChecklist()" onfocus="updateChecklist()" style="padding-right: 2.5rem;">
                    <span class="toggle-eye" onclick="togglePassword('password', 'eyeIcon1')"
                        style="position: absolute; right: 10px; cursor: pointer;">
                        <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#FAF6F0" viewBox="0 0 24 24">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                    </span>
                </div>

                <div class="password-checklist" id="pwd-checklist" style="display: none;">
                    <p id="check-length">At least 8 characters</p>
                    <p id="check-uppercase">One uppercase letter</p>
                    <p id="check-lowercase">One lowercase letter</p>
                    <p id="check-digit">One digit</p>
                    <p id="check-special">One special character</p>
                </div>

                <div class="field" style="position: relative;">
                    <input name="confirm_password" id="confirm_password" placeholder="Confirm Password" type="password"
                        class="input-field" style="padding-right: 2.5rem;">
                    <span class="toggle-eye" onclick="togglePassword('confirm_password', 'eyeIcon2')"
                        style="position: absolute; right: 10px; cursor: pointer;">
                        <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#FAF6F0" viewBox="0 0 24 24">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                    </span>
                </div>

                <?php if ($error): ?>
                    <div class="error-bubble"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="btn">
                    <button type="submit" class="button1">Reset Password</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
