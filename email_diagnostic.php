<?php
/**
 * Email Diagnostic Tool - Debug email configuration and PHP mail() function
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if we have CLI or web access
$isCLI = php_sapi_name() === 'cli';

if (!$isCLI) {
    echo "<h1>📧 Email Diagnostic Report</h1>";
    echo "<pre>";
}

echo "=== PHP MAIL CONFIGURATION ===\n";
echo "PHP Version: " . phpversion() . "\n";

// Check sendmail path
$sendmailPath = ini_get('sendmail_path');
echo "Sendmail Path: " . ($sendmailPath ?: '(not configured)') . "\n";

// Check SMTP configuration (Windows)
$smtp = ini_get('SMTP');
echo "SMTP: " . ($smtp ?: '(not configured)') . "\n";

// Check SMTP_PORT
$smtpPort = ini_get('smtp_port');
echo "SMTP Port: " . ($smtpPort ?: '(not configured)') . "\n";

// Check mail function enabled
$mailFunctionDisabled = ini_get('disable_functions');
if (strpos($mailFunctionDisabled, 'mail') !== false) {
    echo "⚠️ WARNING: mail() function is DISABLED!\n";
} else {
    echo "✅ mail() function is enabled\n";
}

echo "\n=== ENVIRONMENT CONFIGURATION ===\n";

// Load environment
require_once __DIR__ . '/includes/env_loader.php';

echo "Current Domain: " . ($_SERVER['HTTP_HOST'] ?? 'CLI') . "\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n";
echo "MAIL_ADMIN_ADDRESS: " . env('MAIL_ADMIN_ADDRESS') . "\n";
echo "MAIL_BCC_ADDRESS: " . env('MAIL_BCC_ADDRESS') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";

echo "\n=== TEST EMAIL SENDING ===\n";

// Prepare test email
$testTo = env('MAIL_ADMIN_ADDRESS', 'admin@example.com');
$testSubject = "Email Diagnostic Test - " . date('Y-m-d H:i:s');
$testMessage = "This is a test email from Athletes Gym diagnostic script.\n\nTime: " . date('Y-m-d H:i:s') . "\nHost: " . ($_SERVER['HTTP_HOST'] ?? 'CLI');

// Build headers
$headers = "From: Diagnostic <" . env('MAIL_FROM_ADDRESS') . ">\r\n";
$headers .= "Return-Path: <" . env('MAIL_FROM_ADDRESS') . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

echo "Test Email To: {$testTo}\n";
echo "Test Subject: {$testSubject}\n";
echo "Headers:\n" . str_replace("\r\n", "\n", $headers) . "\n";

// Try to send
echo "\nAttempting to send test email...\n";
$result = @mail($testTo, $testSubject, $testMessage, $headers);

if ($result) {
    echo "✅ mail() returned TRUE - Email queued for delivery\n";
} else {
    echo "❌ mail() returned FALSE - Email sending failed\n";
}

echo "\n=== SYSTEM INFORMATION ===\n";
echo "Operating System: " . php_uname() . "\n";
echo "OS Name: " . substr(php_uname('s'), 0, 20) . "\n";

// Check if running on Windows
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "⚠️ Running on WINDOWS - Check SMTP and smtp_port settings in php.ini\n";
} else {
    echo "✅ Running on UNIX/LINUX - Check sendmail configuration\n";
}

echo "\n=== PHP.INI RELEVANT SETTINGS ===\n";
echo "sendmail_from: " . (ini_get('sendmail_from') ?: '(not set)') . "\n";
echo "sendmail_path: " . (ini_get('sendmail_path') ?: '(not set on Windows)') . "\n";

if (!$isCLI) {
    echo "</pre>";
    echo "<hr>";
    echo "<h2>Troubleshooting Guide</h2>";
    echo "<ul>";
    echo "<li><strong>If mail() returns FALSE:</strong> Check your mail server configuration (sendmail on Linux, SMTP on Windows)</li>";
    echo "<li><strong>If mail() returns TRUE but emails don't arrive:</strong> Check spam folder or mail server logs</li>";
    echo "<li><strong>On Hostinger/Shared Hosting:</strong> PHP mail() uses the server's sendmail - usually pre-configured</li>";
    echo "<li><strong>On Local Development:</strong> You may need to configure MailHog or a local mail server</li>";
    echo "</ul>";
}

?>
