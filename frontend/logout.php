<?php
session_start();
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

// I-redirect sa Login Page
header("Location: login.php");
exit();