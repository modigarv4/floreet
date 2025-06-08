<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/connect.php'; // Adjust path if needed




if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        die("All fields are required.");
    }


    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            // Login success: store user info in session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["first_name"] = $user["first_name"];  // ✅ correct
            $_SESSION["last_name"] = $user["last_name"];    // ✅ correct
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];


            header("Location: /index.php"); // Redirect to home
            exit();
        } else {
            header("Location: /subpages/login.php?error=invalid Credentials.");
            exit();
        }
    } else {
        header("Location: /subpages/login.php?error=notfound");
        exit();
    }
    $stmt->close();
} else {
    header("Location: /subpages/login.php?error=Invalid request.");
    exit();
}
