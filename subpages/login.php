<?php
session_start();
// Force browser not to cache this page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

if (isset($_SESSION['first_name'])) {
    header("Location: /index.php");
    exit();
}
?>
<?php $invalidrequest = isset($_GET['error']) && $_GET['error'] === 'invalidrequest'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
    ?>
    <title>Floreet - Login</title>
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
        <form class="form" method="POST" action="/backend/login-f.php" autocomplete="on" onsubmit="return validateForm()">
            <a href="/index.php" class="close-btn" title="Close">&times;</a>
            <p id="heading">Login</p>

            <div class="field email">

                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z" />
                </svg>

                <input name="email" id="email" autocomplete="email" placeholder="Email" class="input-field" type="email" required>
            </div>

            <div class="field" style="position: relative;">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                </svg>
                <div class="password-container" style="position: relative; display: flex; align-items: center; width: 100%;">
                    <input name="password" placeholder="Password" class="input-field" autocomplete="current-password" type="password" required id="password" style="padding-right: 2.75rem; width: 100%;">
                    <span class="toggle-eye" onclick="togglePassword()" title="Show/Hide password"
                        style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#FAF6F0" viewBox="0 0 24 24">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                    </span>
                </div>
            </div>

            <!-- SHOWS ERROR MESSAGE -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-bubble" style="top:30%">
                    <?php
                    if ($_GET['error'] === 'invalid') echo 'Invalid email or password.';
                    else if ($_GET['error'] === 'notfound') echo 'No user found with that email.';
                    else echo 'Invalid Credentials.';
                    ?>
                </div>
            <?php endif; ?>

            <div id="invalidrequest" class="error-bubble" style="<?= $invalidrequest ? 'display: block;' : 'display: none;' ?>">
                Invalid Request.
            </div>



            <div class="btn">
                <button type="submit" class="button1" id="loginBtn">
                    <span id="loginText">Login</span>
                    <span id="loginLoader" class="dots-container" style="display: none;">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </span>
                </button>
            </div>

            <div class="btn secondary">
                <button type="button" onclick="window.location.href='/subpages/signup.php'" class="button2 signinbtn">Sign Up</button>
                <button type="button" onclick="window.location.href='/subpages/forgot.php'" class="button2 forgotbtn">Forgot Password</button>
            </div>
        </form>
    </div>

</body>

</html>