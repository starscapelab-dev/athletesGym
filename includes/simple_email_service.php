<?php
/**
 * Simple Email Service for Hostinger
 * Uses PHP mail() function - No email account required
 * Works like WordPress email system
 */

class SimpleEmailService {
    private $fromEmail;
    private $fromName;
    private $adminEmail;

    public function __construct() {
        // These will be sent "from" your domain automatically
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@athletesgym.qa');
        $this->fromName = env('MAIL_FROM_NAME', 'Athletes Gym Qatar');
        $this->adminEmail = env('MAIL_ADMIN_ADDRESS', 'info@athletesgym.qa');
    }

    /**
     * Send email using PHP mail() function (like WordPress)
     */
    public function send($to, $subject, $message, $headers = []) {
        // Build headers
        $headerString = $this->buildHeaders($headers);

        // Send email
        $success = @mail($to, $subject, $message, $headerString);

        if (!$success) {
            error_log("Failed to send email to: {$to} - Subject: {$subject}");
        }

        return $success;
    }

    /**
     * Build email headers
     */
    private function buildHeaders($customHeaders = []) {
        $headers = [];

        // From header
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";

        // Reply-To
        if (isset($customHeaders['Reply-To'])) {
            $headers[] = "Reply-To: {$customHeaders['Reply-To']}";
        }

        // Content type
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "MIME-Version: 1.0";

        return implode("\r\n", $headers);
    }

    /**
     * Send booking notification to admin
     */
    public function sendBookingNotification($bookingData) {
        $subject = "New Booking Request - {$bookingData['name']}";

        $message = $this->getBookingTemplate($bookingData);

        $headers = [
            'Reply-To' => $bookingData['email']
        ];

        return $this->send($this->adminEmail, $subject, $message, $headers);
    }

    /**
     * Send contact form to admin
     */
    public function sendContactForm($formData) {
        $subject = "New Contact Form - {$formData['name']}";

        $message = $this->getContactTemplate($formData);

        $headers = [
            'Reply-To' => $formData['email']
        ];

        return $this->send($this->adminEmail, $subject, $message, $headers);
    }

    /**
     * Send order confirmation to customer
     */
    public function sendOrderConfirmation($orderData) {
        $subject = "Order Confirmation #{$orderData['order_id']} - Athletes Gym";

        $message = $this->getOrderTemplate($orderData);

        return $this->send($orderData['customer_email'], $subject, $message);
    }

    /**
     * Send order notification to admin
     */
    public function sendOrderNotificationToAdmin($orderData) {
        $subject = "New Order #{$orderData['order_id']} - {$orderData['customer_name']}";

        $message = $this->getOrderAdminTemplate($orderData);

        $headers = [
            'Reply-To' => $orderData['customer_email'] ?? $this->fromEmail
        ];

        return $this->send($this->adminEmail, $subject, $message, $headers);
    }

    /**
     * Send password reset code
     */
    public function sendPasswordReset($email, $code, $userName = 'User') {
        $subject = "Password Reset Code - Athletes Gym";

        $message = $this->getPasswordResetTemplate($code, $userName);

        return $this->send($email, $subject, $message);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($email, $userName) {
        $subject = "Welcome to Athletes Gym Qatar!";

        $message = $this->getWelcomeTemplate($userName);

        return $this->send($email, $subject, $message);
    }

    /**
     * Booking notification template
     */
    private function getBookingTemplate($data) {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $phone = htmlspecialchars($data['phone'] ?? 'Not provided');
        $service = htmlspecialchars($data['service'] ?? 'Not specified');
        $date = htmlspecialchars($data['date'] ?? 'Not specified');
        $time = htmlspecialchars($data['time'] ?? 'Not specified');
        $message = nl2br(htmlspecialchars($data['message'] ?? 'No message'));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 3px solid #000; }
        .label { font-weight: bold; color: #000; }
        .value { margin-top: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🏋️ New Booking Request</h2>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{$name}</div>
            </div>
            <div class="field">
                <div class="label">Email:</div>
                <div class="value"><a href="mailto:{$email}">{$email}</a></div>
            </div>
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{$phone}</div>
            </div>
            <div class="field">
                <div class="label">Service:</div>
                <div class="value">{$service}</div>
            </div>
            <div class="field">
                <div class="label">Preferred Date:</div>
                <div class="value">{$date}</div>
            </div>
            <div class="field">
                <div class="label">Preferred Time:</div>
                <div class="value">{$time}</div>
            </div>
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{$message}</div>
            </div>
        </div>
        <div class="footer">
            <p>Athletes Gym Qatar - Booking System</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Contact form template
     */
    private function getContactTemplate($data) {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $phone = htmlspecialchars($data['phone'] ?? 'Not provided');
        $message = nl2br(htmlspecialchars($data['message']));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 3px solid #000; }
        .label { font-weight: bold; color: #000; }
        .value { margin-top: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📧 New Contact Form Submission</h2>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{$name}</div>
            </div>
            <div class="field">
                <div class="label">Email:</div>
                <div class="value"><a href="mailto:{$email}">{$email}</a></div>
            </div>
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{$phone}</div>
            </div>
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{$message}</div>
            </div>
        </div>
        <div class="footer">
            <p>Athletes Gym Qatar - Contact Form</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Order confirmation template
     */
    private function getOrderTemplate($data) {
        $orderId = $data['order_id'];
        $customerName = htmlspecialchars($data['customer_name']);
        $total = $data['total'];
        $items = $data['items'] ?? [];

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "<tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['name']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>{$item['price']} QR</td>
            </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
        th { background: #000; color: white; padding: 10px; text-align: left; }
        .total { font-weight: bold; font-size: 18px; background: #f0f0f0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 Order Confirmed!</h2>
            <p>Order #{$orderId}</p>
        </div>
        <div class="content">
            <p>Hi <strong>{$customerName}</strong>,</p>
            <p>Thank you for your order! We're preparing your items for shipment.</p>

            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                    <tr class="total">
                        <td colspan="2" style="padding: 15px; text-align: right;">Total:</td>
                        <td style="padding: 15px; text-align: right;">{$total} QR</td>
                    </tr>
                </tbody>
            </table>

            <p>You'll receive a shipping confirmation once your order is on its way.</p>
            <p>Questions? Contact us at <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>
        </div>
        <div class="footer">
            <p>Athletes Gym Qatar - Your Fitness Journey Starts Here</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Order notification template for admin
     */
    private function getOrderAdminTemplate($data) {
        $orderId = $data['order_id'];
        $customerName = htmlspecialchars($data['customer_name']);
        $customerEmail = htmlspecialchars($data['customer_email'] ?? 'Not provided');
        $customerPhone = htmlspecialchars($data['customer_phone'] ?? 'Not provided');
        $customerAddress = htmlspecialchars($data['customer_address'] ?? 'Not provided');
        $total = $data['total'];
        $items = $data['items'] ?? [];
        $orderDate = $data['order_date'] ?? date('Y-m-d H:i:s');

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "<tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['name']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>{$item['price']} QR</td>
            </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 3px solid #000; }
        .label { font-weight: bold; color: #000; }
        .value { margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
        th { background: #000; color: white; padding: 10px; text-align: left; }
        .total { font-weight: bold; font-size: 18px; background: #f0f0f0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🛒 New Order Received!</h2>
            <p>Order #{$orderId}</p>
        </div>
        <div class="content">
            <h3>Customer Information</h3>
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{$customerName}</div>
            </div>
            <div class="field">
                <div class="label">Email:</div>
                <div class="value"><a href="mailto:{$customerEmail}">{$customerEmail}</a></div>
            </div>
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{$customerPhone}</div>
            </div>
            <div class="field">
                <div class="label">Address:</div>
                <div class="value">{$customerAddress}</div>
            </div>
            <div class="field">
                <div class="label">Order Date:</div>
                <div class="value">{$orderDate}</div>
            </div>

            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                    <tr class="total">
                        <td colspan="2" style="padding: 15px; text-align: right;">Total:</td>
                        <td style="padding: 15px; text-align: right;">{$total} QR</td>
                    </tr>
                </tbody>
            </table>

            <p><strong>Action Required:</strong> Please process this order and contact the customer if needed.</p>
        </div>
        <div class="footer">
            <p>Athletes Gym Qatar - Order Management System</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Password reset template
     */
    private function getPasswordResetTemplate($code, $userName) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .code-box { background: white; border: 2px solid #000; padding: 20px; text-align: center; margin: 20px 0; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #000; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔐 Password Reset Request</h2>
        </div>
        <div class="content">
            <p>Hi <strong>{$userName}</strong>,</p>
            <p>We received a request to reset your password. Use the code below:</p>

            <div class="code-box">
                <div class="code">{$code}</div>
                <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">⏱️ Expires in 10 minutes</p>
            </div>

            <p>If you didn't request this, please ignore this email.</p>
            <p>For security concerns, contact us at <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>
        </div>
        <div class="footer">
            <p>Athletes Gym Qatar</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Welcome email template
     */
    private function getWelcomeTemplate($userName) {
        $siteUrl = env('APP_URL', 'https://athletesgym.qa');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .cta-button { display: inline-block; background: #000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>💪 Welcome to Athletes Gym!</h2>
        </div>
        <div class="content">
            <p>Hi <strong>{$userName}</strong>,</p>
            <p>Welcome to Athletes Gym Qatar! We're excited to have you join our fitness community.</p>
            <p>Your account is ready. You can now browse our exclusive products and place orders online.</p>

            <div style="text-align: center;">
                <a href="{$siteUrl}" class="cta-button">Start Shopping</a>
            </div>

            <p>If you have any questions, feel free to reach out to us at <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>
            <p>Best regards,<br><strong>Athletes Gym Qatar Team</strong></p>
        </div>
        <div class="footer">
            <p>Athletes Gym Qatar - Your Fitness Journey Starts Here</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}

/**
 * Helper function - Quick send email
 */
function sendSimpleEmail($to, $subject, $message) {
    $emailService = new SimpleEmailService();
    return $emailService->send($to, $subject, $message);
}

/**
 * Helper function - Send booking notification
 */
function sendBookingNotification($bookingData) {
    $emailService = new SimpleEmailService();
    return $emailService->sendBookingNotification($bookingData);
}

/**
 * Helper function - Send contact form
 */
function sendContactNotification($formData) {
    $emailService = new SimpleEmailService();
    return $emailService->sendContactForm($formData);
}

/**
 * Helper function - Send order notification to admin
 */
function sendOrderNotificationToAdmin($orderData) {
    $emailService = new SimpleEmailService();
    return $emailService->sendOrderNotificationToAdmin($orderData);
}
