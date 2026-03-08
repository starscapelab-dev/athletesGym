<?php
/**
 * Resend Email Service
 * Simple implementation using cURL (no Composer required)
 */

class EmailService {
    private $apiKey;
    private $fromEmail;
    private $fromName;
    private $apiUrl = 'https://api.resend.com/emails';

    public function __construct() {
        $this->apiKey = env('RESEND_API_KEY');
        $this->fromEmail = env('RESEND_FROM_EMAIL', 'onboarding@resend.dev');
        $this->fromName = env('RESEND_FROM_NAME', 'Athletes Gym Qatar');

        if (empty($this->apiKey)) {
            throw new Exception('Resend API key not configured in .env file');
        }
    }

    /**
     * Send an email using Resend API
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $html HTML content
     * @param string|null $text Plain text content (optional)
     * @return array Response from Resend API
     */
    public function send($to, $subject, $html, $text = null) {
        $data = [
            'from' => "{$this->fromName} <{$this->fromEmail}>",
            'to' => [$to],
            'subject' => $subject,
            'html' => $html
        ];

        if ($text) {
            $data['text'] = $text;
        }

        return $this->makeRequest($data);
    }

    /**
     * Send OTP email
     */
    public function sendOTP($to, $otp, $userName = 'User') {
        $subject = 'Your Password Reset Code - Athletes Gym';

        $html = $this->getOTPTemplate($otp, $userName);
        $text = "Hi {$userName},\n\nYour password reset code is: {$otp}\n\nThis code will expire in 10 minutes.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nAthletes Gym Qatar";

        return $this->send($to, $subject, $html, $text);
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation($to, $orderData) {
        $subject = "Order Confirmation #{$orderData['order_id']} - Athletes Gym";

        $html = $this->getOrderConfirmationTemplate($orderData);
        $text = "Thank you for your order #{$orderData['order_id']}!\n\nOrder Total: {$orderData['total']} QR\n\nWe'll send you an update when your order ships.\n\nBest regards,\nAthletes Gym Qatar";

        return $this->send($to, $subject, $html, $text);
    }

    /**
     * Send contact form submission
     */
    public function sendContactForm($formData) {
        // Send to admin email (must be different from FROM email due to Resend restrictions)
        $adminEmail = env('RESEND_ADMIN_EMAIL', env('RESEND_FROM_EMAIL'));
        $subject = "New Contact Form Submission - {$formData['name']}";

        $html = $this->getContactFormTemplate($formData);

        return $this->send($adminEmail, $subject, $html);
    }

    /**
     * Make HTTP request to Resend API
     */
    private function makeRequest($data) {
        $ch = curl_init($this->apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception("Email sending failed: {$error}");
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $result['message'] ?? 'Unknown error';
            throw new Exception("Email API error: {$errorMsg}");
        }

        return $result;
    }

    /**
     * OTP Email Template
     */
    private function getOTPTemplate($otp, $userName) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f6f6f6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #000000;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .logo {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .content {
            background: #ffffff;
            padding: 40px 30px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .otp-box {
            background: linear-gradient(135deg, #f6f6f6 0%, #ffffff 100%);
            border: 2px solid #000000;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
        }
        .otp-label {
            margin: 0;
            font-size: 14px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #000000;
            letter-spacing: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        .otp-validity {
            margin: 15px 0 0 0;
            font-size: 13px;
            color: #999;
        }
        .footer {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            font-size: 13px;
            background: #f6f6f6;
            border-top: 1px solid #e7e7e7;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }
        .cta-button {
            display: inline-block;
            padding: 14px 30px;
            background: #000000;
            color: white !important;
            text-decoration: none;
            border-radius: 30px;
            margin: 20px 0;
            font-weight: 600;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏋️</div>
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <p>Hi <strong>{$userName}</strong>,</p>
            <p>We received a request to reset your password for your Athletes Gym account. Use the verification code below to proceed with resetting your password.</p>

            <div class="otp-box">
                <p class="otp-label">Your Verification Code</p>
                <div class="otp-code">{$otp}</div>
                <p class="otp-validity">⏱️ This code expires in 10 minutes</p>
            </div>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong> If you didn't request this password reset, please ignore this email. Your password will remain unchanged. For security concerns, contact our support team immediately.
            </div>

            <p style="margin-top: 30px; color: #666;">Need help? Contact us at <a href="mailto:support@athletesgym.qa" style="color: #000000; text-decoration: none; font-weight: 600;">support@athletesgym.qa</a></p>

            <p style="margin-top: 40px; font-size: 15px;">
                Best regards,<br>
                <strong style="color: #000000;">Athletes Gym Qatar Team</strong>
            </p>
        </div>
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Athletes Gym Qatar</strong></p>
            <p style="margin: 5px 0;">Your Fitness Journey Starts Here</p>
            <p style="margin: 15px 0 5px 0; font-size: 12px; color: #999;">&copy; 2026 Athletes Gym Qatar. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Order Confirmation Email Template
     */
    private function getOrderConfirmationTemplate($orderData) {
        $orderId = $orderData['order_id'];
        $customerName = $orderData['customer_name'];
        $total = $orderData['total'];
        $items = $orderData['items'] ?? [];

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "<tr>
                <td style='padding: 15px 10px; border-bottom: 1px solid #e7e7e7; font-size: 15px;'>{$item['name']}</td>
                <td style='padding: 15px 10px; border-bottom: 1px solid #e7e7e7; text-align: center; font-weight: 600;'>{$item['quantity']}</td>
                <td style='padding: 15px 10px; border-bottom: 1px solid #e7e7e7; text-align: right; font-weight: 600;'>{$item['price']} QR</td>
            </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f6f6f6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #000000;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 10px 0 5px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .order-number {
            background: rgba(255,255,255,0.1);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            margin-top: 10px;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .content {
            background: #ffffff;
            padding: 40px 30px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .success-badge {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 25px 0;
            font-size: 15px;
        }
        .order-box {
            background: linear-gradient(135deg, #f6f6f6 0%, #ffffff 100%);
            border: 1px solid #e7e7e7;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .order-box h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
            color: #000000;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        thead tr {
            background: #000000;
            color: white;
        }
        th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .total-row {
            background: #f6f6f6;
            font-weight: 700;
            font-size: 18px;
        }
        .total-row td {
            border-bottom: none !important;
            padding: 20px 10px !important;
        }
        .footer {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            font-size: 13px;
            background: #f6f6f6;
            border-top: 1px solid #e7e7e7;
        }
        .next-steps {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .next-steps h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        .next-steps p {
            margin: 5px 0;
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🎉</div>
            <h1>Order Confirmed!</h1>
            <div class="order-number">Order #{$orderId}</div>
        </div>
        <div class="content">
            <p>Hi <strong>{$customerName}</strong>,</p>

            <div class="success-badge">
                <strong>✓ Payment Successful!</strong><br>
                We've received your payment and are now preparing your order for shipment.
            </div>

            <div class="order-box">
                <h3>📦 Order Summary</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: left;">Item</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                        <tr class="total-row">
                            <td colspan="2" style="text-align: right;">Total Amount:</td>
                            <td style="text-align: right; color: #000000;">{$total} QR</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="next-steps">
                <h4>📬 What's Next?</h4>
                <p>• We're preparing your items for shipment</p>
                <p>• You'll receive a shipping confirmation email once your order ships</p>
                <p>• Track your order status in your account dashboard</p>
            </div>

            <p style="margin-top: 30px; color: #666;">Questions about your order? Contact us at <a href="mailto:support@athletesgym.qa" style="color: #000000; text-decoration: none; font-weight: 600;">support@athletesgym.qa</a></p>

            <p style="margin-top: 40px; font-size: 15px;">
                Thank you for choosing Athletes Gym!<br>
                <strong style="color: #000000;">Athletes Gym Qatar Team</strong>
            </p>
        </div>
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Athletes Gym Qatar</strong></p>
            <p style="margin: 5px 0;">Your Fitness Journey Starts Here</p>
            <p style="margin: 15px 0 5px 0; font-size: 12px; color: #999;">&copy; 2026 Athletes Gym Qatar. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Contact Form Email Template
     */
    private function getContactFormTemplate($formData) {
        $name = htmlspecialchars($formData['name']);
        $email = htmlspecialchars($formData['email']);
        $phone = htmlspecialchars($formData['phone'] ?? 'Not provided');
        $message = nl2br(htmlspecialchars($formData['message']));
        $date = date('F d, Y \a\t h:i A');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f6f6f6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #000000;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h2 {
            margin: 10px 0 5px 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .timestamp {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            margin-top: 5px;
        }
        .content {
            background: white;
            padding: 30px;
        }
        .field {
            background: #f6f6f6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #000000;
        }
        .label {
            font-weight: 600;
            color: #000000;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            font-size: 15px;
            color: #333;
        }
        .message-field {
            background: linear-gradient(135deg, #f6f6f6 0%, #ffffff 100%);
            border: 1px solid #e7e7e7;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .message-field .label {
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 13px;
            background: #f6f6f6;
            border-top: 1px solid #e7e7e7;
        }
        .reply-button {
            display: inline-block;
            padding: 12px 25px;
            background: #000000;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            margin: 15px 0;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">📬</div>
            <h2>New Contact Form Submission</h2>
            <div class="timestamp">{$date}</div>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">👤 Name</span>
                <div class="value">{$name}</div>
            </div>
            <div class="field">
                <span class="label">✉️ Email Address</span>
                <div class="value"><a href="mailto:{$email}" style="color: #000000; text-decoration: none; font-weight: 600;">{$email}</a></div>
            </div>
            <div class="field">
                <span class="label">📞 Phone Number</span>
                <div class="value">{$phone}</div>
            </div>

            <div class="message-field">
                <span class="label">💬 Message</span>
                <div class="value">{$message}</div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="mailto:{$email}" class="reply-button">Reply to Customer</a>
            </div>
        </div>
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Athletes Gym Qatar</strong></p>
            <p style="margin: 5px 0; font-size: 12px; color: #999;">Contact Form Notification System</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}

/**
 * Helper function to send emails easily
 */
function sendEmail($to, $subject, $html, $text = null) {
    try {
        $emailService = new EmailService();
        return $emailService->send($to, $subject, $html, $text);
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
