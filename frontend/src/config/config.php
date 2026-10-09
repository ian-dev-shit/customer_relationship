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

// 1. I-load ang .env mula sa frontend root folder
load_frontend_env(dirname(__DIR__, 2) . '/.env');

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
// Para sa PHP cURL requests - default to localhost for XAMPP, override via .env/Docker for crm_backend
define('INTERNAL_API_URL', env('INTERNAL_API_URL', 'http://127.0.0.1:8000'));

// Para sa Client-side JS fetch 
define('PUBLIC_API_URL', env('PUBLIC_API_URL', 'http://127.0.0.1:8000'));

// Keep backward compatibility 
define('API_BASE_URL', INTERNAL_API_URL);

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
        header("Location: /login.html");
        exit();
    }
}