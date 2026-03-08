<?php
/**
 * Email Testing Script
 * Use this to test your Resend integration
 *
 * Access: http://localhost/athletesGym/test_email.php
 * Delete this file when done testing!
 */

require_once "includes/env_loader.php";
require_once "includes/email_service.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Test - Athletes Gym</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .test-btn { padding: 10px 20px; background: #21335b; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .test-btn:hover { background: #1a2847; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 Resend Email Test</h1>
    <p><strong>API Key:</strong> <?= substr(env('RESEND_API_KEY'), 0, 10) ?>...</p>
    <p><strong>From Email:</strong> <?= env('RESEND_FROM_EMAIL') ?></p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $testType = $_POST['test_type'] ?? '';
        $testEmail = $_POST['test_email'] ?? '';

        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="error">Please enter a valid email address</div>';
        } else {
            try {
                $emailService = new EmailService();

                switch ($testType) {
                    case 'simple':
                        $result = $emailService->send(
                            $testEmail,
                            'Test Email from Athletes Gym',
                            '<h1>Test Email</h1><p>If you received this, Resend is working! 🎉</p>',
                            'Test Email - If you received this, Resend is working!'
                        );
                        echo '<div class="success">✅ Simple test email sent successfully!</div>';
                        break;

                    case 'otp':
                        $result = $emailService->sendOTP($testEmail, '123456', 'Test User');
                        echo '<div class="success">✅ OTP email sent successfully!</div>';
                        break;

                    case 'order':
                        $result = $emailService->sendOrderConfirmation($testEmail, [
                            'order_id' => '12345',
                            'customer_name' => 'Test User',
                            'total' => '500.00',
                            'items' => [
                                ['name' => 'Test Product 1', 'quantity' => 2, 'price' => '100.00'],
                                ['name' => 'Test Product 2', 'quantity' => 1, 'price' => '300.00']
                            ]
                        ]);
                        echo '<div class="success">✅ Order confirmation email sent successfully!</div>';
                        break;

                    case 'contact':
                        $result = $emailService->sendContactForm([
                            'name' => 'Test Customer',
                            'email' => $testEmail,
                            'phone' => '+974 1234 5678',
                            'message' => 'This is a test contact form submission.'
                        ]);
                        echo '<div class="success">✅ Contact form email sent successfully!</div>';
                        break;
                }

                if (isset($result)) {
                    echo '<h3>Response from Resend:</h3>';
                    echo '<pre>' . print_r($result, true) . '</pre>';
                }

            } catch (Exception $e) {
                echo '<div class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }
    ?>

    <h2>Test Email Sending</h2>
    <form method="POST">
        <div style="margin: 20px 0;">
            <label><strong>Your Email Address:</strong></label><br>
            <input type="email" name="test_email" required
                   style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px;"
                   placeholder="Enter your email to receive test">
        </div>

        <h3>Select Test Type:</h3>

        <button type="submit" name="test_type" value="simple" class="test-btn">
            📧 Simple Email Test
        </button>
        <button type="submit" name="test_type" value="otp" class="test-btn">
            🔐 OTP Email Test
        </button>
        <button type="submit" name="test_type" value="order" class="test-btn">
            🛒 Order Confirmation Test
        </button>
        <button type="submit" name="test_type" value="contact" class="test-btn">
            📬 Contact Form Test
        </button>
    </form>

    <hr style="margin: 40px 0;">

    <h2>📝 Instructions</h2>
    <ol>
        <li>Enter your email address (the one you used for Resend)</li>
        <li>Click any test button to send a test email</li>
        <li>Check your inbox for the email</li>
        <li>If you don't see it, check spam folder</li>
        <li><strong style="color: red;">Delete this file (test_email.php) when done testing!</strong></li>
    </ol>

    <h2>⚠️ Troubleshooting</h2>
    <ul>
        <li>Make sure your <code>.env</code> file has the correct <code>RESEND_API_KEY</code></li>
        <li>For production, verify your domain in Resend dashboard</li>
        <li>Check error logs if emails fail</li>
    </ul>
</body>
</html>
