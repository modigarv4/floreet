<!DOCTYPE html>
<html lang="en">

<head>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
    ?>  
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <title>Floreet - Sign Up</title>

</head>

<body>
  <div class="login-container">
    <form class="form" method="POST" action="/backend/signup-f.php" onsubmit="validateSequential(event)">
      <a href="/index.php" class="close-btn" title="Close">&times;</a>
      <span class="back-arrow" onclick="window.location.href='/subpages/login.php'">&#8592;</span>
      <p id="heading">Sign Up</p>

      <div style="display: flex; gap: 1rem;">
        <div class="field" style="width: 50%; position: relative;">
          <input id="first_name" name="first_name" placeholder="First Name" class="input-field" type="text"
            oninput="formatName(this); hideAsterisk('fn-asterisk'); this.classList.remove('error');"
            onblur="if(this.value.trim() === '') showAsterisk('fn-asterisk');">
          <span class="asterisk" id="fn-asterisk">*</span>
        </div>

        <div class="field" style="width: 50%; position: relative;">
          <input id="last_name" name="last_name" placeholder="Last Name" class="input-field" type="text"
            oninput="formatName(this); hideAsterisk('ln-asterisk'); this.classList.remove('error');"
            onblur="if(this.value.trim() === '') showAsterisk('ln-asterisk');">
          <span class="asterisk" id="ln-asterisk">*</span>
        </div>
      </div>

      <div class="field" style="position: relative;">
        <input name="email" id="email" placeholder="Email" class="input-field" type="text"
          oninput="forceLowercase(this); hideAsterisk('email-asterisk'); hideErrorBubble('emailError'); this.classList.remove('error');"
          onblur="if(this.value.trim() === '') showAsterisk('email-asterisk'); else if(!/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(this.value)) showErrorBubble('emailError');">
        <span class="asterisk" id="email-asterisk">*</span>
      </div>

      <div class="error-bubble email-error" id="emailError">Please enter a valid email address.</div>

      <div class="field" style="position: relative;">
        <input name="password" id="password" placeholder="Password" class="input-field" type="password"
          onfocus="updatePasswordChecklist()" oninput="updatePasswordChecklist()"
          style="padding-right: 2.5rem;">
        <span class="toggle-eye" onclick="togglePassword()" title="Show/Hide password"
          style="position: absolute; right: 10px; cursor: pointer;">
          <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#FAF6F0"
            viewBox="0 0 24 24">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
          </svg>
        </span>
      </div>

      <!-- ✅ Password Checklist -->
      <div class="password-checklist" id="pwd-checklist">
        <p id="check-length">At least 8 characters</p>
        <p id="check-uppercase">One uppercase letter</p>
        <p id="check-lowercase">One lowercase letter</p>
        <p id="check-digit">One digit</p>
        <p id="check-special">One special character</p>
      </div>

      <div class="btn">
        <button type="submit" id="signupBtn" class="button1">
          <span class="btn-text">Sign Up</span>
          <span class="dots-container" style="display: none;">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </span>
        </button>


      </div>

      <div class="btn secondary" style="gap: 1rem;">
        <button type="button" class="button2" onclick="window.location.href='/subpages/login.php'">Login</button>
        <button type="button" class="button2" onclick="window.location.href='/subpages/forgot.php'">Forgot Password</button>
      </div>
    </form>
  </div>
</body>

</html>