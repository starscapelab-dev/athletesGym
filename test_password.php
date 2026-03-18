<?php
/**
 * Test password characters
 * Upload this to verify the password doesn't have hidden characters
 * DELETE THIS FILE AFTER USE!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/env_loader.php';

echo "<h2>Password Analysis</h2>";

$password = env('DB_PASS');

echo "<strong>Password from .env:</strong> " . htmlspecialchars($password) . "<br>";
echo "<strong>Length:</strong> " . strlen($password) . " characters<br>";
echo "<strong>Character breakdown:</strong><br>";

for ($i = 0; $i < strlen($password); $i++) {
    $char = $password[$i];
    echo "Position $i: '$char' (ASCII: " . ord($char) . ")<br>";
}

echo "<hr>";
echo "<h3>Try connecting with this password:</h3>";

// Try different password variations
$passwords = [
    'original' => $password,
    'trimmed' => trim($password),
    'escaped' => addslashes($password)
];

foreach ($passwords as $type => $pass) {
    echo "<strong>Testing: $type</strong> - ";
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=u794267186_athletesgym',
            'u794267186_gymuser',
            $pass
        );
        echo "<span style='color: green;'>✓ SUCCESS!</span><br>";
        echo "Use this password in .env: <code>" . htmlspecialchars($pass) . "</code><br>";
        break;
    } catch (PDOException $e) {
        echo "<span style='color: red;'>✗ Failed: " . $e->getMessage() . "</span><br>";
    }
}

echo "<hr>";
echo "<div style='background: #fff3cd; padding: 15px;'>";
echo "<strong>⚠️ DELETE THIS FILE immediately after use!</strong>";
echo "</div>";
?>
