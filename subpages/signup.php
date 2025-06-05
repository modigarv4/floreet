<!DOCTYPE html>
<html lang="en">

<head>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
    ?>  
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <title>Floreet - Sign Up</title>
  <style>
    .error-bubble,
    .password-checklist {
      position: absolute;
      left: 105%;
      background-color: #7A9E7E;
      color: #fff;
      border-radius: 10px;
      padding: 1rem;
      font-size: 0.9rem;
      width: 17rem;
      line-height: 1.5rem;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
      display: none;
    }

    .password-checklist {
      top: 52%;
    }

    .error-bubble.email-error {
      top: 32%;
    }

    .done {
      text-decoration: line-through;
      color: #ccc;
    }

    .field.error input {
      border: 2px solid red;
    }

    .asterisk {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: red;
      font-weight: bold;
      font-size: 1.2rem;
      opacity: 0.5;
      display: none;
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

    .dots-container {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      gap: 6px;
    }

    .dot {
      height: 8px;
      width: 8px;
      border-radius: 50%;
      background-color: #fff;
      /* Use white to match your UI */
      animation: pulse 1.5s infinite ease-in-out;
    }

    .dot:nth-child(1) {
      animation-delay: -0.3s;
    }

    .dot:nth-child(2) {
      animation-delay: -0.1s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.1s;
    }

    @keyframes pulse {
      0% {
        transform: scale(0.8);
        opacity: 0.5;
      }

      50% {
        transform: scale(1.3);
        opacity: 1;
      }

      100% {
        transform: scale(0.8);
        opacity: 0.5;
      }
    }
  </style>
  <script>
    document.querySelector('.form').addEventListener('submit', function(e) {
      const btn = document.getElementById('signupBtn');
      const text = btn.querySelector('.btn-text');
      const dots = btn.querySelector('.dots-container');

      // show animation before page leaves
      text.style.display = 'none';
      dots.style.display = 'flex';
    });

    function togglePassword() {
      const pwd = document.getElementById("password");
      const icon = document.getElementById("eyeIcon");
      pwd.type = pwd.type === "password" ? "text" : "password";
      icon.style.fillOpacity = pwd.type === "password" ? "1" : "0.4";
    }

    function forceLowercase(el) {
      el.value = el.value.toLowerCase();
    }

    function formatName(el) {
      el.value = el.value.replace(/[^A-Za-z]/g, '');
      el.value = el.value.charAt(0).toUpperCase() + el.value.slice(1).toLowerCase();
    }

    function showAsterisk(id) {
      document.getElementById(id).style.display = "block";
    }

    function hideAsterisk(id) {
      document.getElementById(id).style.display = "none";
    }

    function showErrorBubble(id) {
      document.getElementById(id).style.display = "block";
    }

    function hideErrorBubble(id) {
      document.getElementById(id).style.display = "none";
    }

    function validateSequential(e) {
      const fname = document.getElementById("first_name");
      const lname = document.getElementById("last_name");
      const email = document.getElementById("email");
      const password = document.getElementById("password");

      const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
      const pwdPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

      let hasError = false;

      if (fname.value.trim() === "") {
        fname.classList.add("error");
        showAsterisk("fn-asterisk");
        hasError = true;
      }

      if (!pwdPattern.test(password.value)) {
        hasError = true;
      }

      if (lname.value.trim() === "") {
        lname.classList.add("error");
        showAsterisk("ln-asterisk");
        hasError = true;
      }

      if (email.value.trim() === "") {
        email.classList.add("error");
        showAsterisk("email-asterisk");
        hasError = true;
      } else if (!emailPattern.test(email.value)) {
        email.classList.add("error");
        showErrorBubble("emailError");
        hasError = true;
      }

      if (!pwdPattern.test(password.value)) {
        showErrorBubble("passwordError");
        hasError = true;
      } else {
        hideErrorBubble("passwordError");
      }

      if (hasError) e.preventDefault();
    }


    function updatePasswordChecklist() {
      const pwd = document.getElementById("password").value;
      const checklist = document.getElementById("pwd-checklist");

      const checks = {
        length: pwd.length >= 8,
        uppercase: /[A-Z]/.test(pwd),
        lowercase: /[a-z]/.test(pwd),
        digit: /[0-9]/.test(pwd),
        special: /[^A-Za-z0-9]/.test(pwd),
      };

      // Update checklist
      document.getElementById("check-length").className = checks.length ? "done" : "";
      document.getElementById("check-uppercase").className = checks.uppercase ? "done" : "";
      document.getElementById("check-lowercase").className = checks.lowercase ? "done" : "";
      document.getElementById("check-digit").className = checks.digit ? "done" : "";
      document.getElementById("check-special").className = checks.special ? "done" : "";

      // Show if typing and still invalid, hide if valid
      const allValid = Object.values(checks).every(Boolean);
      checklist.style.display = (pwd && !allValid) ? "block" : "none";
    }
  </script>
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