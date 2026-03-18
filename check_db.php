<?php
/**
 * Quick database connection test
 * Shows actual error messages regardless of environment
 * DELETE THIS FILE AFTER DEBUGGING!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Load environment
require_once __DIR__ . '/includes/env_loader.php';

echo "<strong>Domain:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "<br>";
echo "<strong>Environment:</strong> " . (env('APP_ENV') ?: 'not set') . "<br><br>";

echo "<strong>Database Credentials Being Used:</strong><br>";
echo "DB_HOST: " . (env('DB_HOST') ?: '(empty)') . "<br>";
echo "DB_NAME: " . (env('DB_NAME') ?: '(empty)') . "<br>";
echo "DB_USER: " . (env('DB_USER') ?: '(empty)') . "<br>";
echo "DB_PASS: " . (env('DB_PASS') ? '***' . substr(env('DB_PASS'), -3) : '(empty)') . "<br><br>";

echo "<hr><h3>Attempting Connection...</h3>";

try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME') . ';charset=utf8mb4',
        env('DB_USER'),
        env('DB_PASS'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<div style='background: #d4edda; padding: 15px; color: #155724; border: 1px solid #c3e6cb;'>";
    echo "<strong>✓ SUCCESS!</strong> Database connection successful!<br>";
    echo "Connected to: " . env('DB_NAME');
    echo "</div>";

    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "<p>Products in database: <strong>" . $result['count'] . "</strong></p>";

} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; color: #721c24; border: 1px solid #f5c6cb;'>";
    echo "<strong>✗ CONNECTION FAILED!</strong><br><br>";
    echo "<strong>Error Code:</strong> " . $e->getCode() . "<br>";
    echo "<strong>Error Message:</strong> " . $e->getMessage() . "<br><br>";

    // Common issues
    echo "<strong>Common Causes:</strong><ul>";
    echo "<li>Wrong database credentials</li>";
    echo "<li>Database user doesn't have permissions</li>";
    echo "<li>Database doesn't exist</li>";
    echo "<li>.env.production file not uploaded or has wrong values</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='background: #fff3cd; padding: 15px; color: #856404; border: 1px solid #ffeaa7;'>";
echo "<strong>⚠️ IMPORTANT:</strong> Delete this file (check_db.php) after debugging for security!";
echo "</div>";
?>
