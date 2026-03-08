
<?php
// Load environment variables
require_once __DIR__ . '/../../includes/env_loader.php';

// Configure error reporting based on environment
if (env('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../../logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// PDO Database connection using environment variables
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME') . ';charset=utf8mb4',
        env('DB_USER'),
        env('DB_PASS'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    if (env('APP_ENV') === 'production') {
        die('Database connection failed. Please contact support.');
    } else {
        die('Database connection failed: ' . $e->getMessage());
    }
}
