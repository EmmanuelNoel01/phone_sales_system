<?php
// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$dbname = "phone_sales_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Security configuration - only define if not already defined
if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', 'your-32-character-encryption-key-here'); // Change this!
    define('ENCRYPTION_METHOD', 'AES-256-CBC');
    define('PASSWORD_COST', 12);
}

// Function declarations with existence checks
if (!function_exists('encryptPassword')) {
    function encryptPassword($password) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
        $encrypted = openssl_encrypt($password, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
}

if (!function_exists('decryptPassword')) {
    function decryptPassword($encrypted) {
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, openssl_cipher_iv_length(ENCRYPTION_METHOD));
        $encrypted = substr($data, openssl_cipher_iv_length(ENCRYPTION_METHOD));
        return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    }
}

if (!function_exists('hashPassword')) {
    function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);
    }
}

if (!function_exists('verifyPassword')) {
    function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>