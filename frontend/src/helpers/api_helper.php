<?php
require_once dirname(__DIR__) . '/config/config.php';

/**
 * Helper function para sa lahat ng FastAPI Requests (GET, POST, PUT, DELETE)
 *
 * @param string $endpoint Halimbawa: '/api/auth/login'
 * @param string $method HTTP Method (GET, POST, PUT, DELETE)
 * @param array|null $data Payload data
 * @param bool $is_form_data True kung x-www-form-urlencoded, False kung JSON
 * @param array $custom_headers Custom headers na gustong ibato (e.g. ['x-user-id: xxx'])
 * @return array ['status_code' => int, 'data' => array|null, 'error' => string|null]
 */
function make_api_request($endpoint, $method = 'GET', $data = null, $is_form_data = false, $custom_headers = []) {
    // Siguraduhing active ang session para mabasa ang Bearer Token
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $url = API_BASE_URL . $endpoint;
    $ch = curl_init($url);
    
    // Base cURL Options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 seconds timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $headers = [];
    
    // I-attach ang Auth Token kung may valid session
    if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
    }

    // Isama ang Anumang Custom Headers (tulad ng x-user-id)
    if (!empty($custom_headers) && is_array($custom_headers)) {
        $headers = array_merge($headers, $custom_headers);
    }

    // Body / Payload Processing
    if ($data !== null) {
        if ($is_form_data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    curl_close($ch);
    
    // Handler kapag offline/down ang FastAPI Backend Server
    if ($response === false) {
        return [
            'status_code' => 500,
            'data' => null,
            'error' => 'Couldn\'t connect to Backend Server: ' . $curl_error
        ];
    }
    
    // Decode JSON response mula sa FastAPI
    $decoded_data = json_decode($response, true);
    
    return [
        'status_code' => $http_code,
        'data' => $decoded_data ?? $response, // Fallback sa raw string kung hindi JSON
        'error' => ($http_code >= 400) ? ($decoded_data['detail'] ?? 'API Request Failed') : null
    ];
}