<?php
/**
 * Database Connection Test
 * Verifies database configuration and connection
 */

// Load environment variables
require_once __DIR__ . "/includes/env_loader.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test - Athletes Gym</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 800px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .header {
            background: #000000;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f6f6f6;
            border-radius: 8px;
            border-left: 4px solid #000;
        }
        .test-section h3 {
            margin-bottom: 15px;
            color: #000;
        }
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
            font-family: monospace;
        }
        .config-item:last-child {
            border-bottom: none;
        }
        .config-label {
            font-weight: 600;
            color: #666;
        }
        .config-value {
            color: #000;
            word-break: break-all;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #28a745;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #dc3545;
        }
        .status-warning {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
        }
        .error-details {
            background: #fff;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
            border: 2px solid #dc3545;
        }
        .error-details pre {
            margin: 10px 0;
            font-size: 13px;
            color: #721c24;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .success-details {
            background: #fff;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
            border: 2px solid #28a745;
        }
        .table-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .table-item {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 13px;
        }
        .instructions {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .instructions h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        .instructions ol {
            margin-left: 20px;
            color: #856404;
        }
        .instructions li {
            margin-bottom: 8px;
        }
        .btn {
            display: inline-block;
            background: #000;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: 600;
        }
        .btn:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🗄️</div>
            <h1>Database Connection Test</h1>
            <p>Athletes Gym Qatar</p>
        </div>

        <div class="content">
            <!-- Configuration Display -->
            <div class="test-section">
                <h3>📋 Current Configuration (.env)</h3>

                <?php
                $dbHost = env('DB_HOST');
                $dbName = env('DB_NAME');
                $dbUser = env('DB_USER');
                $dbPass = env('DB_PASS');
                ?>

                <div class="config-item">
                    <span class="config-label">DB_HOST:</span>
                    <span class="config-value"><?= htmlspecialchars($dbHost ?: 'NOT SET') ?></span>
                </div>
                <div class="config-item">
                    <span class="config-label">DB_NAME:</span>
                    <span class="config-value"><?= htmlspecialchars($dbName ?: 'NOT SET') ?></span>
                </div>
                <div class="config-item">
                    <span class="config-label">DB_USER:</span>
                    <span class="config-value"><?= htmlspecialchars($dbUser ?: 'NOT SET') ?></span>
                </div>
                <div class="config-item">
                    <span class="config-label">DB_PASS:</span>
                    <span class="config-value"><?= empty($dbPass) ? '(empty)' : '****' ?></span>
                </div>
            </div>

            <!-- Connection Test -->
            <div class="test-section">
                <h3>🔌 Connection Test</h3>

                <?php
                $connectionSuccess = false;
                $errorMessage = '';
                $pdo = null;

                try {
                    // Attempt connection
                    $pdo = new PDO(
                        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
                        $dbUser,
                        $dbPass,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false
                        ]
                    );

                    $connectionSuccess = true;

                    echo '<div class="status-badge status-success">';
                    echo '✅ CONNECTION SUCCESSFUL!';
                    echo '</div>';

                    // Get MySQL version
                    $version = $pdo->query('SELECT VERSION()')->fetchColumn();

                    echo '<div class="success-details">';
                    echo '<strong>Connection Details:</strong>';
                    echo '<pre>';
                    echo "MySQL Version: {$version}\n";
                    echo "Host: {$dbHost}\n";
                    echo "Database: {$dbName}\n";
                    echo "User: {$dbUser}\n";
                    echo "Connection established successfully!";
                    echo '</pre>';
                    echo '</div>';

                } catch (PDOException $e) {
                    $errorMessage = $e->getMessage();

                    echo '<div class="status-badge status-error">';
                    echo '❌ CONNECTION FAILED';
                    echo '</div>';

                    echo '<div class="error-details">';
                    echo '<strong>Error Message:</strong>';
                    echo '<pre>' . htmlspecialchars($errorMessage) . '</pre>';
                    echo '</div>';
                }
                ?>
            </div>

            <?php if ($connectionSuccess && $pdo): ?>
                <!-- List Tables -->
                <div class="test-section">
                    <h3>📊 Database Tables</h3>

                    <?php
                    try {
                        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

                        if (empty($tables)) {
                            echo '<div class="status-badge status-warning">';
                            echo '⚠️ NO TABLES FOUND';
                            echo '</div>';
                            echo '<p style="margin-top: 15px;">Database exists but has no tables. You need to import your database.</p>';
                        } else {
                            echo '<div class="status-badge status-success">';
                            echo '✅ ' . count($tables) . ' TABLES FOUND';
                            echo '</div>';

                            echo '<div class="table-list">';
                            foreach ($tables as $table) {
                                echo '<div class="table-item">📋 ' . htmlspecialchars($table) . '</div>';
                            }
                            echo '</div>';

                            // Check for required tables
                            $requiredTables = ['users', 'products', 'orders', 'carts', 'categories'];
                            $missingTables = array_diff($requiredTables, $tables);

                            if (!empty($missingTables)) {
                                echo '<div class="status-badge status-warning" style="margin-top: 15px;">';
                                echo '⚠️ MISSING REQUIRED TABLES';
                                echo '</div>';
                                echo '<p style="margin-top: 10px;">Missing: ' . implode(', ', $missingTables) . '</p>';
                            }
                        }

                    } catch (PDOException $e) {
                        echo '<div class="status-badge status-error">';
                        echo '❌ ERROR LISTING TABLES';
                        echo '</div>';
                        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                    }
                    ?>
                </div>

                <!-- Check Password Reset Columns -->
                <div class="test-section">
                    <h3>🔐 Password Reset Setup</h3>

                    <?php
                    try {
                        $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
                        $columnNames = array_column($columns, 'Field');

                        $hasResetOtp = in_array('reset_otp', $columnNames);
                        $hasResetExpires = in_array('reset_expires', $columnNames);

                        if ($hasResetOtp && $hasResetExpires) {
                            echo '<div class="status-badge status-success">';
                            echo '✅ PASSWORD RESET CONFIGURED';
                            echo '</div>';
                            echo '<p style="margin-top: 15px;">Columns <code>reset_otp</code> and <code>reset_expires</code> exist in users table.</p>';
                        } else {
                            echo '<div class="status-badge status-error">';
                            echo '❌ PASSWORD RESET NOT CONFIGURED';
                            echo '</div>';
                            echo '<p style="margin-top: 15px;">Missing columns: ';
                            if (!$hasResetOtp) echo '<code>reset_otp</code> ';
                            if (!$hasResetExpires) echo '<code>reset_expires</code>';
                            echo '</p>';
                            echo '<p style="margin-top: 10px;">Run the migration SQL in phpMyAdmin:</p>';
                            echo '<pre style="background: #f5f5f5; padding: 10px; margin-top: 10px; border-radius: 4px;">';
                            echo 'ALTER TABLE users ADD COLUMN reset_otp VARCHAR(6) DEFAULT NULL;' . "\n";
                            echo 'ALTER TABLE users ADD COLUMN reset_expires DATETIME DEFAULT NULL;';
                            echo '</pre>';
                        }

                    } catch (PDOException $e) {
                        echo '<div class="status-badge status-warning">';
                        echo '⚠️ COULD NOT CHECK';
                        echo '</div>';
                        echo '<p style="margin-top: 10px;">Users table might not exist yet.</p>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Troubleshooting Instructions -->
            <?php if (!$connectionSuccess): ?>
                <div class="instructions">
                    <h4>🆘 Troubleshooting Steps</h4>

                    <?php if (strpos($errorMessage, 'refused') !== false): ?>
                        <p><strong>MySQL is not running!</strong></p>
                        <ol>
                            <li>Open XAMPP Control Panel</li>
                            <li>Click "Start" next to MySQL</li>
                            <li>Wait until it turns GREEN</li>
                            <li>Refresh this page</li>
                        </ol>
                    <?php elseif (strpos($errorMessage, 'Access denied') !== false): ?>
                        <p><strong>Wrong username or password!</strong></p>
                        <ol>
                            <li>Check your .env file credentials</li>
                            <li>Default XAMPP: user=root, password=(empty)</li>
                            <li>Open phpMyAdmin to verify credentials</li>
                        </ol>
                    <?php elseif (strpos($errorMessage, 'Unknown database') !== false): ?>
                        <p><strong>Database does not exist!</strong></p>
                        <ol>
                            <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                            <li>Click "Import" tab</li>
                            <li>Select your database backup file (.sql)</li>
                            <li>Click "Go" to import</li>
                            <li>Refresh this page</li>
                        </ol>
                    <?php else: ?>
                        <p><strong>General troubleshooting:</strong></p>
                        <ol>
                            <li>Verify XAMPP MySQL is running (GREEN)</li>
                            <li>Check .env file has correct settings</li>
                            <li>Open phpMyAdmin to test connection</li>
                            <li>Check MySQL error logs in XAMPP</li>
                        </ol>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px;">
                <?php if ($connectionSuccess): ?>
                    <a href="http://localhost/athletesGym/" class="btn">✅ Go to Website</a>
                <?php else: ?>
                    <a href="http://localhost/phpmyadmin" class="btn" target="_blank">🔧 Open phpMyAdmin</a>
                <?php endif; ?>
                <a href="javascript:location.reload()" class="btn" style="margin-left: 10px;">🔄 Refresh Test</a>
            </div>
        </div>
    </div>
</body>
</html>
