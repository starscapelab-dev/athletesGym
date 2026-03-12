<?php
/**
 * Email Troubleshooting Guide
 * Access at: https://athletesgym.haziex.com/email_troubleshooting.php
 * Or: https://athletesgym.qa/email_troubleshooting.php
 */

// Prevent public access
$allowed_ips = ['127.0.0.1', 'localhost', '::1'];
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$current_host = $_SERVER['HTTP_HOST'] ?? '';

// Allow access from localhost or admin
$isLocalhost = in_array($client_ip, $allowed_ips) || strpos($current_host, 'localhost') !== false;
$isAdmin = isset($_SESSION['admin_id']) && $_SESSION['admin_id'];

if (!$isLocalhost && !$isAdmin) {
    die('Access denied. This page is for debugging only.');
}

require_once __DIR__ . '/includes/env_loader.php';
require_once __DIR__ . '/includes/simple_email_service.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Troubleshooting - Athletes Gym</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #000; border-bottom: 3px solid #000; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .check { padding: 15px; margin: 10px 0; border-left: 4px solid #ddd; }
        .check.pass { border-left-color: #4CAF50; background: #e8f5e9; }
        .check.fail { border-left-color: #f44336; background: #ffebee; }
        .check.warning { border-left-color: #ff9800; background: #fff3e0; }
        .code { background: #f0f0f0; padding: 10px; border-radius: 3px; font-family: monospace; overflow-x: auto; }
        .step { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .btn { background: #000; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email System Troubleshooting</h1>
        
        <h2>1. PHP Configuration Check</h2>
        
        <?php
        // Check mail function
        if (function_exists('mail')) {
            echo '<div class="check pass">✅ mail() function is available</div>';
        } else {
            echo '<div class="check fail">❌ mail() function is DISABLED - Check php.ini disable_functions</div>';
        }
        
        // Check sendmail/SMTP configuration
        if (PHP_OS_FAMILY === 'Windows') {
            $smtp = ini_get('SMTP');
            $port = ini_get('smtp_port');
            if (!empty($smtp)) {
                echo "<div class='check pass'>✅ Windows SMTP configured: {$smtp}:{$port}</div>";
            } else {
                echo "<div class='check fail'>❌ Windows SMTP not configured in php.ini</div>";
            }
        } else {
            $sendmail = ini_get('sendmail_path');
            if (!empty($sendmail)) {
                echo "<div class='check pass'>✅ sendmail_path configured: {$sendmail}</div>";
            } else {
                echo "<div class='check warning'>⚠️ sendmail_path not configured - Check with hosting provider</div>";
            }
        }
        
        // Check email configuration
        echo '<h2>2. Email Configuration</h2>';
        $config = [
            'FROM' => env('MAIL_FROM_ADDRESS'),
            'FROM_NAME' => env('MAIL_FROM_NAME'),
            'ADMIN' => env('MAIL_ADMIN_ADDRESS'),
            'BCC' => env('MAIL_BCC_ADDRESS'),
            'ENV' => env('APP_ENV'),
            'URL' => env('APP_URL'),
        ];
        
        foreach ($config as $key => $value) {
            if ($value) {
                echo "<div class='check pass'>✅ {$key}: {$value}</div>";
            } else {
                echo "<div class='check fail'>❌ {$key}: NOT SET</div>";
            }
        }
        
        // Test email sending
        echo '<h2>3. Test Email Sending</h2>';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $testEmail = $_POST['test_email'] ?? env('MAIL_ADMIN_ADDRESS');
            
            if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                echo "<div class='check fail'>❌ Invalid email: {$testEmail}</div>";
            } else {
                echo "<div class='step'>Sending test email to: {$testEmail}</div>";
                
                try {
                    $emailService = new SimpleEmailService();
                    
                    // Send a simple test email
                    $subject = "Test Email - " . date('Y-m-d H:i:s');
                    $message = $emailService->getWelcomeTemplate("Tester");
                    
                    $reflection = new ReflectionClass($emailService);
                    $method = $reflection->getMethod('send');
                    $method->setAccessible(true);
                    $result = $method->invoke($emailService, $testEmail, $subject, $message);
                    
                    if ($result) {
                        echo "<div class='check pass'>✅ Test email queued successfully</div>";
                        echo "<div class='step'>Check your email inbox (and spam folder) for: <strong>{$testEmail}</strong></div>";
                    } else {
                        echo "<div class='check fail'>❌ Test email FAILED</div>";
                        echo "<div class='step'>Check error_log for details</div>";
                    }
                } catch (Exception $e) {
                    echo "<div class='check fail'>❌ Exception: " . $e->getMessage() . "</div>";
                }
            }
        } else {
            echo '<form method="POST">';
            echo '<div class="step">';
            echo '<label>Test email address:</label><br>';
            echo '<input type="email" name="test_email" value="' . env('MAIL_ADMIN_ADDRESS') . '" style="width: 300px; padding: 8px;">';
            echo '<button type="submit" class="btn">Send Test Email</button>';
            echo '</div>';
            echo '</form>';
        }
        
        // Hostinger-specific troubleshooting
        echo '<h2>4. Hosting-Specific Issues</h2>';
        
        if (strpos($_SERVER['HTTP_HOST'] ?? '', 'haziex.com') !== false) {
            echo '<div class="step">';
            echo '<h3>Hostinger Mail Configuration</h3>';
            echo '<ol>';
            echo '<li>Log in to Hostinger control panel</li>';
            echo '<li>Go to Email section</li>';
            echo '<li>Verify email address is set up for your domain</li>';
            echo '<li>Check that mail service is active</li>';
            echo '<li>Verify SPF/DKIM records are configured</li>';
            echo '</ol>';
            
            echo '<h4>Common Solutions:</h4>';
            echo '<ul>';
            echo '<li>Use "noreply@athletesgym.haziex.com" as From address (must match domain)</li>';
            echo '<li>Set MAIL_FROM_ADDRESS to a valid email on the domain</li>';
            echo '<li>Contact Hostinger support if sendmail is not working</li>';
            echo '</ul>';
            echo '</div>';
        }
        
        // Error log check
        echo '<h2>5. Recent Error Log</h2>';
        if (file_exists(__DIR__ . '/error_log')) {
            echo '<div class="step">';
            echo '<h3>Recent entries (last 20 lines):</h3>';
            $lines = array_slice(file(__DIR__ . '/error_log'), -20);
            echo '<div class="code">';
            foreach ($lines as $line) {
                if (strpos($line, 'email') !== false || strpos($line, 'mail') !== false || strpos($line, 'MAIL') !== false) {
                    echo htmlspecialchars($line) . "\n";
                }
            }
            echo '</div>';
            echo '</div>';
        }
        
        echo '<h2>6. Quick Fixes to Try</h2>';
        echo '<div class="step">';
        echo '<ol>';
        echo '<li><strong>Check .env file:</strong> Verify MAIL_FROM_ADDRESS matches domain (noreply@athletesgym.haziex.com)</li>';
        echo '<li><strong>Hostinger:</strong> Contact support to verify sendmail service is running</li>';
        echo '<li><strong>Check spam:</strong> Send test email and check spam folder</li>';
        echo '<li><strong>Check permissions:</strong> Ensure /tmp_sessions directory is writable</li>';
        echo '<li><strong>Review logs:</strong> Check error_log file for PHP errors before mail() call</li>';
        echo '</ol>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
