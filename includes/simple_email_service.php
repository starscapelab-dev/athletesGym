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
    private $siteUrl;
    private $logoUrl;

    public function __construct() {
        // These will be sent "from" your domain automatically
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@athletesgym.qa');
        $this->fromName = env('MAIL_FROM_NAME', 'Athletes Gym Qatar');
        $this->adminEmail = env('MAIL_ADMIN_ADDRESS', 'info@athletesgym.qa');
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
                <a href='tel:+974XXXXXXXX'>+974 XXXX XXXX</a>
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
            $itemSKU = isset($item['sku']) ? '<br><small style="color: #666;">SKU: ' . htmlspecialchars($item['sku']) . '</small>' : '';
            $itemSize = isset($item['size']) ? '<br><small style="color: #666;">Size: ' . htmlspecialchars($item['size']) . '</small>' : '';
            $itemColor = isset($item['color']) ? '<br><small style="color: #666;">Color: ' . htmlspecialchars($item['color']) . '</small>' : '';
            $itemQty = intval($item['quantity']);
            $itemPrice = number_format(floatval($item['price']), 2);
            $itemTotal = number_format(floatval($item['price']) * $itemQty, 2);

            $itemsHtml .= "<tr>
                <td style='padding: 12px; border-bottom: 1px solid #ddd;'>
                    {$itemName}{$itemSKU}{$itemSize}{$itemColor}
                </td>
                <td style='padding: 12px; border-bottom: 1px solid #ddd; text-align: center;'>{$itemQty}</td>
                <td style='padding: 12px; border-bottom: 1px solid #ddd; text-align: right;'>{$itemPrice} QR</td>
                <td style='padding: 12px; border-bottom: 1px solid #ddd; text-align: right;'>{$itemTotal} QR</td>
            </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 650px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: white; padding: 25px; text-align: center; }
        .content { background: #f9f9f9; padding: 25px; margin-top: 20px; }
        .info-section { background: white; padding: 15px; margin: 15px 0; border-left: 3px solid #000; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 15px 0; }
        .info-box { background: white; padding: 15px; border-left: 3px solid #000; }
        .info-label { font-weight: bold; color: #000; margin-bottom: 5px; }
        .info-value { color: #555; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
        th { background: #000; color: white; padding: 12px; text-align: left; font-weight: bold; }
        .subtotal-row td { padding: 10px 12px; border-bottom: 1px solid #ddd; text-align: right; }
        .total-row { font-weight: bold; font-size: 18px; background: #f0f0f0; }
        .total-row td { padding: 15px 12px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .status-badge { display: inline-block; padding: 5px 15px; background: #28a745; color: white; border-radius: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 Order Confirmed!</h2>
            <p style="margin: 10px 0 5px 0;">Order #{$orderId}</p>
            <span class="status-badge">{$orderStatus}</span>
        </div>
        <div class="content">
            <p>Hi <strong>{$customerName}</strong>,</p>
            <p>Thank you for your order! We're preparing your items for delivery.</p>

            <div class="info-section">
                <div class="info-label">Order Date:</div>
                <div class="info-value">{$orderDate}</div>
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

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Contact</div>
                    <div class="info-value">
                        {$customerPhone}<br>
                        {$customerEmail}
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{$paymentMethod}</div>
                </div>
            </div>

            <h3 style="margin-top: 25px;">Order Items</h3>
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
                    <tr class="subtotal-row">
                        <td colspan="3"><strong>Subtotal:</strong></td>
                        <td><strong>" . number_format($subtotal, 2) . " QR</strong></td>
                    </tr>";

        $html = $return;

        if ($shippingFee > 0) {
            $html .= "<tr class='subtotal-row'>
                        <td colspan='3'>Shipping Fee:</td>
                        <td>" . number_format($shippingFee, 2) . " QR</td>
                    </tr>";
        }

        if ($tax > 0) {
            $html .= "<tr class='subtotal-row'>
                        <td colspan='3'>Tax:</td>
                        <td>" . number_format($tax, 2) . " QR</td>
                    </tr>";
        }

        if ($discount > 0) {
            $html .= "<tr class='subtotal-row' style='color: #28a745;'>
                        <td colspan='3'>Discount:</td>
                        <td>-" . number_format($discount, 2) . " QR</td>
                    </tr>";
        }

        $html .= "<tr class='total-row'>
                        <td colspan='3' style='text-align: right;'>Total:</td>
                        <td style='text-align: right;'>" . number_format($total, 2) . " QR</td>
                    </tr>
                </tbody>
            </table>

            <p><strong>What's Next?</strong></p>
            <ul>
                <li>You'll receive a shipping confirmation once your order is dispatched</li>
                <li>Track your order status in your account dashboard</li>
                <li>Estimated delivery: 2-5 business days</li>
            </ul>

            <p>Questions? Contact us at <a href='mailto:info@athletesgym.qa'>info@athletesgym.qa</a> or call +974 XXXX XXXX</p>
        </div>
        <div class='footer'>
            <p>Athletes Gym Qatar - Your Fitness Journey Starts Here</p>
            <p><a href='" . env('APP_URL', 'https://athletesgym.qa') . "'>Visit Our Website</a></p>
        </div>
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
            $itemSKU = isset($item['sku']) ? '<br><small style="color: #666;">SKU: ' . htmlspecialchars($item['sku']) . '</small>' : '';
            $itemSize = isset($item['size']) ? '<br><small style="color: #666;">Size: ' . htmlspecialchars($item['size']) . '</small>' : '';
            $itemColor = isset($item['color']) ? '<br><small style="color: #666;">Color: ' . htmlspecialchars($item['color']) . '</small>' : '';
            $itemCategory = isset($item['category']) ? '<br><small style="color: #666;">Category: ' . htmlspecialchars($item['category']) . '</small>' : '';
            $itemQty = intval($item['quantity']);
            $itemPrice = number_format(floatval($item['price']), 2);
            $itemTotal = number_format(floatval($item['price']) * $itemQty, 2);

            $itemsHtml .= "<tr>
                <td style='padding: 12px; border-bottom: 1px solid #ddd;'>
                    {$itemName}{$itemSKU}{$itemSize}{$itemColor}{$itemCategory}
                </td>
                <td style='padding: 12px; border-bottom: 1px solid #ddd; text-align: center;'>{$itemQty}</td>
                <td style='padding: 12px; border-bottom: 1px solid #ddd; text-align: right;'>{$itemPrice} QR</td>
                <td style='padding: 12px; border-bottom: 1px solid #ddd; text-align: right;'>{$itemTotal} QR</td>
            </tr>";
        }

        $return = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 25px; text-align: center; }
        .content { background: #f9f9f9; padding: 25px; margin-top: 20px; }
        .alert { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0; }
        .info-box { background: white; padding: 15px; border-left: 3px solid #dc3545; }
        .info-label { font-weight: bold; color: #dc3545; margin-bottom: 8px; font-size: 14px; }
        .info-value { color: #333; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
        th { background: #dc3545; color: white; padding: 12px; text-align: left; font-weight: bold; font-size: 14px; }
        .subtotal-row td { padding: 10px 12px; border-bottom: 1px solid #ddd; text-align: right; font-size: 14px; }
        .total-row { font-weight: bold; font-size: 18px; background: #fff3cd; }
        .total-row td { padding: 15px 12px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .status-badge { display: inline-block; padding: 5px 15px; background: #ffc107; color: #000; border-radius: 15px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🛒 NEW ORDER RECEIVED!</h2>
            <p style="margin: 10px 0 5px 0; font-size: 20px;">Order #{$orderId}</p>
            <span class="status-badge">{$orderStatus}</span>
        </div>

        <div class="alert">
            <strong>⚠️ Action Required:</strong> A new order has been placed. Please review and process this order promptly.
        </div>

        <div class="content">
            <h3 style="color: #dc3545; margin-bottom: 15px;">📋 Order Details</h3>

            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-box">
                    <div class="info-label">📅 Order Date & Time</div>
                    <div class="info-value">{$orderDate}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">💳 Payment Method</div>
                    <div class="info-value">{$paymentMethod}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">📊 Order Status</div>
                    <div class="info-value">{$orderStatus}</div>
                </div>
            </div>

            <h3 style="color: #dc3545; margin: 25px 0 15px 0;">👤 Customer Information</h3>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Name</div>
                    <div class="info-value">{$customerName}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">📧 Email</div>
                    <div class="info-value"><a href="mailto:{$customerEmail}">{$customerEmail}</a></div>
                </div>
            </div>

            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-box">
                    <div class="info-label">📱 Phone</div>
                    <div class="info-value"><a href="tel:{$customerPhone}">{$customerPhone}</a></div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">📦 Shipping Address</div>
                    <div class="info-value">{$shippingAddress}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">💰 Billing Address</div>
                    <div class="info-value">{$billingAddress}</div>
                </div>
            </div>
HTML;

        if (!empty($orderNotes)) {
            $return .= "
            <div class=\"info-grid\" style=\"grid-template-columns: 1fr;\">
                <div class=\"info-box\">
                    <div class=\"info-label\">📝 Order Notes</div>
                    <div class=\"info-value\">{$orderNotes}</div>
                </div>
            </div>";
        }

        $return .= "
            <h3 style=\"color: #dc3545; margin: 25px 0 15px 0;\">🛍️ Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th style=\"text-align: center; width: 80px;\">Qty</th>
                        <th style=\"text-align: right; width: 100px;\">Unit Price</th>
                        <th style=\"text-align: right; width: 100px;\">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                    <tr class=\"subtotal-row\">
                        <td colspan=\"3\"><strong>Subtotal:</strong></td>
                        <td><strong>" . number_format($subtotal, 2) . " QR</strong></td>
                    </tr>";

        if ($shippingFee > 0) {
            $return .= "<tr class='subtotal-row'>
                        <td colspan='3'>Shipping Fee:</td>
                        <td>" . number_format($shippingFee, 2) . " QR</td>
                    </tr>";
        }

        if ($tax > 0) {
            $return .= "<tr class='subtotal-row'>
                        <td colspan='3'>Tax / VAT:</td>
                        <td>" . number_format($tax, 2) . " QR</td>
                    </tr>";
        }

        if ($discount > 0) {
            $return .= "<tr class='subtotal-row' style='color: #28a745;'>
                        <td colspan='3'>Discount Applied:</td>
                        <td>-" . number_format($discount, 2) . " QR</td>
                    </tr>";
        }

        $return .= "<tr class='total-row'>
                        <td colspan='3' style='text-align: right;'>TOTAL AMOUNT:</td>
                        <td style='text-align: right;'>" . number_format($total, 2) . " QR</td>
                    </tr>
                </tbody>
            </table>

            <div style='background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0;'>
                <strong>📋 Next Steps:</strong>
                <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                    <li>Verify order details and customer information</li>
                    <li>Check product availability and stock levels</li>
                    <li>Process payment (if applicable)</li>
                    <li>Prepare items for packaging and shipment</li>
                    <li>Update order status in admin panel</li>
                    <li>Send shipping confirmation to customer</li>
                </ul>
            </div>

            <p style='text-align: center; margin: 20px 0;'>
                <strong>Need to contact the customer?</strong><br>
                Email: <a href='mailto:{$customerEmail}'>{$customerEmail}</a><br>
                Phone: <a href='tel:{$customerPhone}'>{$customerPhone}</a>
            </p>
        </div>
        <div class='footer'>
            <p><strong>Athletes Gym Qatar - Order Management System</strong></p>
            <p>This is an automated notification. Do not reply to this email.</p>
        </div>
    </div>
</body>
</html>";

        return $return;
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
