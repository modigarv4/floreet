// Runs only when user navigates back (browser back/forward)
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return;

    const path = window.location.pathname;

    // if (path.includes('your/path/here.php')) {
    //     resetPageUI('field1Id', 'field2Id', 'buttonTextId', 'loaderId');
    // }

    // -------- Login Page Reset --------
    if (path.includes('/subpages/login.php')) {
        resetPageUI('email', 'password', 'loginText', 'loginLoader');
    }

    // -------- Signup Page Reset --------
    else if (path.includes('/subpages/signup.php')) {
        resetPageUI('first_name', 'email', 'signupText', 'signupLoader');
        clearOTPField(); // Optional: clear OTP input if used
    }

    // -------- Reset Password Page Reset --------
    else if (path.includes('/subpages/reset.php')) {
        resetPageUI('new_password', 'confirm_password', 'resetText', 'resetLoader');
    }

    // -------- Forgot Password Page Reset --------
    else if (path.includes('/subpages/forgot.php')) {
        resetPageUI('email', null, 'resetText', 'resetLoader');
        clearOTPField(); // Optional
    }
});

// --- Generic UI Reset Function ---
function resetPageUI(field1Id, field2Id, buttonTextId, loaderId) {
    if (field1Id) {
        const field1 = document.getElementById(field1Id);
        if (field1) field1.value = "";
    }

    if (field2Id) {
        const field2 = document.getElementById(field2Id);
        if (field2) field2.value = "";
    }

    const btnText = document.getElementById(buttonTextId);
    const loader = document.getElementById(loaderId);
    if (btnText && loader) {
        btnText.style.display = "inline";
        loader.style.display = "none";
    }

    const errorBubble = document.querySelector(".error-bubble");
    if (errorBubble) errorBubble.remove();

    const invalidBlock = document.getElementById("invalidrequest");
    if (invalidBlock) invalidBlock.style.display = "none";
}

// --- Optional: clear OTP input field if present ---
function clearOTPField() {
    const otpField = document.querySelector('input[name="otp"]');
    if (otpField) otpField.value = "";
}
