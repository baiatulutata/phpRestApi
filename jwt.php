<?php

class JwtAuth {

    // Secret key for JWT encoding and decoding (should be stored securely)
    private static $secret_key = 'your_secret_key_here';
    private static $issued_at = null;
    private static $expiration_time = 3600;  // JWT token expiration time (1 hour)
    private static $issuer = 'your_domain.com';

    // Encode data to generate a JWT token
    public static function encode($data) {
        self::$issued_at = time();
        $expiration_time = self::$issued_at + self::$expiration_time;

        // JWT Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // JWT Payload
        $payload = array_merge($data, [
            'iat' => self::$issued_at,
            'exp' => $expiration_time,
            'iss' => self::$issuer
        ]);

        // Encode Header and Payload to base64
        $header_encoded = base64UrlEncode(json_encode($header));
        $payload_encoded = base64UrlEncode(json_encode($payload));

        // Create Signature
        $signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, self::$secret_key, true);
        $signature_encoded = base64UrlEncode($signature);

        // Return JWT token
        return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
    }

    // Decode JWT token
    public static function decode($jwt) {
        $token_parts = explode('.', $jwt);

        if (count($token_parts) != 3) {
            throw new Exception('Invalid token');
        }

        // Decode Header and Payload
        list($header_encoded, $payload_encoded, $signature_encoded) = $token_parts;

        $header = json_decode(base64UrlDecode($header_encoded), true);
        $payload = json_decode(base64UrlDecode($payload_encoded), true);

        // Verify signature
        $signature = base64UrlDecode($signature_encoded);
        $valid_signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, self::$secret_key, true);

        if ($signature !== $valid_signature) {
            throw new Exception('Invalid token signature');
        }

        // Check token expiration
        if ($payload['exp'] < time()) {
            throw new Exception('Token has expired');
        }

        return $payload;
    }

    // Helper function to encode to base64 URL
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Helper function to decode base64 URL
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
