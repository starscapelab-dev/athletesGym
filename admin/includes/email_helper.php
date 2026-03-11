<?php
/**
 * Email Helper Functions
 * Handles email sending for order status updates
 */

/**
 * Send order status update email to customer
 */
function sendOrderStatusEmail($pdo, $orderId, $newStatus) {
    // Fetch order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as customer_name, u.email as customer_email
        FROM orders o
        LEFT JOIN users u ON o.customer_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order || !$order['email']) {
        return false;
    }

    // Get order items with product images
    $itemsStmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, pi.image_path
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_images pi ON oi.product_id = pi.product_id
        WHERE oi.order_id = ?
        ORDER BY oi.id, pi.id
    ");
    $itemsStmt->execute([$orderId]);
    $allItems = $itemsStmt->fetchAll();
    
    // Group images by order_item_id
    $items = [];
    foreach ($allItems as $item) {
        $key = $item['id'];
        if (!isset($items[$key])) {
            $items[$key] = $item;
            $items[$key]['images'] = [];
        }
        if ($item['image_path']) {
            $items[$key]['images'][] = $item['image_path'];
        }
    }
    $items = array_values($items);

    // Prepare email based on status
    $subject = getEmailSubject($newStatus, $orderId);
    $htmlBody = getEmailTemplate($order, $items, $newStatus);

    // Send email
    return sendEmail($order['email'], $subject, $htmlBody, $order['customer_name'] ?? $order['full_name']);
}

/**
 * Get email subject based on order status
 */
function getEmailSubject($status, $orderId) {
    $subjects = [
        'pending' => "Order #$orderId Received - Athletes Gym",
        'processing' => "Order #$orderId is Being Processed - Athletes Gym",
        'shipped' => "Order #$orderId Has Been Shipped - Athletes Gym",
        'delivered' => "Order #$orderId Delivered - Athletes Gym",
        'refunded' => "Order #$orderId Refunded - Athletes Gym",
        'cancelled' => "Order #$orderId Cancelled - Athletes Gym"
    ];

    return $subjects[$status] ?? "Order #$orderId Status Update - Athletes Gym";
}

/**
 * Generate HTML email template
 */
function getEmailTemplate($order, $items, $status) {
    // Load env if needed
    if (!function_exists('env')) {
        require_once __DIR__ . '/../../includes/env_loader.php';
    }
    
    $orderId = $order['id'];
    $customerName = $order['customer_name'] ?? $order['full_name'];
    $orderDate = date('M d, Y', strtotime($order['created_at']));
    $total = number_format($order['total'], 2);
    $siteUrl = env('APP_URL', 'https://athletesgym.qa');

    // Status-specific content
    $statusContent = getStatusContent($status, $orderId);

    // Build items HTML with images
    $itemsHtml = '';
    foreach ($items as $item) {
        $itemTotal = number_format($item['price'] * $item['quantity'], 2);
        $imageHtml = '';
        
        // Add first product image if available
        if (!empty($item['images']) && isset($item['images'][0])) {
            $imagePath = $item['images'][0];
            // Build image URL
            $imageUrl = $siteUrl . '/admin/product_images/' . $imagePath;
            $imageHtml = "<img src='{$imageUrl}' alt='{$item['product_name']}' style='max-width: 120px; max-height: 120px; border-radius: 4px; margin-right: 15px; vertical-align: top;' />";
        }
        
        $itemsHtml .= "
        <tr>
            <td style='padding: 15px; border-bottom: 1px solid #e5e5e5; vertical-align: top;'>
                {$imageHtml}
                <div style='display: inline-block; vertical-align: top;'>
                    <strong style='color: #000; font-size: 15px;'>{$item['product_name']}</strong><br>
                    <span style='color: #666; font-size: 13px;'>Quantity: {$item['quantity']}</span>
                </div>
            </td>
            <td style='padding: 15px; border-bottom: 1px solid #e5e5e5; text-align: right; color: #000; font-weight: 600;'>
                {$itemTotal} QAR
            </td>
        </tr>";
    }

    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Order Status Update</title>
    </head>
    <body style='margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif; background-color: #f5f5f5;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f5f5f5; padding: 40px 20px;'>
            <tr>
                <td align='center'>
                    <!-- Main Container -->
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>

                        <!-- Header -->
                        <tr>
                            <td style='background-color: #000000; padding: 30px 40px; text-align: center;'>
                                <h1 style='margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 1px;'>
                                    ATHLETES GYM
                                </h1>
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <td style='padding: 40px;'>
                                <h2 style='margin: 0 0 20px 0; color: #000; font-size: 22px; font-weight: 600;'>
                                    {$statusContent['title']}
                                </h2>

                                <p style='margin: 0 0 25px 0; color: #333; font-size: 15px; line-height: 1.6;'>
                                    Hi {$customerName},
                                </p>

                                <p style='margin: 0 0 30px 0; color: #333; font-size: 15px; line-height: 1.6;'>
                                    {$statusContent['message']}
                                </p>

                                <!-- Order Details Box -->
                                <div style='background-color: #f8f8f8; border: 1px solid #e5e5e5; border-radius: 6px; padding: 25px; margin-bottom: 30px;'>
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td style='padding-bottom: 15px;'>
                                                <span style='color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;'>Order Number</span><br>
                                                <strong style='color: #000; font-size: 18px;'>#{$orderId}</strong>
                                            </td>
                                            <td style='padding-bottom: 15px; text-align: right;'>
                                                <span style='color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;'>Order Date</span><br>
                                                <strong style='color: #000; font-size: 16px;'>{$orderDate}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan='2' style='padding-top: 15px; border-top: 1px solid #e5e5e5;'>
                                                <span style='color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;'>Status</span><br>
                                                <strong style='color: #000; font-size: 16px; text-transform: capitalize;'>{$status}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Order Items -->
                                <h3 style='margin: 0 0 20px 0; color: #000; font-size: 18px; font-weight: 600;'>Order Items</h3>
                                <table width='100%' cellpadding='0' cellspacing='0' style='border: 1px solid #e5e5e5; border-radius: 6px; overflow: hidden;'>
                                    {$itemsHtml}
                                    <tr>
                                        <td style='padding: 20px; text-align: right; border-top: 2px solid #000; background-color: #f8f8f8;' colspan='2'>
                                            <span style='color: #666; font-size: 14px; margin-right: 15px;'>Total:</span>
                                            <strong style='color: #000; font-size: 20px;'>{$total} QAR</strong>
                                        </td>
                                    </tr>
                                </table>

                                {$statusContent['action']}
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style='background-color: #f8f8f8; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e5e5;'>
                                <p style='margin: 0 0 10px 0; color: #666; font-size: 13px;'>
                                    Thank you for shopping with Athletes Gym
                                </p>
                                <p style='margin: 0; color: #999; font-size: 12px;'>
                                    If you have any questions, please contact our support team.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
}

function getStatusContent($status, $orderId) {
    $content = [
        'pending' => [
            'title' => 'Order Received',
            'message' => 'We have received your order and will begin processing it shortly. You will receive another email once your order is confirmed and being prepared.',
            'action' => ''
        ],
        'processing' => [
            'title' => 'Order is Being Processed',
            'message' => 'Great news! Your order is now being prepared by our team. We are carefully packaging your items and they will be ready for shipment soon.',
            'action' => ''
        ],
        'shipped' => [
            'title' => 'Your Order Has Been Shipped',
            'message' => 'Exciting news! Your order has been shipped and is on its way to you. You should receive it within the next few business days.',
            'action' => '<div style="margin-top: 30px; padding: 20px; background-color: #f0f8ff; border-left: 4px solid #000; border-radius: 4px;">
                <p style="margin: 0; color: #333; font-size: 14px;">
                    <strong>Track your package:</strong> Your order is being delivered to the address provided.
                </p>
            </div>'
        ],
        'delivered' => [
            'title' => 'Order Delivered Successfully',
            'message' => 'Your order has been delivered! We hope you enjoy your purchase. Thank you for choosing Athletes Gym.',
            'action' => '<div style="margin-top: 30px; text-align: center;">
                <p style="margin: 0 0 15px 0; color: #333; font-size: 14px;">How was your experience?</p>
                <p style="margin: 0; color: #666; font-size: 13px;">We would love to hear your feedback about your purchase.</p>
            </div>'
        ],
        'refunded' => [
            'title' => 'Refund Processed',
            'message' => 'Your refund has been processed successfully. The funds should appear in your account within 5-7 business days.',
            'action' => '<div style="margin-top: 30px; padding: 20px; background-color: #f0fff0; border-left: 4px solid #28a745; border-radius: 4px;">
                <p style="margin: 0; color: #333; font-size: 14px;">
                    <strong>Refund Status:</strong> Complete
                </p>
            </div>'
        ],
        'cancelled' => [
            'title' => 'Order Cancelled',
            'message' => 'Your order has been cancelled as requested. If you did not request this cancellation or have any questions, please contact our support team immediately.',
            'action' => '<div style="margin-top: 30px; padding: 20px; background-color: #fff5f5; border-left: 4px solid #dc3545; border-radius: 4px;">
                <p style="margin: 0; color: #333; font-size: 14px;">
                    <strong>Refund:</strong> If you have already paid, your refund will be processed within 5-7 business days.
                </p>
            </div>'
        ]
    ];

    return $content[$status] ?? $content['pending'];
}

/**
 * Send email using PHP mail function
 */
function sendEmail($to, $subject, $htmlBody, $toName = '') {
    // Load environment variables if not already loaded
    if (!function_exists('env')) {
        require_once __DIR__ . '/../../includes/env_loader.php';
    }

    // Get email configuration from environment variables
    $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@athletesgym.qa');
    $fromName = env('MAIL_FROM_NAME', 'Athletes Gym Qatar');
    $replyToEmail = env('MAIL_ADMIN_ADDRESS', 'athletesgymqa@gmail.com');

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        "From: {$fromName} <{$fromEmail}>",
        "Reply-To: {$replyToEmail}",
        'X-Mailer: PHP/' . phpversion(),
        'Return-Path: ' . $fromEmail
    ];

    // Add recipient name if provided
    $recipient = $toName ? "$toName <$to>" : $to;

    // Send email with error logging
    $success = mail($recipient, $subject, $htmlBody, implode("\r\n", $headers));

    // Log email sending for debugging
    if (!$success) {
        error_log("Failed to send email to: {$to} | Subject: {$subject} | From: {$fromEmail}");
    } else {
        error_log("Email sent successfully to: {$to} | Subject: {$subject}");
    }

    return $success;
}
