<?php
require_once 'common.php';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

if (isset($_COOKIE['remember'])) {
    $parts = explode(':', $_COOKIE['remember'], 2);

    if (count($parts) === 2) {
        $stmt = $db->prepare(
            'UPDATE users SET remember_token_hash = NULL WHERE id = ?'
        );
        $stmt->bind_param('i', $parts[0]);
        $stmt->execute();
    }

    setcookie('remember', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']),
        'samesite' => 'Lax'
    ]);
}

session_destroy();
header('Location: login.php');
exit;