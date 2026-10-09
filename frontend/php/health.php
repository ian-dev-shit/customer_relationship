<?php
// Health check: tells you if PHP can reach FastAPI. Open http://127.0.0.1:3000/php/health.php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/helpers/api_helper.php';
$res = make_api_request('/', 'GET', null, false);
echo json_encode([
    'frontend' => 'ok',
    'api_base_url' => defined('API_BASE_URL') ? API_BASE_URL : null,
    'backend_status_code' => $res['status_code'] ?? null,
    'backend_data' => $res['data'] ?? null,
    'backend_error' => $res['error'] ?? null,
]);
