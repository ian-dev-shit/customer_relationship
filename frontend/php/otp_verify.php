<?php
// POST proxy: HTML OTP form -> FastAPI /api/auth/verify-otp.
// Uses $_SESSION['temp_email'] set by php/login.php. Returns JSON.
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

if (!isset($_SESSION['temp_email'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.', 'redirect' => 'login.html']);
    exit();
}

$email = $_SESSION['temp_email'];
$otp_code = trim($body['otp_code'] ?? '');

if ($otp_code === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'OTP should not be empty.']);
    exit();
}

$query_string = http_build_query(['email' => $email, 'otp_code' => $otp_code]);
$endpoint = '/api/auth/verify-otp?' . $query_string;
$response = make_api_request($endpoint, 'POST', null, false);

if (($response['status_code'] ?? 500) == 200 && isset($response['data']['access_token'])) {
    $data = $response['data'];
    $_SESSION['access_token']  = $data['access_token'];
    $_SESSION['refresh_token'] = $data['refresh_token'] ?? null;
    $_SESSION['user_id']       = $data['user_id'] ?? null;
    $_SESSION['email']         = $email;

    $user_role = strtolower($data['role'] ?? '');
    if ($user_role === '') {
        $parts = explode('.', $data['access_token']);
        if (count($parts) === 3) {
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
            $user_role = strtolower($payload['role'] ?? 'customer');
        } else {
            $user_role = 'customer';
        }
    }
    $_SESSION['role'] = $user_role;

    $userId = $_SESSION['user_id'] ?? $data['user_id'] ?? null;
    if ($userId) {
        $profile_res = make_api_request('/api/v1/portal/profile', 'GET', null, false, ['x-user-id: ' . $userId]);
        if (($profile_res['status_code'] ?? 500) == 200 && !empty($profile_res['data'])) {
            $profile = $profile_res['data'];
            $_SESSION['first_name'] = $profile['first_name'] ?? '';
            $_SESSION['last_name']  = $profile['last_name'] ?? '';
            $_SESSION['user_name']  = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) ?: 'Sales Agent';
            $_SESSION['agent_id']   = $profile['agent_id'] ?? $profile['id'] ?? 'SA-014';
            if (!empty($profile['role'])) {
                $user_role = strtolower($profile['role']);
                $_SESSION['role'] = $user_role;
            }
        }
    }

    unset($_SESSION['temp_email']);

    $map = [
        'admin' => '/src/views/admin/dashboard.php',
        'administrator' => '/src/views/admin/dashboard.php',
        'sales_agent' => '/src/views/sales_agent/dashboard.php',
        'sales' => '/src/views/sales_agent/dashboard.php',
        'super_admin' => '/src/views/super_admin/dashboard.php',
        'customer' => '/src/views/customer/dashboard.php',
    ];
    $redirect = $map[$user_role] ?? '/src/views/customer/dashboard.php';
    echo json_encode(['status' => 'ok', 'role' => $user_role, 'redirect' => $redirect]);
    exit();
}

$err = $response['error'] ?? ($response['data']['detail'] ?? 'Invalid OTP or Expired.');
if (is_array($err)) {
    $err = json_encode($err);
}
http_response_code($response['status_code'] ?? 401);
echo json_encode(['status' => 'error', 'message' => $err]);
