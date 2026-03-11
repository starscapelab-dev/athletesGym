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
    private $bccEmail;
    private $siteUrl;
    private $logoUrl;

    public function __construct() {
        // These will be sent "from" your domain automatically
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@athletesgym.qa');
        $this->fromName = env('MAIL_FROM_NAME', 'Athletes Gym Qatar');
        $this->adminEmail = env('MAIL_ADMIN_ADDRESS', 'athletesgymqa@gmail.com');
        $this->bccEmail = env('MAIL_BCC_ADDRESS', 'info@akshayvt.com');
        $this->siteUrl = env('APP_URL', 'https://athletesgym.qa');
        $this->logoUrl = $this->siteUrl . '/assets/images/logo/logo-white.png';
    }

    /**
     * Get base email styles (consistent branding)
     */
    private function getEmailStyles() {
        return "
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

            body {
                font-family: 'Inter', Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .email-wrapper {
                max-width: 650px;
                margin: 0 auto;
                background-color: #ffffff;
            }
            .email-header {
                background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
                padding: 30px 20px;
                text-align: center;
            }
            .email-logo {
                max-width: 180px;
                height: auto;
                margin-bottom: 10px;
            }
            .email-title {
                font-family: 'Orbitron', 'Arial Black', sans-serif;
                color: #ffffff;
                font-size: 24px;
                font-weight: 700;
                margin: 15px 0 5px 0;
                letter-spacing: 0.5px;
            }
            .email-subtitle {
                color: #cccccc;
                font-size: 14px;
                margin: 5px 0 0 0;
            }
            .email-content {
                padding: 30px 25px;
                background-color: #ffffff;
            }
            .content-section {
                margin-bottom: 25px;
            }
            .section-title {
                font-family: 'Orbitron', 'Arial Black', sans-serif;
                color: #000000;
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 2px solid #f0f0f0;
            }
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin: 15px 0;
            }
            .info-box {
                background-color: #f9f9f9;
                padding: 15px;
                border-left: 3px solid #000000;
            }
            .info-label {
                font-weight: 600;
                color: #000000;
                font-size: 13px;
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .info-value {
                color: #555555;
                font-size: 14px;
                line-height: 1.5;
            }
            .info-value a {
                color: #000000;
                text-decoration: none;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                background-color: #ffffff;
            }
            th {
                background-color: #000000;
                color: #ffffff;
                padding: 12px;
                text-align: left;
                font-weight: 600;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            td {
                padding: 12px;
                border-bottom: 1px solid #e0e0e0;
                font-size: 14px;
                color: #333333;
            }
            .total-row {
                background-color: #f9f9f9;
                font-weight: 700;
                font-size: 16px;
            }
            .total-row td {
                border-bottom: none;
                padding: 15px 12px;
            }
            .alert-box {
                background-color: #f9f9f9;
                border-left: 4px solid #000000;
                padding: 15px;
                margin: 20px 0;
            }
            .status-badge {
                display: inline-block;
                padding: 6px 16px;
                background-color: #000000;
                color: #ffffff;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn {
                display: inline-block;
                padding: 12px 30px;
                background-color: #000000;
                color: #ffffff;
                text-decoration: none;
                border-radius: 4px;
                font-weight: 600;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .email-footer {
                background-color: #f5f5f5;
                padding: 25px 20px;
                text-align: center;
                border-top: 1px solid #e0e0e0;
            }
            .footer-text {
                color: #666666;
                font-size: 13px;
                line-height: 1.6;
                margin: 5px 0;
            }
            .footer-links {
                margin: 15px 0;
            }
            .footer-links a {
                color: #000000;
                text-decoration: none;
                margin: 0 10px;
                font-size: 13px;
                font-weight: 500;
            }
            @media only screen and (max-width: 600px) {
                .info-grid {
                    grid-template-columns: 1fr;
                }
                .email-content {
                    padding: 20px 15px;
                }
            }
        </style>";
    }

    /**
     * Get email header HTML
     */
    private function getEmailHeader($title, $subtitle = '') {
        $subtitleHtml = $subtitle ? "<p class='email-subtitle'>{$subtitle}</p>" : '';
        return "
        <div class='email-header'>
            <img src='{$this->logoUrl}' alt='Athletes Gym Qatar' class='email-logo'>
            <h1 class='email-title'>{$title}</h1>
            {$subtitleHtml}
        </div>";
    }

    /**
     * Get email footer HTML
     */
    private function getEmailFooter() {
        return "
        <div class='email-footer'>
            <p class='footer-text'><strong>Athletes Gym Qatar</strong></p>
            <p class='footer-text'>Your Fitness Journey Starts Here</p>
            <div class='footer-links'>
                <a href='{$this->siteUrl}'>Visit Website</a> |
                <a href='mailto:info@athletesgym.qa'>Contact Us</a> |
                <a href='tel:+97439992247'>+974 3999 2247</a>
            </div>
            <p class='footer-text' style='margin-top: 15px; font-size: 12px; color: #999999;'>
                © " . date('Y') . " Athletes Gym Qatar. All rights reserved.
            </p>
        </div>";
    }

    /**
     * Send email using PHP mail() function (like WordPress)
     */
    public function send($to, $subject, $message, $headers = []) {
        // Build headers
        $headerString = $this->buildHeaders($headers);

        error_log("=== 📧 SENDING EMAIL ===");
        error_log("To: {$to}");
        error_log("Subject: {$subject}");
        error_log("From: {$this->fromEmail}");
        error_log("Environment: " . env('APP_ENV'));
        
        // Send email
        $success = @mail($to, $subject, $message, $headerString);

        if (!$success) {
            error_log("❌ MAIL FUNCTION FAILED - To: {$to}, Subject: {$subject}, From: {$this->fromEmail}");
        } else {
            error_log("✅ MAIL FUNCTION SUCCESS - To: {$to}");
        }
        error_log("=== === ===");

        return $success;
    }

    /**
     * Build email headers
     */
    private function buildHeaders($customHeaders = []) {
        $headers = [];

        // From header
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";

        // Return-Path header (important for deliverability)
        $headers[] = "Return-Path: <{$this->fromEmail}>";

        // Reply-To
        if (isset($customHeaders['Reply-To'])) {
            $headers[] = "Reply-To: {$customHeaders['Reply-To']}";
        }

        // BCC for admin emails
        if (isset($customHeaders['BCC']) && $customHeaders['BCC'] === true) {
            $headers[] = "BCC: {$this->bccEmail}";
        }

        // Content type
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "MIME-Version: 1.0";
        
        // Additional headers for better deliverability
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "X-Priority: 3";

        return implode("\r\n", $headers);
    }

    /**
     * Send booking notification to admin
     */
    public function sendBookingNotification($bookingData) {
        $subject = "New Booking Request - {$bookingData['name']}";

        $message = $this->getBookingTemplate($bookingData);

        $headers = [
            'Reply-To' => $bookingData['email'],
            'BCC' => true
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
            'Reply-To' => $formData['email'],
            'BCC' => true
        ];

        return $this->send($this->adminEmail, $subject, $message, $headers);
    }

    /**
     * Send order confirmation to customer
     */
    public function sendOrderConfirmation($orderData) {
        $subject = "Order Confirmation #{$orderData['order_id']} - Athletes Gym";
        $to = $orderData['customer_email'];

        error_log("📧 Preparing order confirmation email to: {$to}");
        
        try {
            $message = $this->getOrderTemplate($orderData);
            error_log("📧 Order template generated, message length: " . strlen($message));
        } catch (Exception $e) {
            error_log("❌ Error generating order template: " . $e->getMessage());
            return false;
        }

        $result = $this->send($to, $subject, $message);
        error_log("📧 Order confirmation send result: " . ($result ? "SUCCESS" : "FAILED"));
        
        return $result;
    }

    /**
     * Send order notification to admin
     */
    public function sendOrderNotificationToAdmin($orderData) {
        $subject = "New Order #{$orderData['order_id']} - {$orderData['customer_name']}";

        $message = $this->getOrderAdminTemplate($orderData);

        $headers = [
            'Reply-To' => $orderData['customer_email'] ?? $this->fromEmail,
            'BCC' => true
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
     * Send OTP verification code
     */
    public function sendOTP($email, $otp, $userName = 'User') {
        $subject = "Your Password Reset Code - Athletes Gym";

        $message = $this->getOTPTemplate($otp, $userName);

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

        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('New Booking Request', 'Booking System Notification');
        $footer = $this->getEmailFooter();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <p>A new booking request has been submitted:</p>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Customer Name</div>
                    <div class="info-value">{$name}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><a href="mailto:{$email}">{$email}</a></div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{$phone}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Service Requested</div>
                    <div class="info-value">{$service}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Preferred Date</div>
                    <div class="info-value">{$date}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Preferred Time</div>
                    <div class="info-value">{$time}</div>
                </div>
            </div>

            <div class="alert-box">
                <div class="info-label">Customer Message</div>
                <div class="info-value">{$message}</div>
            </div>

            <p style="margin-top: 20px;"><strong>Action Required:</strong> Please contact the customer to confirm their booking.</p>
        </div>
        {$footer}
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

        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('New Contact Form', 'Website Inquiry');
        $footer = $this->getEmailFooter();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <p>A new contact form has been submitted from your website:</p>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Name</div>
                    <div class="info-value">{$name}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><a href="mailto:{$email}">{$email}</a></div>
                </div>
            </div>

            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-box">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{$phone}</div>
                </div>
            </div>

            <div class="alert-box">
                <div class="info-label">Message</div>
                <div class="info-value">{$message}</div>
            </div>

            <p style="margin-top: 20px;"><strong>Action Required:</strong> Please respond to this inquiry promptly.</p>
        </div>
        {$footer}
    </div>
</body>
</html>
HTML;
    }

    /**
     * Order confirmation template for customer
     */
    private function getOrderTemplate($data) {
        $orderId = $data['order_id'];
        $customerName = htmlspecialchars($data['customer_name']);
        $customerEmail = htmlspecialchars($data['customer_email'] ?? '');
        $customerPhone = htmlspecialchars($data['customer_phone'] ?? '');
        $shippingAddress = nl2br(htmlspecialchars($data['shipping_address'] ?? $data['customer_address'] ?? ''));
        $billingAddress = nl2br(htmlspecialchars($data['billing_address'] ?? $data['shipping_address'] ?? $data['customer_address'] ?? ''));
        $orderDate = htmlspecialchars($data['order_date'] ?? date('Y-m-d H:i:s'));
        $paymentMethod = htmlspecialchars($data['payment_method'] ?? 'Cash on Delivery');
        $orderStatus = htmlspecialchars($data['order_status'] ?? 'Processing');

        $subtotal = floatval($data['subtotal'] ?? $data['total'] ?? 0);
        $shippingFee = floatval($data['shipping_fee'] ?? 0);
        $tax = floatval($data['tax'] ?? 0);
        $discount = floatval($data['discount'] ?? 0);
        $total = floatval($data['total'] ?? 0);

        $items = $data['items'] ?? [];

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemName = htmlspecialchars($item['name']);
            $itemSKU = isset($item['sku']) ? '<br><small style="color: #888;">SKU: ' . htmlspecialchars($item['sku']) . '</small>' : '';
            $itemSize = isset($item['size']) ? '<br><small style="color: #888;">Size: ' . htmlspecialchars($item['size']) . '</small>' : '';
            $itemColor = isset($item['color']) ? '<br><small style="color: #888;">Color: ' . htmlspecialchars($item['color']) . '</small>' : '';
            $itemQty = intval($item['quantity']);
            $itemPrice = number_format(floatval($item['price']), 2);
            $itemTotal = number_format(floatval($item['price']) * $itemQty, 2);

            $imageHtml = '';
            // Add first product image if available
            if (isset($item['images']) && is_array($item['images']) && !empty($item['images'])) {
                $imagePath = $item['images'][0];
                // Images are stored in /uploads/ directory
                $imageUrl = htmlspecialchars($this->siteUrl . '/uploads/' . $imagePath);
                $imageHtml = "<img src='{$imageUrl}' alt='{$itemName}' style='max-width: 100px; max-height: 100px; border-radius: 4px; margin-right: 15px; display: inline-block; vertical-align: top;' />";
            } elseif (isset($item['image']) && !empty($item['image'])) {
                // Fallback to single image field if provided
                $imageUrl = htmlspecialchars($this->siteUrl . '/uploads/' . $item['image']);
                $imageHtml = "<img src='{$imageUrl}' alt='{$itemName}' style='max-width: 100px; max-height: 100px; border-radius: 4px; margin-right: 15px; display: inline-block; vertical-align: top;' />";
            }

            $itemsHtml .= "<tr>
                <td style='padding: 10px; vertical-align: top;'>
                    {$imageHtml}
                    <div style='display: inline-block; vertical-align: top;'>
                        {$itemName}{$itemSKU}{$itemSize}{$itemColor}
                    </div>
                </td>
                <td style='text-align: center; padding: 10px;'>{$itemQty}</td>
                <td style='text-align: right; padding: 10px;'>{$itemPrice} QR</td>
                <td style='text-align: right; padding: 10px;'>{$itemTotal} QR</td>
            </tr>";
        }

        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('Order Confirmation', "Order #{$orderId}");
        $footer = $this->getEmailFooter();

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <div class="status-badge">{$orderStatus}</div>

            <p>Hello <strong>{$customerName}</strong>,</p>
            <p>Thank you for your order. We're preparing your items for delivery.</p>

            <h3 class="section-title">Order Information</h3>

            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-box">
                    <div class="info-label">Order Date</div>
                    <div class="info-value">{$orderDate}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{$paymentMethod}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Order Status</div>
                    <div class="info-value">{$orderStatus}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Shipping Address</div>
                    <div class="info-value">{$shippingAddress}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Billing Address</div>
                    <div class="info-value">{$billingAddress}</div>
                </div>
            </div>

            <h3 class="section-title">Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align: center; width: 80px;">Qty</th>
                        <th style="text-align: right; width: 100px;">Price</th>
                        <th style="text-align: right; width: 100px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
HTML;

        $html .= "<tr><td colspan='3' style='text-align: right; padding-top: 20px;'><strong>Subtotal:</strong></td>";
        $html .= "<td style='text-align: right; padding-top: 20px;'><strong>" . number_format($subtotal, 2) . " QR</strong></td></tr>";

        if ($shippingFee > 0) {
            $html .= "<tr><td colspan='3' style='text-align: right;'>Shipping Fee:</td>";
            $html .= "<td style='text-align: right;'>" . number_format($shippingFee, 2) . " QR</td></tr>";
        }

        if ($tax > 0) {
            $html .= "<tr><td colspan='3' style='text-align: right;'>Tax:</td>";
            $html .= "<td style='text-align: right;'>" . number_format($tax, 2) . " QR</td></tr>";
        }

        if ($discount > 0) {
            $html .= "<tr><td colspan='3' style='text-align: right; color: #28a745;'>Discount:</td>";
            $html .= "<td style='text-align: right; color: #28a745;'>-" . number_format($discount, 2) . " QR</td></tr>";
        }

        $html .= "<tr class='total-row'><td colspan='3' style='text-align: right;'>Total Amount:</td>";
        $html .= "<td style='text-align: right;'>" . number_format($total, 2) . " QR</td></tr>";

        $html .= "
                </tbody>
            </table>

            <div class='alert-box'>
                <h3 class='section-title' style='margin: 0 0 10px 0; border: none; font-size: 16px;'>What's Next?</h3>
                <ul style='margin: 0; padding-left: 20px; line-height: 1.8;'>
                    <li>You'll receive a shipping confirmation once your order is dispatched</li>
                    <li>Track your order status in your account dashboard</li>
                    <li>Estimated delivery: 2-5 business days</li>
                </ul>
            </div>

            <p style='margin-top: 25px;'>If you have any questions about your order, please contact us at <a href='mailto:info@athletesgym.qa'>info@athletesgym.qa</a></p>
        </div>
        {$footer}
    </div>
</body>
</html>";

        return $html;
    }

    /**
     * Order notification template for admin
     */
    private function getOrderAdminTemplate($data) {
        $orderId = $data['order_id'];
        $customerName = htmlspecialchars($data['customer_name']);
        $customerEmail = htmlspecialchars($data['customer_email'] ?? 'Not provided');
        $customerPhone = htmlspecialchars($data['customer_phone'] ?? 'Not provided');
        $shippingAddress = nl2br(htmlspecialchars($data['shipping_address'] ?? $data['customer_address'] ?? 'Not provided'));
        $billingAddress = nl2br(htmlspecialchars($data['billing_address'] ?? $data['shipping_address'] ?? $data['customer_address'] ?? 'Not provided'));
        $orderDate = htmlspecialchars($data['order_date'] ?? date('Y-m-d H:i:s'));
        $paymentMethod = htmlspecialchars($data['payment_method'] ?? 'Cash on Delivery');
        $orderStatus = htmlspecialchars($data['order_status'] ?? 'Pending');
        $orderNotes = nl2br(htmlspecialchars($data['order_notes'] ?? ''));

        $subtotal = floatval($data['subtotal'] ?? $data['total'] ?? 0);
        $shippingFee = floatval($data['shipping_fee'] ?? 0);
        $tax = floatval($data['tax'] ?? 0);
        $discount = floatval($data['discount'] ?? 0);
        $total = floatval($data['total'] ?? 0);

        $items = $data['items'] ?? [];

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemName = htmlspecialchars($item['name']);
            $itemSKU = isset($item['sku']) ? '<br><small style="color: #888;">SKU: ' . htmlspecialchars($item['sku']) . '</small>' : '';
            $itemSize = isset($item['size']) ? '<br><small style="color: #888;">Size: ' . htmlspecialchars($item['size']) . '</small>' : '';
            $itemColor = isset($item['color']) ? '<br><small style="color: #888;">Color: ' . htmlspecialchars($item['color']) . '</small>' : '';
            $itemCategory = isset($item['category']) ? '<br><small style="color: #888;">Category: ' . htmlspecialchars($item['category']) . '</small>' : '';
            $itemQty = intval($item['quantity']);
            $itemPrice = number_format(floatval($item['price']), 2);
            $itemTotal = number_format(floatval($item['price']) * $itemQty, 2);

            $imageHtml = '';
            // Add first product image if available
            if (isset($item['images']) && is_array($item['images']) && !empty($item['images'])) {
                $imagePath = $item['images'][0];
                // Images are stored in /uploads/ directory
                $imageUrl = htmlspecialchars($this->siteUrl . '/uploads/' . $imagePath);
                $imageHtml = "<img src='{$imageUrl}' alt='{$itemName}' style='max-width: 100px; max-height: 100px; border-radius: 4px; margin-right: 15px; display: inline-block; vertical-align: top;' />";
            } elseif (isset($item['image']) && !empty($item['image'])) {
                // Fallback to single image field if provided
                $imageUrl = htmlspecialchars($this->siteUrl . '/uploads/' . $item['image']);
                $imageHtml = "<img src='{$imageUrl}' alt='{$itemName}' style='max-width: 100px; max-height: 100px; border-radius: 4px; margin-right: 15px; display: inline-block; vertical-align: top;' />";
            }

            $itemsHtml .= "<tr>
                <td style='padding: 10px; vertical-align: top;'>
                    {$imageHtml}
                    <div style='display: inline-block; vertical-align: top;'>
                        {$itemName}{$itemSKU}{$itemSize}{$itemColor}{$itemCategory}
                    </div>
                </td>
                <td style='text-align: center; padding: 10px;'>{$itemQty}</td>
                <td style='text-align: right; padding: 10px;'>{$itemPrice} QR</td>
                <td style='text-align: right; padding: 10px;'>{$itemTotal} QR</td>
            </tr>";
        }

        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('New Order Received', "Order #{$orderId}");
        $footer = $this->getEmailFooter();

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <div class="status-badge">{$orderStatus}</div>

            <div class="alert-box">
                <p style="margin: 0;"><strong>Action Required:</strong> A new order has been placed. Please review and process this order promptly.</p>
            </div>

            <h3 class="section-title">Order Details</h3>

            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-box">
                    <div class="info-label">Order Date & Time</div>
                    <div class="info-value">{$orderDate}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{$paymentMethod}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Order Status</div>
                    <div class="info-value">{$orderStatus}</div>
                </div>
            </div>

            <h3 class="section-title">Customer Information</h3>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Name</div>
                    <div class="info-value">{$customerName}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Email</div>
                    <div class="info-value"><a href="mailto:{$customerEmail}">{$customerEmail}</a></div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Phone</div>
                    <div class="info-value"><a href="tel:{$customerPhone}">{$customerPhone}</a></div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Shipping Address</div>
                    <div class="info-value">{$shippingAddress}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Billing Address</div>
                    <div class="info-value">{$billingAddress}</div>
                </div>
            </div>
HTML;

        if (!empty($orderNotes)) {
            $html .= "
            <div class='info-grid' style='grid-template-columns: 1fr;'>
                <div class='info-box'>
                    <div class='info-label'>Order Notes</div>
                    <div class='info-value'>{$orderNotes}</div>
                </div>
            </div>";
        }

        $html .= "
            <h3 class='section-title'>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th style='text-align: center; width: 80px;'>Qty</th>
                        <th style='text-align: right; width: 100px;'>Unit Price</th>
                        <th style='text-align: right; width: 100px;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}";

        $html .= "<tr><td colspan='3' style='text-align: right; padding-top: 20px;'><strong>Subtotal:</strong></td>";
        $html .= "<td style='text-align: right; padding-top: 20px;'><strong>" . number_format($subtotal, 2) . " QR</strong></td></tr>";

        if ($shippingFee > 0) {
            $html .= "<tr><td colspan='3' style='text-align: right;'>Shipping Fee:</td>";
            $html .= "<td style='text-align: right;'>" . number_format($shippingFee, 2) . " QR</td></tr>";
        }

        if ($tax > 0) {
            $html .= "<tr><td colspan='3' style='text-align: right;'>Tax / VAT:</td>";
            $html .= "<td style='text-align: right;'>" . number_format($tax, 2) . " QR</td></tr>";
        }

        if ($discount > 0) {
            $html .= "<tr><td colspan='3' style='text-align: right; color: #28a745;'>Discount Applied:</td>";
            $html .= "<td style='text-align: right; color: #28a745;'>-" . number_format($discount, 2) . " QR</td></tr>";
        }

        $html .= "<tr class='total-row'><td colspan='3' style='text-align: right;'>Total Amount:</td>";
        $html .= "<td style='text-align: right;'>" . number_format($total, 2) . " QR</td></tr>";

        $html .= "
                </tbody>
            </table>

            <div class='alert-box'>
                <h3 class='section-title' style='margin: 0 0 10px 0; border: none; font-size: 16px;'>Next Steps</h3>
                <ul style='margin: 0; padding-left: 20px; line-height: 1.8;'>
                    <li>Verify order details and customer information</li>
                    <li>Check product availability and stock levels</li>
                    <li>Process payment (if applicable)</li>
                    <li>Prepare items for packaging and shipment</li>
                    <li>Update order status in admin panel</li>
                    <li>Send shipping confirmation to customer</li>
                </ul>
            </div>

            <p style='text-align: center; margin: 25px 0 0 0;'>
                <strong>Customer Contact:</strong><br>
                Email: <a href='mailto:{$customerEmail}'>{$customerEmail}</a><br>
                Phone: <a href='tel:{$customerPhone}'>{$customerPhone}</a>
            </p>
        </div>
        {$footer}
    </div>
</body>
</html>";

        return $html;
    }

    /**
     * Password reset template
     */
    /**
     * OTP verification code template
     */
    private function getOTPTemplate($otp, $userName) {
        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('Verify Your Identity', 'OTP Verification');
        $footer = $this->getEmailFooter();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
    <style>
        .code-box {
            background: #f9f9f9;
            border: 3px solid #000000;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-family: 'Courier New', monospace;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #000000;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <p>Hello <strong>{$userName}</strong>,</p>
            <p>We received a request to verify your identity. Please use the verification code below to proceed:</p>

            <div class="code-box">
                <div class="code">{$otp}</div>
                <p style="margin: 0; color: #666666; font-size: 13px;">This code expires in 10 minutes</p>
            </div>

            <div class="alert-box">
                <p style="margin: 0;"><strong>Security Notice:</strong> If you didn't request this verification, please ignore this email. For security concerns, contact us immediately.</p>
            </div>

            <p>For security assistance, please contact us at <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>
        </div>
        {$footer}
    </div>
</body>
</html>
HTML;
    }

    private function getPasswordResetTemplate($code, $userName) {
        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('Password Reset Request', 'Security Verification');
        $footer = $this->getEmailFooter();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
    <style>
        .code-box {
            background: #f9f9f9;
            border: 3px solid #000000;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-family: 'Courier New', monospace;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #000000;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <p>Hello <strong>{$userName}</strong>,</p>
            <p>We received a request to reset your password. Please use the verification code below to proceed:</p>

            <div class="code-box">
                <div class="code">{$code}</div>
                <p style="margin: 0; color: #666666; font-size: 13px;">This code expires in 10 minutes</p>
            </div>

            <div class="alert-box">
                <p style="margin: 0;"><strong>Security Notice:</strong> If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
            </div>

            <p>For security assistance, please contact us at <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>
        </div>
        {$footer}
    </div>
</body>
</html>
HTML;
    }

    /**
     * Welcome email template
     */
    private function getWelcomeTemplate($userName) {
        $styles = $this->getEmailStyles();
        $header = $this->getEmailHeader('Welcome to Athletes Gym', 'Your Account is Ready');
        $footer = $this->getEmailFooter();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {$styles}
</head>
<body>
    <div class="email-wrapper">
        {$header}
        <div class="email-content">
            <p>Hello <strong>{$userName}</strong>,</p>
            <p>Thank you for creating an account with Athletes Gym Qatar. We're thrilled to have you join our fitness community.</p>

            <div class="alert-box">
                <h3 class="section-title" style="margin: 0 0 10px 0; border: none;">Your Account is Active</h3>
                <p style="margin: 0;">You can now access all features including our exclusive product catalog, online ordering, and order tracking.</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{$this->siteUrl}" class="btn">Start Shopping</a>
            </div>

            <h3 class="section-title">What's Next?</h3>
            <ul style="line-height: 1.8;">
                <li>Browse our exclusive fitness products and apparel</li>
                <li>Place orders online with secure checkout</li>
                <li>Track your order status in real-time</li>
                <li>Manage your account and preferences</li>
            </ul>

            <p>If you have any questions or need assistance, our team is here to help. Contact us at <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>

            <p style="margin-top: 25px;">Best regards,<br><strong>Athletes Gym Qatar Team</strong></p>
        </div>
        {$footer}
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
