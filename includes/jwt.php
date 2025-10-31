<?php
// JWT Token Handler
// includes/jwt.php

class JWT {
    private static $secret_key = "4f8b3a2e9c7d1f6a5b8e2c9d4f7a3b6e1c8d5f2a9b7e4c3d6f1a8b5e2c9d7f4a3";
    private static $algorithm = 'HS256';
    private static $token_expiry = 86400; // 24 hours in seconds

    // Generate JWT token
    public static function generate($user_id, $email) {
        $issued_at = time();
        $expiration = $issued_at + self::$token_expiry;
        
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ]);
        
        $payload = json_encode([
            'user_id' => $user_id,
            'email' => $email,
            'iat' => $issued_at,
            'exp' => $expiration
        ]);
        
        $base64_header = self::base64UrlEncode($header);
        $base64_payload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64_header . "." . $base64_payload, self::$secret_key, true);
        $base64_signature = self::base64UrlEncode($signature);
        
        return $base64_header . "." . $base64_payload . "." . $base64_signature;
    }

    // Verify and decode JWT token
    public static function verify($token) {
        if (empty($token)) {
            return false;
        }

        $token_parts = explode('.', $token);
        
        if (count($token_parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $token_parts;
        
        // Verify signature
        $valid_signature = hash_hmac('sha256', $header . "." . $payload, self::$secret_key, true);
        $valid_signature = self::base64UrlEncode($valid_signature);
        
        if ($signature !== $valid_signature) {
            return false;
        }
        
        // Decode payload
        $payload_data = json_decode(self::base64UrlDecode($payload), true);
        
        // Check expiration
        if (isset($payload_data['exp']) && $payload_data['exp'] < time()) {
            return false;
        }
        
        return $payload_data;
    }

    // Get token from Authorization header
    public static function getBearerToken() {
        $headers = self::getAuthorizationHeader();
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s+(.*)$/i', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    // Get Authorization header
    private static function getAuthorizationHeader() {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }

    // Base64 URL encode
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Base64 URL decode
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // Validate token and return user data
    public static function validateRequest() {
        $token = self::getBearerToken();
        
        if (!$token) {
            // Check if token is in cookie
            if (isset($_COOKIE['auth_token'])) {
                $token = $_COOKIE['auth_token'];
            } else {
                return null;
            }
        }
        
        $payload = self::verify($token);
        return $payload;
    }
}
?>