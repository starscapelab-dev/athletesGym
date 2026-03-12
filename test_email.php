<?php
/**
 * Email Diagnostic Test
 * Run this in browser to test email configuration
 * Access: http://yoursite.com/test_email.php
 */

// Prevent public access - only specific hosts
$allowed_hosts = ['127.0.0.1', 'localhost', '::1', 'athletesgym.haziex.com'];
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$current_host = $_SERVER['HTTP_HOST'] ?? '';

$is_allowed = in_array($client_ip, $allowed_hosts) || 
              strpos($current_host, 'athletesgym.haziex.com') !== false ||
              strpos($current_host, 'localhost') !== false;

if (!$is_allowed) {
    die('Access denied. This test is only available on allowed hosts.');
}

// Load environment
require_once __DIR__ . '/includes/env_loader.php';
require_once __DIR__ . '/includes/simple_email_service.php';

echo "<h1>📧 Email Configuration Test</h1>";
echo "<hr>";

// Check environment variables
echo "<h2>Environment Configuration</h2>";
echo "<pre>";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n";
echo "MAIL_ADMIN_ADDRESS: " . env('MAIL_ADMIN_ADDRESS') . "\n";
echo "MAIL_BCC_ADDRESS: " . env('MAIL_BCC_ADDRESS') . "\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . env('APP_DEBUG') . "\n";
echo "</pre>";

// Test email sending
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['test_email'] ?? '';
    
    if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<div style='color: red; padding: 10px; background: #fee;'>";
        echo "❌ Invalid email address";
        echo "</div>";
    } else {
        try {
            $emailService = new SimpleEmailService();
            
            $testData = [
                'order_id' => '12345',
                'customer_name' => 'Test User',
                'customer_email' => $testEmail,
                'items' => [
                    ['name' => 'Test Product', 'quantity' => 1, 'price' => 100]
                ],
                'total' => 100,
                'subtotal' => 100,
                'tax' => 0,
                'shipping_fee' => 0
            ];
            
            $success = $emailService->sendOrderConfirmation($testData);
            
            if ($success) {
                echo "<div style='color: green; padding: 10px; background: #efe;'>";
                echo "✅ Test email sent successfully to: {$testEmail}";
                echo "<br><br>";
                echo "Check your email inbox and spam folder.";
                echo "</div>";
            } else {
                echo "<div style='color: red; padding: 10px; background: #fee;'>";
                echo "❌ Failed to send test email";
                echo "<br><br>";
                echo "Check error logs at: error_log";
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div style='color: red; padding: 10px; background: #fee;'>";
            echo "❌ Error: " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
    }
}

// Check PHP mail function
echo "<h2>PHP Mail Configuration</h2>";
echo "<pre>";
echo "mail() function available: " . (function_exists('mail') ? "Yes" : "No") . "\n";
echo "PHP Version: " . phpversion() . "\n";

// Check sendmail_path
$sendmail_path = ini_get('sendmail_path');
echo "sendmail_path: " . ($sendmail_path ? $sendmail_path : "Not configured") . "\n";

// Check smtp settings
$smtp = ini_get('SMTP');
echo "SMTP: " . ($smtp ? $smtp : "Not configured") . "\n";

$smtp_port = ini_get('smtp_port');
echo "SMTP Port: " . ($smtp_port ? $smtp_port : "Not configured") . "\n";
echo "</pre>";

// Recent error log entries
echo "<h2>Recent Error Log Entries</h2>";
$error_log = __DIR__ . '/error_log';
if (file_exists($error_log)) {
    $lines = array_slice(file($error_log), -20);
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>";
    foreach ($lines as $line) {
        if (strpos($line, 'mail') !== false || strpos($line, 'Email') !== false || strpos($line, 'email') !== false) {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "<p>No error log found</p>";
}

// Test form
echo "<h2>Send Test Email</h2>";
echo "<form method='POST'>";
echo "Email Address: <input type='email' name='test_email' required value='your@email.com'>";
echo " <button type='submit'>Send Test Email</button>";
echo "</form>";

echo "<hr>";
echo "<p><strong>Note:</strong> This test file is for debugging only and should be removed in production.</p>";
?>