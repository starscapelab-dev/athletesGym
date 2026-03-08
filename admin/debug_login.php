<?php
/**
 * Admin Login Debug Script
 * SECURITY WARNING: Delete this file after debugging!
 *
 * Access: https://athletesgym.haziex.com/admin/debug_login.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Admin Login Debug</h2>";
echo "<hr>";

// Test 1: Check database connection
echo "<h3>Test 1: Database Connection</h3>";
try {
    require_once __DIR__ . '/includes/db.php';
    echo "<p style='color: green;'>✓ Database connection successful</p>";

    // Test 2: Check admins table
    echo "<h3>Test 2: Admins Table</h3>";
    $stmt = $pdo->query("SELECT * FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($admins) > 0) {
        echo "<p style='color: green;'>✓ Found " . count($admins) . " admin(s)</p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Password Hash (first 50 chars)</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['id']}</td>";
            echo "<td><strong>{$admin['username']}</strong></td>";
            echo "<td>" . substr($admin['password'], 0, 50) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ No admins found in database</p>";
    }

    // Test 3: Password verification
    echo "<h3>Test 3: Password Verification Test</h3>";
    echo "<form method='POST'>";
    echo "<label>Username: <input type='text' name='test_username' value='admin' required></label><br><br>";
    echo "<label>Password: <input type='password' name='test_password' placeholder='Enter password to test' required></label><br><br>";
    echo "<button type='submit' name='test_login'>Test Login</button>";
    echo "</form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
        $test_username = $_POST['test_username'];
        $test_password = $_POST['test_password'];

        echo "<div style='background: #f0f0f0; padding: 15px; margin-top: 15px;'>";
        echo "<h4>Test Results:</h4>";

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$test_username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            echo "<p>✓ Username '<strong>{$test_username}</strong>' exists in database</p>";
            echo "<p>Password hash in DB: <code>" . htmlspecialchars($admin['password']) . "</code></p>";

            // Test password verification
            $verify_result = password_verify($test_password, $admin['password']);

            if ($verify_result) {
                echo "<p style='color: green; font-weight: bold;'>✓ Password is CORRECT! Login should work.</p>";
                echo "<p><a href='login.php' style='color: blue;'>→ Go to Login Page</a></p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>✗ Password is INCORRECT!</p>";
                echo "<p>You entered: <code>{$test_password}</code></p>";
                echo "<p>Try resetting the password again at: <a href='reset_admin_password.php'>reset_admin_password.php</a></p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Username '<strong>{$test_username}</strong>' not found in database</p>";
        }
        echo "</div>";
    }

    // Test 4: Session check
    echo "<h3>Test 4: Session Configuration</h3>";
    session_start();
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? '✓ Active' : '✗ Inactive') . "</p>";
    echo "<p>Session Save Path: " . session_save_path() . "</p>";

    // Test 5: CSRF Token
    echo "<h3>Test 5: CSRF Protection</h3>";
    if (file_exists(__DIR__ . '/../includes/csrf.php')) {
        require_once __DIR__ . '/../includes/csrf.php';
        echo "<p style='color: green;'>✓ CSRF file exists</p>";
        if (isset($_SESSION['csrf_token'])) {
            echo "<p>✓ CSRF token generated: " . substr($_SESSION['csrf_token'], 0, 20) . "...</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ CSRF file not found</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANT: Delete this file after debugging!</p>";
echo "<p><small>File location: admin/debug_login.php</small></p>";
?>
