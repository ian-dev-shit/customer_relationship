<?php
// POST proxy: HTML login form -> FastAPI /api/auth/login.
// Keeps session + api_helper server-side. Returns JSON.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../src/helpers/api_helper.php';

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!is_array($body)) {
    $body = $_POST;
}

$email = trim($body['email'] ?? '');
$password = trim($body['password'] ?? '');

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and Password should not be empty']);
    exit();
}

$res = make_api_request('/api/auth/login', 'POST', ['email' => $email, 'password' => $password], false);

// Case A: OTP flow (Step 1 complete)
if (($res['status_code'] ?? 500) == 200 && isset($res['data']['status']) && $res['data']['status'] === 'otp_sent') {
    $_SESSION['temp_email'] = $email;
    echo json_encode(['status' => 'otp_sent', 'redirect' => 'otp_verification.html']);
    exit();
}

// Case B: Direct token response (no OTP)
if (($res['status_code'] ?? 500) == 200 && isset($res['data']['access_token'])) {
    $token = $res['data']['access_token'];
    $_SESSION['access_token'] = $token;

    $role = 'customer';
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        $_SESSION['email'] = $payload['email'] ?? $email;
        $role = $payload['role'] ?? 'customer';
        $_SESSION['role'] = $role;
    } else {
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
    }

    $map = [
        'sales' => 'src/views/sales_agent/dashboard.php',
        'sales_agent' => 'src/views/sales_agent/dashboard.php',
        'admin' => 'src/views/admin/dashboard.php',
        'administrator' => 'src/views/admin/dashboard.php',
        'super_admin' => 'src/views/super_admin/dashboard.php',
        'customer' => 'src/views/customer/dashboard.php',
    ];
    if (!isset($map[$role])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized role. Please contact support.']);
        exit();
    }
    echo json_encode(['status' => 'ok', 'role' => $role, 'redirect' => $map[$role]]);
    exit();
}

// Error handling
$detail = $res['data']['detail'] ?? ($res['error'] ?? 'Wrong email or Password.');
if (is_array($detail)) {
    $detail = $detail[0]['msg'] ?? 'Wrong email or Password.';
}
http_response_code($res['status_code'] ?? 401);
echo json_encode(['status' => 'error', 'message' => $detail]);
