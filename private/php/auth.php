<?php
function isAuthorized() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) return false;

    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s+(.*)/i', $authHeader, $matches)) {
        $token = $matches[1];
        return $token === 'titkos_token_123';
    }
    return false;
}