<?php
// Database Connection Test Script
// Upload this to your Hostinger public_html folder to test DB connection

// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove inline comments
            if (strpos($value, '#') !== false) {
                $value = trim(explode('#', $value)[0]);
            }
            // Remove quotes if present
            $value = trim($value, '"\'');
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    echo "✓ .env file loaded<br><br>";
} else {
    die("✗ .env file not found in: " . __DIR__);
}

// Get database credentials
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

echo "<h2>Database Configuration Test</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Variable</th><th>Value</th><th>Status</th></tr>";

echo "<tr><td>DB_HOST</td><td>" . ($db_host ?: '<em>empty</em>') . "</td><td>" . ($db_host ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>DB_NAME</td><td>" . ($db_name ?: '<em>empty</em>') . "</td><td>" . ($db_name ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>DB_USER</td><td>" . ($db_user ?: '<em>empty</em>') . "</td><td>" . ($db_user ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>DB_PASS</td><td>" . ($db_pass ? str_repeat('*', strlen($db_pass)) : '<em>empty</em>') . "</td><td>" . ($db_pass ? '✓' : '✗') . "</td></tr>";

echo "</table><br>";

// Test database connection
if ($db_host && $db_name && $db_user && $db_pass) {
    echo "<h3>Testing Database Connection...</h3>";

    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        echo "<p style='color: green; font-weight: bold;'>✓ Database connection successful!</p>";

        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name'");
        $result = $stmt->fetch();

        echo "<p>✓ Database has <strong>{$result['count']}</strong> tables</p>";

        // List tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($tables) > 0) {
            echo "<h4>Tables in database:</h4><ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠ Database is empty (no tables found). You need to import the SQL file.</p>";
        }

    } catch (PDOException $e) {
        echo "<p style='color: red; font-weight: bold;'>✗ Database connection failed!</p>";
        echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";

        // Common error messages
        if (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "<p style='color: orange;'>⚠ <strong>Solution:</strong> Check username and password are correct</p>";
        } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
            echo "<p style='color: orange;'>⚠ <strong>Solution:</strong> Database '$db_name' doesn't exist. Create it in hPanel → Databases</p>";
        } elseif (strpos($e->getMessage(), "Can't connect") !== false) {
            echo "<p style='color: orange;'>⚠ <strong>Solution:</strong> Check DB_HOST (should be 'localhost')</p>";
        }
    }
} else {
    echo "<p style='color: red;'>✗ Missing database credentials in .env file</p>";
}

echo "<hr>";
echo "<p><small>Test completed. Delete this file after troubleshooting.</small></p>";
?>
