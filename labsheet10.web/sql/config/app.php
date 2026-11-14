<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', '/');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/storage/app-error.log');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']),
        'samesite' => 'Lax',
        'path' => '/'
    ]);
    session_start();
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function check_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    $saved = $_SESSION['csrf_token'] ?? '';

    if (!$saved || !hash_equals($saved, $sent)) {
        http_response_code(419);
        exit('Invalid request token');
    }
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}