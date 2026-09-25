<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/helpers/api_helper.php';

// Burahin ang lahat ng Session Data
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// I-destroy ang Session
session_destroy();

// Kung galing sa JavaScript Fetch Request (Auto-logout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Logged out successfully"]);
    exit();
}

// Kung galing sa Manual Click (Direct Link Access)
header("Location: login.php");
exit();