// login
const loginForm = document.querySelector(".form");
const loginBtn = document.getElementById("loginBtn");
const loginText = document.getElementById("loginText");
const loginLoader = document.getElementById("loginLoader");

loginForm.addEventListener("submit", () => {
    loginBtn.disabled = true;
    loginText.style.display = "none";
    loginLoader.style.display = "flex";
});

function togglePassword() {
    const pwd = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");
    if (pwd.type === "password") {
        pwd.type = "text";
        icon.style.fillOpacity = "0.4"; // slightly faded when visible
    } else {
        pwd.type = "password";
        icon.style.fillOpacity = "1"; // normal opacity
    }
}

function validateForm() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;

    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;

    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (!password) {
        alert("Please enter your password.");
        return false;
    }

    return true;
}

document.getElementById('email').addEventListener('input', function (e) {
    // Allowed characters in email local part and domain: letters, digits, ., _, %, +, -, @
    // We disallow everything else, including & and uppercase letters (convert to lowercase)

    // Convert input to lowercase
    let value = e.target.value.toLowerCase();

    // Remove any character that is not a-z, 0-9, ., _, %, +, -, @
    value = value.replace(/[^a-z0-9._%+\-@]/g, '');

    // Set the cleaned value back to input
    e.target.value = value;
});


// signup


document.querySelector('.form').addEventListener('submit', function (e) {
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
