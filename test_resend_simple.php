<?php
/**
 * Simple Resend Email Test
 * Quick test to verify Resend API is working
 */

require_once __DIR__ . "/includes/env_loader.php";
require_once __DIR__ . "/includes/email_service.php";

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Resend Email Test - Athletes Gym</title>
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
            max-width: 700px;
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
        .config-box {
            background: #f6f6f6;
            border: 2px solid #e7e7e7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .config-box h3 {
            margin-bottom: 15px;
            color: #000;
            font-size: 18px;
        }
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
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
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
        .test-form {
            background: #fff;
            border: 2px solid #000;
            border-radius: 12px;
            padding: 30px;
            margin-top: 20px;
        }
        .test-form h3 {
            margin-bottom: 20px;
            color: #000;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e7e7e7;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #000;
        }
        .btn {
            background: #000;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .result-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .result-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .result h4 {
            margin-bottom: 10px;
        }
        .result pre {
            background: rgba(0,0,0,0.05);
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 13px;
            margin-top: 10px;
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
        .instructions ul {
            margin-left: 20px;
            color: #856404;
        }
        .instructions li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">📧</div>
            <h1>Resend Email Test</h1>
            <p>Athletes Gym Qatar</p>
        </div>

        <div class="content">
            <!-- Configuration Status -->
            <div class="config-box">
                <h3>🔧 Configuration Status</h3>

                <?php
                $apiKey = env('RESEND_API_KEY');
                $fromEmail = env('RESEND_FROM_EMAIL');
                $fromName = env('RESEND_FROM_NAME');
                $adminEmail = env('RESEND_ADMIN_EMAIL');

                $apiKeyStatus = !empty($apiKey);
                ?>

                <div class="config-item">
                    <span class="config-label">API Key</span>
                    <span class="config-value">
                        <?php if ($apiKeyStatus): ?>
                            <?= substr($apiKey, 0, 10) ?>...
                            <span class="status-badge status-success">✓ Set</span>
                        <?php else: ?>
                            <span class="status-badge status-error">✗ Missing</span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="config-item">
                    <span class="config-label">From Email</span>
                    <span class="config-value"><?= htmlspecialchars($fromEmail) ?></span>
                </div>

                <div class="config-item">
                    <span class="config-label">From Name</span>
                    <span class="config-value"><?= htmlspecialchars($fromName) ?></span>
                </div>

                <div class="config-item">
                    <span class="config-label">Admin Email</span>
                    <span class="config-value"><?= htmlspecialchars($adminEmail) ?></span>
                </div>
            </div>

            <?php
            // Process test email if form submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($apiKey)) {
                $testEmail = filter_var($_POST['test_email'] ?? '', FILTER_VALIDATE_EMAIL);
                $testType = $_POST['test_type'] ?? 'simple';

                if ($testEmail) {
                    try {
                        $emailService = new EmailService();

                        switch ($testType) {
                            case 'simple':
                                $result = $emailService->send(
                                    $testEmail,
                                    'Test Email from Athletes Gym',
                                    '<div style="font-family: Arial; padding: 20px; background: #f6f6f6;">
                                        <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto;">
                                            <h1 style="color: #000;">🎉 Success!</h1>
                                            <p style="font-size: 16px; color: #333; line-height: 1.6;">
                                                Your Resend integration is working correctly! This test email was sent from your Athletes Gym website.
                                            </p>
                                            <div style="background: #000; color: white; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center;">
                                                <strong>Athletes Gym Qatar</strong>
                                            </div>
                                        </div>
                                    </div>',
                                    'Success! Your Resend integration is working. This test email was sent from Athletes Gym.'
                                );
                                $testName = 'Simple Test Email';
                                break;

                            case 'otp':
                                $result = $emailService->sendOTP($testEmail, '123456', 'Test User');
                                $testName = 'Password Reset OTP Email';
                                break;

                            case 'order':
                                $result = $emailService->sendOrderConfirmation($testEmail, [
                                    'order_id' => 'TEST-' . time(),
                                    'customer_name' => 'Test Customer',
                                    'total' => '500.00 QR',
                                    'items' => [
                                        ['name' => 'Test Product 1', 'quantity' => 2, 'price' => '200.00'],
                                        ['name' => 'Test Product 2', 'quantity' => 1, 'price' => '300.00']
                                    ]
                                ]);
                                $testName = 'Order Confirmation Email';
                                break;
                        }

                        echo '<div class="result result-success">';
                        echo '<h4>✅ Email Sent Successfully!</h4>';
                        echo '<p><strong>Test Type:</strong> ' . htmlspecialchars($testName) . '</p>';
                        echo '<p><strong>Sent to:</strong> ' . htmlspecialchars($testEmail) . '</p>';
                        echo '<p style="margin-top: 15px;">Check your inbox! The email should arrive within seconds.</p>';
                        if (isset($result['id'])) {
                            echo '<pre>Email ID: ' . htmlspecialchars($result['id']) . '</pre>';
                        }
                        echo '</div>';

                    } catch (Exception $e) {
                        echo '<div class="result result-error">';
                        echo '<h4>❌ Error Sending Email</h4>';
                        echo '<p><strong>Error Message:</strong></p>';
                        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="result result-error">';
                    echo '<h4>❌ Invalid Email Address</h4>';
                    echo '<p>Please enter a valid email address.</p>';
                    echo '</div>';
                }
            }
            ?>

            <!-- Test Form -->
            <div class="test-form">
                <h3>📤 Send Test Email</h3>

                <?php if (!$apiKeyStatus): ?>
                    <div class="result result-error">
                        <h4>⚠️ API Key Missing</h4>
                        <p>Please add your Resend API key to the .env file:</p>
                        <pre>RESEND_API_KEY=re_your_api_key_here</pre>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="test_email">📧 Your Email Address</label>
                            <input
                                type="email"
                                id="test_email"
                                name="test_email"
                                placeholder="your@email.com"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="test_type">📋 Select Test Type</label>
                            <select id="test_type" name="test_type">
                                <option value="simple">Simple Test Email</option>
                                <option value="otp">Password Reset OTP Email</option>
                                <option value="order">Order Confirmation Email</option>
                            </select>
                        </div>

                        <button type="submit" class="btn">
                            🚀 Send Test Email
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h4>📝 Instructions</h4>
                <ul>
                    <li>Enter your email address (you must have access to this inbox)</li>
                    <li>Select the type of email you want to test</li>
                    <li>Click "Send Test Email"</li>
                    <li>Check your inbox (and spam folder)</li>
                    <li>All emails use your Athletes Gym branding (black/white theme)</li>
                </ul>
            </div>

            <div style="margin-top: 30px; text-align: center; color: #666; font-size: 14px;">
                <p><strong>Current API Key:</strong> <?= $apiKeyStatus ? 're_bLYzBP...cbY ✓' : 'Not configured' ?></p>
                <p style="margin-top: 10px;"><a href="/" style="color: #000; text-decoration: none; font-weight: 600;">← Back to Website</a></p>
            </div>
        </div>
    </div>
</body>
</html>
