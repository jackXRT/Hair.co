<?php
require 'config.php';

class PasswordHash {
    public static function createToken($email) {
        return hash_hmac('sha256', $email . time(), SECRET_KEY);
    }

    public static function validateToken($token, $email, $expiry = 3600) {
        $expected = hash_hmac('sha256', $email . $_SESSION['token_time'], SECRET_KEY);
        return hash_equals($expected, $token) && (time() - $_SESSION['token_time'] < $expiry);
    }
}