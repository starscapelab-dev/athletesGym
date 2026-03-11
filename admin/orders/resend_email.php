<?php
/**
 * Resend Order Email Handler
 * Resends order confirmation and admin notification emails
 */

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . '/../../layouts/config.php';
require_once __DIR__ . '/../../includes/simple_email_service.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);

    if (!$orderId) {
        $_SESSION['error_msg'] = "Invalid order ID.";
        header("Location: " . BASE_URL . "admin/orders/view.php?id=0");
        exit;
    }

    // Fetch order details
    try {
        $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch();

        if (!$order) {
            $_SESSION['error_msg'] = "Order not found.";
            header("Location: " . BASE_URL . "admin/orders/list.php");
            exit;
        }

        if (!$order['email']) {
            $_SESSION['error_msg'] = "Order has no customer email address.";
            header("Location: " . BASE_URL . "admin/orders/view.php?id=$orderId");
            exit;
        }

        // Fetch order items
        $itemsStmt = $pdo->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll();

        // Initialize email service
        $emailService = new SimpleEmailService();

        error_log("=== 📧 ADMIN RESENDING ORDER EMAILS ===");
        error_log("Order ID: {$orderId}");
        error_log("Customer Email: {$order['email']}");

        // Send customer confirmation email
        $customerEmailSent = $emailService->sendOrderConfirmation([
            'order_id' => $orderId,
            'customer_name' => $order['full_name'],
            'customer_email' => $order['email'],
            'customer_phone' => $order['phone'] ?? '',
            'shipping_address' => $order['shipping_address'] ?? '',
            'order_date' => $order['created_at'],
            'payment_method' => 'MyFatoorah',
            'order_status' => ucfirst($order['order_status']),
            'total' => $order['total'],
            'items' => array_map(function($item) {
                return [
                    'name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }, $items)
        ]);

        if ($customerEmailSent) {
            error_log("✅ Customer confirmation email resent successfully");
        } else {
            error_log("❌ Customer confirmation email FAILED to resend");
        }

        // Send admin notification email
        $adminEmailSent = $emailService->sendOrderNotificationToAdmin([
            'order_id' => $orderId,
            'customer_name' => $order['full_name'],
            'customer_email' => $order['email'],
            'customer_phone' => $order['phone'] ?? '',
            'shipping_address' => $order['shipping_address'] ?? '',
            'order_date' => $order['created_at'],
            'total' => $order['total'],
            'items' => array_map(function($item) {
                return [
                    'name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }, $items)
        ]);

        if ($adminEmailSent) {
            error_log("✅ Admin notification email resent successfully");
        } else {
            error_log("❌ Admin notification email FAILED to resend");
        }

        error_log("=== === ===");

        // Set success message
        if ($customerEmailSent && $adminEmailSent) {
            $_SESSION['success_msg'] = "✅ Order emails resent successfully! (Customer & Admin)";
        } elseif ($customerEmailSent) {
            $_SESSION['success_msg'] = "⚠️ Customer email sent, but admin email failed.";
        } elseif ($adminEmailSent) {
            $_SESSION['success_msg'] = "⚠️ Admin email sent, but customer email failed.";
        } else {
            $_SESSION['error_msg'] = "❌ Failed to resend both emails. Check error logs.";
        }

    } catch (Exception $e) {
        error_log("❌ EXCEPTION during resend emails: " . $e->getMessage());
        $_SESSION['error_msg'] = "Error resending emails: " . $e->getMessage();
    }
}

ob_end_clean();
header("Location: " . BASE_URL . "admin/orders/view.php?id=$orderId");
exit;
?>
