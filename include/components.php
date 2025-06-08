<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function showMessageBubble() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['msg_type'] ?? 'info';
        $msg = $_SESSION['message'];
        echo "<div class='message-bubble $type'>$msg</div>";
        unset($_SESSION['message'], $_SESSION['msg_type']);
    }
}

// 🔹 Loading Dots Component
function loadingDots() {
    echo <<<HTML
    <span class="dots-container" style="display: none;">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </span>
    HTML;
}
?>

<!-- use < ? php  showMessageBubble(); ?>  to use the message bubble-->

<!-- EXAMPLE FOR LOADING BUBBLE -->
<!-- <button type="submit" id="signupBtn" class="button1">
  <span class="btn-text">Sign Up</span>
  < ?php loadingDots(); ?>
</button> -->