<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT($data, $secretKey, $expiry = 86400) {
        // $expirationTime = $issuedAt + 86400;
        // $expiry = 3600
        $issuedAt = time();
        $payload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $expiry,
            'data' => $data
        ];
        return JWT::encode($payload, $secretKey, 'HS256');
    }
}

if (!function_exists('validateJWT')) {
    function validateJWT($token, $secretKey) {
        try {
            return JWT::decode($token, new Key($secretKey, 'HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
}
