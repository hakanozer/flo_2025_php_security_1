<?php

namespace App\Helpers;

class JWTHelper
{
    /**
     * Generate a JWT token
     *
     * @param array $payload
     * @return string
     */
    public static function encode(array $payload): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $base64UrlHeader = self::base64UrlEncode(json_encode($header));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        $signature = self::sign($base64UrlHeader . '.' . $base64UrlPayload, env('JWT_SECRET', 'default_secret_key'));
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    /**
     * Decode a JWT token
     *
     * @param string $jwt
     * @return array|null
     */
    public static function decode(string $jwt): ?array
    {
        $result = null;
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            $result = null;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

        $signature = self::base64UrlDecode($base64UrlSignature);
        $expectedSignature = self::sign($base64UrlHeader . '.' . $base64UrlPayload, env('JWT_SECRET', 'default_secret_key'));

        if (!hash_equals($signature, $expectedSignature)) {
            $result = null;
        }

        $result = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        // Check if token is expired
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            $result = null;
        }

        return $result;
    }

    /**
     * Sign data with secret key using HMAC SHA256
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    private static function sign(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key, true);
    }

    /**
     * Encode data to Base64URL
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode data from Base64URL
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
