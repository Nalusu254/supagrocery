<?php
header('Content-Type: application/json');

$response = [
    'status' => 'ok',
    'php' => [
        'version' => PHP_VERSION,
        'extensions' => get_loaded_extensions(),
        'sapi' => php_sapi_name()
    ],
    'server' => [
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'unknown',
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
    ],
    'time' => [
        'timestamp' => time(),
        'timezone' => date_default_timezone_get()
    ]
];

if (isset($_SERVER['VERCEL_URL'])) {
    $response['vercel'] = [
        'url' => $_SERVER['VERCEL_URL'],
        'env' => $_SERVER['VERCEL_ENV'] ?? 'unknown',
        'region' => $_SERVER['VERCEL_REGION'] ?? 'unknown'
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT); 