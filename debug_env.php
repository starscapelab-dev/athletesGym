<?php
/**
 * Environment Debug Script
 * Upload this to your Hostinger server to diagnose .env issues
 * DELETE THIS FILE AFTER DEBUGGING!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Environment Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Environment Debug Tool</h1>
    <div class="warning">
        <strong>⚠️ SECURITY WARNING:</strong> Delete this file after debugging! It exposes sensitive information.
    </div>

    <h2>Server Information</h2>
    <table>
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        <tr>
            <td><strong>Domain (HTTP_HOST)</strong></td>
            <td><?= $_SERVER['HTTP_HOST'] ?? 'unknown' ?></td>
        </tr>
        <tr>
            <td><strong>Document Root</strong></td>
            <td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'unknown' ?></td>
        </tr>
        <tr>
            <td><strong>Script Path</strong></td>
            <td><?= __DIR__ ?></td>
        </tr>
        <tr>
            <td><strong>PHP Version</strong></td>
            <td><?= phpversion() ?></td>
        </tr>
    </table>

    <h2>Environment File Detection</h2>
    <?php
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Determine which .env file should be loaded
    if (strpos($host, 'athletesgym.haziex.com') !== false) {
        $envFile = '.env.test';
        $detectionReason = 'Domain contains "athletesgym.haziex.com"';
    } elseif (strpos($host, 'athletesgym.qa') !== false) {
        $envFile = '.env.production';
        $detectionReason = 'Domain contains "athletesgym.qa"';
    } else {
        $envFile = '.env';
        $detectionReason = 'Default (localhost or unknown domain)';
    }

    $envPath = __DIR__ . '/' . $envFile;
    ?>

    <table>
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        <tr>
            <td><strong>Expected .env file</strong></td>
            <td><code><?= $envFile ?></code></td>
        </tr>
        <tr>
            <td><strong>Detection Reason</strong></td>
            <td><?= $detectionReason ?></td>
        </tr>
        <tr>
            <td><strong>Full Path</strong></td>
            <td><code><?= $envPath ?></code></td>
        </tr>
        <tr>
            <td><strong>File Exists</strong></td>
            <td><?= file_exists($envPath) ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>' ?></td>
        </tr>
        <?php if (file_exists($envPath)): ?>
        <tr>
            <td><strong>File Readable</strong></td>
            <td><?= is_readable($envPath) ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>' ?></td>
        </tr>
        <tr>
            <td><strong>File Permissions</strong></td>
            <td><code><?= substr(sprintf('%o', fileperms($envPath)), -4) ?></code></td>
        </tr>
        <tr>
            <td><strong>File Size</strong></td>
            <td><?= filesize($envPath) ?> bytes</td>
        </tr>
        <?php endif; ?>
    </table>

    <?php if (!file_exists($envPath)): ?>
    <div class="error">
        <strong>❌ ERROR:</strong> The file <code><?= $envFile ?></code> does not exist!<br><br>
        <strong>Solution:</strong> Upload the <code><?= $envFile ?></code> file to: <code><?= __DIR__ ?>/</code>
    </div>
    <?php elseif (!is_readable($envPath)): ?>
    <div class="error">
        <strong>❌ ERROR:</strong> The file <code><?= $envFile ?></code> exists but is not readable!<br><br>
        <strong>Solution:</strong> Fix file permissions. Run: <code>chmod 600 <?= $envPath ?></code>
    </div>
    <?php else: ?>
    <div class="success">
        <strong>✓ SUCCESS:</strong> Environment file found and readable!
    </div>
    <?php endif; ?>

    <h2>Available .env Files</h2>
    <table>
        <tr>
            <th>Filename</th>
            <th>Exists</th>
            <th>Size</th>
            <th>Permissions</th>
        </tr>
        <?php
        $possibleEnvFiles = ['.env', '.env.production', '.env.test', '.env.hostinger'];
        foreach ($possibleEnvFiles as $file) {
            $filePath = __DIR__ . '/' . $file;
            echo '<tr>';
            echo '<td><code>' . $file . '</code></td>';
            echo '<td>' . (file_exists($filePath) ? '<span style="color: green;">✓ YES</span>' : '<span style="color: #999;">✗ NO</span>') . '</td>';
            echo '<td>' . (file_exists($filePath) ? filesize($filePath) . ' bytes' : '-') . '</td>';
            echo '<td>' . (file_exists($filePath) ? '<code>' . substr(sprintf('%o', fileperms($filePath)), -4) . '</code>' : '-') . '</td>';
            echo '</tr>';
        }
        ?>
    </table>

    <h2>Loading Environment Variables</h2>
    <?php
    if (file_exists(__DIR__ . '/includes/env_loader.php')) {
        echo '<div class="info">Loading environment variables from <code>includes/env_loader.php</code>...</div>';
        try {
            require_once __DIR__ . '/includes/env_loader.php';
            echo '<div class="success">✓ env_loader.php loaded successfully!</div>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Error loading env_loader.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<div class="error">❌ <code>includes/env_loader.php</code> not found!</div>';
    }
    ?>

    <h2>Database Configuration</h2>
    <?php
    function env($key, $default = null) {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
    ?>
    <table>
        <tr>
            <th>Variable</th>
            <th>Value</th>
            <th>Status</th>
        </tr>
        <tr>
            <td><strong>DB_HOST</strong></td>
            <td><code><?= env('DB_HOST') ?: '(not set)' ?></code></td>
            <td><?= env('DB_HOST') ? '<span style="color: green;">✓ Set</span>' : '<span style="color: red;">✗ Missing</span>' ?></td>
        </tr>
        <tr>
            <td><strong>DB_NAME</strong></td>
            <td><code><?= env('DB_NAME') ?: '(not set)' ?></code></td>
            <td><?= env('DB_NAME') ? '<span style="color: green;">✓ Set</span>' : '<span style="color: red;">✗ Missing</span>' ?></td>
        </tr>
        <tr>
            <td><strong>DB_USER</strong></td>
            <td><code><?= env('DB_USER') ?: '(not set)' ?></code></td>
            <td><?= env('DB_USER') ? '<span style="color: green;">✓ Set</span>' : '<span style="color: red;">✗ Missing</span>' ?></td>
        </tr>
        <tr>
            <td><strong>DB_PASS</strong></td>
            <td><?= env('DB_PASS') ? '<code>***' . str_repeat('*', strlen(env('DB_PASS')) - 3) . substr(env('DB_PASS'), -3) . '***</code>' : '<code>(not set)</code>' ?></td>
            <td><?= env('DB_PASS') ? '<span style="color: green;">✓ Set</span>' : '<span style="color: red;">✗ Missing</span>' ?></td>
        </tr>
    </table>

    <h2>Database Connection Test</h2>
    <?php
    if (env('DB_HOST') && env('DB_NAME') && env('DB_USER') && env('DB_PASS')) {
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
            echo '<div class="success">';
            echo '<strong>✓ DATABASE CONNECTION SUCCESSFUL!</strong><br>';
            echo 'Connected to: <code>' . env('DB_NAME') . '</code> as <code>' . env('DB_USER') . '</code>';
            echo '</div>';

            // Test query
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
            $result = $stmt->fetch();
            echo '<div class="info">Products in database: <strong>' . $result['count'] . '</strong></div>';

        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '<strong>❌ DATABASE CONNECTION FAILED!</strong><br><br>';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
    } else {
        echo '<div class="warning">⚠️ Cannot test database connection - missing credentials in environment variables</div>';
    }
    ?>

    <h2>All Loaded Environment Variables</h2>
    <pre><?php print_r($_ENV); ?></pre>

    <div class="error" style="margin-top: 30px;">
        <strong>🔒 IMPORTANT:</strong> Delete this file (<code>debug_env.php</code>) after debugging!<br>
        It exposes sensitive configuration information.
    </div>
</body>
</html>
