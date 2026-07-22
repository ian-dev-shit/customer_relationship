<?php

function load_frontend_env($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Hatiin sa key at value gamit ang '='
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Tanggalin ang mga nakabalot na quotes kung mayroon man
        $value = trim($value, '"\'');

        // I-set sa environment variables ng server PHP
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

load_frontend_env(dirname(__DIR__) . '/.env');

define('API_BASE_URL', getenv('API_BASE_URL') ?: 'http://127.0.0.1:8000/api/auth');
define('APP_NAME', getenv('APP_NAME') ?: 'Customer Relationship');


// Auth helper function 
function check_auth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["access_token"])) {
        header("Location: login.php");
        exit();
    }
}