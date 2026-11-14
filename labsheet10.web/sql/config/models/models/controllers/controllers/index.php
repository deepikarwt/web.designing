<?php
define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/config/app.php';
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/controllers/ProductController.php';
require_once APP_ROOT . '/controllers/AuthController.php';
require_once APP_ROOT . '/controllers/ApiAuth.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

try {
    if ($path === '/' || $path === '/products') {
        (new ProductController())->index();
    } elseif ($path === '/api/products') {
        (new ProductController())->api();
    } elseif ($path === '/login') {
        (new AuthController())->login();
    } elseif ($path === '/logout') {
        $_SESSION = [];
        session_destroy();
        header('Location: /login');
    } else {
        http_response_code(404);
        require APP_ROOT . '/views/404.php';
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    require APP_ROOT . '/views/500.php';
}