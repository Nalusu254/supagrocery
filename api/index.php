<?php
session_start();

// Define constants
define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', isset($_SERVER['VERCEL_URL']) ? 'https://' . $_SERVER['VERCEL_URL'] : '//' . $_SERVER['HTTP_HOST']);

// Load environment variables from Vercel
$_ENV['DB_HOST'] = getenv('DB_HOST');
$_ENV['DB_NAME'] = getenv('DB_NAME');
$_ENV['DB_USER'] = getenv('DB_USER');
$_ENV['DB_PASS'] = getenv('DB_PASS');

// Load configuration
require_once ROOT_PATH . '/config/db.php';

// Load common functions and utilities
require_once ROOT_PATH . '/includes/functions.php';

// Get the request path
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = ltrim(substr($path, strlen(dirname($_SERVER['SCRIPT_NAME']))), '/');

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
    case 'api/health':
        header('Content-Type: application/json');
        echo json_encode(['status' => 'healthy', 'timestamp' => time()]);
        break;
    default:
        http_response_code(404);
        require ROOT_PATH . '/src/views/404.php';
        break;
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'message' => 'Supagrocery API is running',
    'php_version' => PHP_VERSION,
    'timestamp' => time()
]);
?> 