<?php
// Environment setting
define('ENVIRONMENT', getenv('APP_ENV') ?: 'production');

// Base configuration
$config = [
    'production' => [
        'debug' => false,
        'base_url' => 'https://your-domain.com',
        'db' => [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'grocery_db',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
        ],
        'upload_path' => '/var/www/uploads',
        'session' => [
            'lifetime' => 7200,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    ],
    'development' => [
        'debug' => true,
        'base_url' => 'http://localhost',
        'db' => [
            'host' => 'localhost',
            'name' => 'grocery_db',
            'user' => 'root',
            'pass' => '',
        ],
        'upload_path' => __DIR__ . '/../assets/uploads',
        'session' => [
            'lifetime' => 7200,
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    ]
];

// Load environment specific configuration
$current_config = $config[ENVIRONMENT];

// Define constants
define('DEBUG_MODE', $current_config['debug']);
define('UPLOAD_PATH', $current_config['upload_path']);

// Session configuration
ini_set('session.gc_maxlifetime', $current_config['session']['lifetime']);
ini_set('session.cookie_lifetime', $current_config['session']['lifetime']);
ini_set('session.cookie_secure', $current_config['session']['secure']);
ini_set('session.cookie_httponly', $current_config['session']['httponly']);
ini_set('session.cookie_samesite', $current_config['session']['samesite']);

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

return $current_config;
?> 