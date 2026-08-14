<?php

function load_frontend_env($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $line = trim($line);

        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);

            $value = trim($value, '"\'');

            // I-set sa environment global variables
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// 1. I-load ang .env mula sa root folder
load_frontend_env(dirname(__DIR__) . '/.env');

/**
 * Helper function para madaling makakuha ng env values na may default fallback
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $_ENV[$key] ?? $default;
    }
    return $value;
}

// 2. Constants Configuration
define('API_BASE_URL', env('API_BASE_URL', 'http://127.0.0.1:8000'));
define('APP_NAME', env('APP_NAME', 'Customer Relationship'));
define('SUPABASE_URL', env('SUPABASE_URL', ''));
define('SUPABASE_ANON_KEY', env('SUPABASE_ANON_KEY', ''));

/**
 * 3. Auth Guard Helper
 */
function check_auth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION["access_token"])) {
        header("Location: login.php");
        exit();
    }
}