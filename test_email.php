<?php
/**
 * Email Testing Script
 * Upload this to your live server and access it via browser to test email functionality
 *
 * ⚠️ IMPORTANT: Delete this file after testing!
 */

require_once __DIR__ . '/includes/env_loader.php';
require_once __DIR__ . '/includes/simple_email_service.php';

// Change this to your email address for testing
$TEST_EMAIL = 'your-email@example.com'; // ⚠️ CHANGE THIS TO YOUR EMAIL

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - Athletes Gym</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #21335b;
            border-bottom: 3px solid #2a9d8f;
            padding-bottom: 10px;
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #21335b;
        }
        .success {
            color: #2a9d8f;
            font-weight: bold;
        }
        .error {
            color: #e63946;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #21335b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #1a2847;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Testing - Athletes Gym</h1>

        <div class="warning">
            ⚠️ <strong>Important:</strong> Change the <code>$TEST_EMAIL</code> variable at the top of this file to your email address before testing!
        </div>

        <div class="info">
            ℹ️ <strong>Configuration Status:</strong><br>
            From Email: <code><?= env('MAIL_FROM_ADDRESS', 'noreply@athletesgym.qa') ?></code><br>
            From Name: <code><?= env('MAIL_FROM_NAME', 'Athletes Gym Qatar') ?></code><br>
            Admin Email: <code><?= env('MAIL_ADMIN_ADDRESS', 'info@athletesgym.qa') ?></code>
        </div>

        <?php
        // Check if PHP mail() function exists
        if (!function_exists('mail')) {
            echo '<div class="test-section"><p class="error">❌ ERROR: PHP mail() function is not available on this server!</p></div>';
            exit;
        }

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $testType = $_POST['test_type'] ?? '';

            // Temporarily override admin email for testing
            $_ENV['MAIL_ADMIN_ADDRESS'] = $TEST_EMAIL;

            $emailService = new SimpleEmailService();
            $result = false;

            echo '<div class="test-section">';

            switch ($testType) {
                case 'booking':
                    echo '<h3>Testing Booking Notification...</h3>';
                    $result = $emailService->sendBookingNotification([
                        'name' => 'Test Customer',
                        'email' => $TEST_EMAIL,
                        'phone' => '+974 1234 5678',
                        'service' => 'Personal Training',
                        'date' => date('Y-m-d', strtotime('+7 days')),
                        'time' => '10:00 AM',
                        'message' => 'This is a test booking notification from Athletes Gym'
                    ]);
                    break;

                case 'contact':
                    echo '<h3>Testing Contact Form...</h3>';
                    $result = $emailService->sendContactForm([
                        'name' => 'Test Customer',
                        'email' => $TEST_EMAIL,
                        'phone' => '+974 1234 5678',
                        'message' => 'This is a test contact form submission from Athletes Gym'
                    ]);
                    break;

                case 'order':
                    echo '<h3>Testing Order Confirmation...</h3>';
                    $result = $emailService->sendOrderConfirmation([
                        'order_id' => 'TEST-' . time(),
                        'customer_email' => $TEST_EMAIL,
                        'customer_name' => 'Test Customer',
                        'total' => '250.00',
                        'items' => [
                            ['name' => 'Gym T-Shirt', 'quantity' => 2, 'price' => '100.00'],
                            ['name' => 'Water Bottle', 'quantity' => 3, 'price' => '150.00']
                        ]
                    ]);
                    break;

                case 'password':
                    echo '<h3>Testing Password Reset...</h3>';
                    $result = $emailService->sendPasswordReset($TEST_EMAIL, '123456', 'Test User');
                    break;

                case 'welcome':
                    echo '<h3>Testing Welcome Email...</h3>';
                    $result = $emailService->sendWelcomeEmail($TEST_EMAIL, 'Test User');
                    break;
            }

            if ($result) {
                echo '<p class="success">✓ Email sent successfully!</p>';
                echo '<p>Check your inbox at: <strong>' . htmlspecialchars($TEST_EMAIL) . '</strong></p>';
                echo '<p><small>Note: It may take a few minutes to arrive. Check your spam folder if you don\'t see it.</small></p>';
            } else {
                echo '<p class="error">✗ Failed to send email.</p>';
                echo '<p>Possible reasons:</p>';
                echo '<ul>';
                echo '<li>PHP mail() function is disabled</li>';
                echo '<li>Server email configuration issue</li>';
                echo '<li>SPF/DKIM records not configured</li>';
                echo '</ul>';
                echo '<p>Check your server error logs for more details.</p>';
            }

            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h3>Test Email Types</h3>
            <p>Click a button below to send a test email to: <strong><?= htmlspecialchars($TEST_EMAIL) ?></strong></p>
            <p><small><strong>Note:</strong> Booking and Contact form emails normally go to admin, but will be sent to your test email for testing purposes.</small></p>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_type" value="booking">
                <button type="submit" class="btn">📅 Booking Notification</button>
            </form>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_type" value="contact">
                <button type="submit" class="btn">📧 Contact Form</button>
            </form>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_type" value="order">
                <button type="submit" class="btn">🛒 Order Confirmation</button>
            </form>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_type" value="password">
                <button type="submit" class="btn">🔐 Password Reset</button>
            </form>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_type" value="welcome">
                <button type="submit" class="btn">💪 Welcome Email</button>
            </form>
        </div>

        <div class="test-section">
            <h3>📋 Troubleshooting Checklist</h3>
            <ul>
                <li>✓ PHP mail() function: <strong><?= function_exists('mail') ? 'Available' : 'NOT Available' ?></strong></li>
                <li>Check that email addresses are configured in .env file</li>
                <li>Verify SPF and DKIM records in Hostinger DNS settings</li>
                <li>Check spam folder if email doesn't arrive</li>
                <li>Check server error logs: <code>/home/username/public_html/error_log</code></li>
            </ul>
        </div>

        <div class="warning">
            ⚠️ <strong>Security Notice:</strong> Delete this file after testing! Don't leave it accessible on your live server.
        </div>
    </div>
</body>
</html>
