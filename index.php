<?php
session_start();
define('ROOT_PATH', __DIR__);
define('BASE_URL', '//' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));

// Load configuration
require_once ROOT_PATH . '/config/db.php';

// Load common functions and utilities
require_once ROOT_PATH . '/includes/functions.php';

// Route the request
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = ltrim(substr($path, strlen(dirname($_SERVER['PHP_SELF']))), '/');

// Simple router
switch ($path) {
    case '':
    case 'home':
        require ROOT_PATH . '/src/controllers/home.php';
        break;
    case 'products':
        require ROOT_PATH . '/src/controllers/products.php';
        break;
    case 'cart':
        require ROOT_PATH . '/src/controllers/cart.php';
        break;
    case 'login':
        require ROOT_PATH . '/src/views/login.php';
        break;
    case 'admin':
        require ROOT_PATH . '/src/views/admin/admin_login.php';
        break;
    default:
        http_response_code(404);
        require ROOT_PATH . '/src/views/404.php';
        break;
}
?> 