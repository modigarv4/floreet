document.addEventListener('DOMContentLoaded', () => {
    // so on reload the errors are gone.
    const params = new URLSearchParams(window.location.search);

    if (params.has('error')) {
        params.delete('error');
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.replaceState({}, document.title, newUrl);
    }

    // use either one of this ^

    // if u want the refresh error gone for selected pages only
    // const currentPage = window.location.pathname;

    // if (
    //     currentPage.includes('/subpages/login.php') ||
    //     currentPage.includes('/subpages/signup.php')
    // ) {
    //     const params = new URLSearchParams(window.location.search);
    //     if (params.has('error')) {
    //         params.delete('error');
    //         const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    //         window.history.replaceState({}, document.title, newUrl);
    //     }
    // }


    const otpInput = document.querySelector('input[name="otp"]');
    if (otpInput) otpInput.focus();


    const signupBtn = document.getElementById("signupBtn");
    const fname = document.getElementById("first_name");
    const lname = document.getElementById("last_name");
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    const checklist = {
        length: false,
        upper: false,
        lower: false,
        digit: false,
        special: false
    };

    function validatePasswordChecklist(pwd) {
        checklist.length = pwd.length >= 8;
        checklist.upper = /[A-Z]/.test(pwd);
        checklist.lower = /[a-z]/.test(pwd);
        checklist.digit = /\d/.test(pwd);
        checklist.special = /[^A-Za-z0-9]/.test(pwd);

        document.getElementById("check-length").classList.toggle("done", checklist.length);
        document.getElementById("check-uppercase").classList.toggle("done", checklist.upper);
        document.getElementById("check-lowercase").classList.toggle("done", checklist.lower);
        document.getElementById("check-digit").classList.toggle("done", checklist.digit);
        document.getElementById("check-special").classList.toggle("done", checklist.special);
    }

    function isFormValid() {
        const isFname = fname.value.trim() !== "";
        const isLname = lname.value.trim() !== "";
        const isEmail = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(email.value.trim());
        const isPasswordValid = Object.values(checklist).every(val => val === true);
        return isFname && isLname && isEmail && isPasswordValid;
    }

    function checkAndToggleSubmit() {
        signupBtn.disabled = !isFormValid();
    }

    [fname, lname, email, password].forEach(field => {
        field.addEventListener('input', () => {
            if (field === password) {
                validatePasswordChecklist(field.value);
            }
            checkAndToggleSubmit();
        });
    });


    // Wait for OTP success message to be visible before hiding
    const otpSuccessBubble = document.getElementById('otp-success');
    if (otpSuccessBubble) {
        setTimeout(() => {
            otpSuccessBubble.style.transition = 'opacity 0.5s ease';
            otpSuccessBubble.style.opacity = '0';
            setTimeout(() => {
                otpSuccessBubble.remove();
            }, 500);
        }, 5000); // 20 seconds
    }



    window.addEventListener('pageshow', function (event) {
        // For Safari and Firefox (bfcache), and Chrome/Edge (navigation type)
        const navType = performance.getEntriesByType("navigation")[0]?.type;
        if (event.persisted || navType === "back_forward") {
            window.location.reload();
        }
    });

    const minsSpan = document.getElementById('lockout-mins');
    const secsSpan = document.getElementById('lockout-secs');

    if (minsSpan && secsSpan) {
        let totalSeconds = parseInt(minsSpan.textContent) * 60 + parseInt(secsSpan.textContent);

        const updateTimer = () => {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                location.reload(); // try again after timer
                return;
            }

            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            minsSpan.textContent = mins.toString().padStart(2, '0');
            secsSpan.textContent = secs.toString().padStart(2, '0');
        };

        const timerInterval = setInterval(updateTimer, 1000);
    }

    if (!resendBtn || !resendTimer) {
        console.warn("Resend elements not found");
    }


});




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

// function togglePasswordreset(id, iconId) {
//     const pwd = document.getElementById(id);
//     const icon = document.getElementById(iconId);
//     pwd.type = pwd.type === "password" ? "text" : "password";
//     icon.style.fillOpacity = pwd.type === "password" ? "1" : "0.4";
// }


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
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");

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


    // for email exist
    if (error === "email_exists") {
        showErrorBubble("emailExistsError");
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


// verify-otp
// AUTO-HIDE MESSAGE BUBBLE


const errorText = new URLSearchParams(window.location.search).get('error');
if (errorText && errorText.includes("Too many")) {
    document.getElementById('resendbtn').disabled = true;
    document.getElementById('countdown-otp').textContent = "Locked. Try later.";
}



document.querySelector('input[name="otp"]').addEventListener('paste', function (e) {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    const digits = pasted.replace(/\D/g, '').slice(0, 6);
    this.value = digits;
});


// reset

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

