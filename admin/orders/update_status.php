<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/email_helper.php";
require_once __DIR__ . '/../../layouts/config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $newStatus = $_POST['status'];

    // Get old status before updating
    $oldStatusStmt = $pdo->prepare("SELECT order_status FROM orders WHERE id = ?");
    $oldStatusStmt->execute([$id]);
    $oldStatus = $oldStatusStmt->fetchColumn();

    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET order_status=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$newStatus, $id]);

    // Send email notification if status changed
    if ($oldStatus !== $newStatus) {
        $emailSent = sendOrderStatusEmail($pdo, $id, $newStatus);

        if ($emailSent) {
            $_SESSION['msg'] = "Order #{$id} status updated to '{$newStatus}'. Email notification sent to customer.";
        } else {
            $_SESSION['msg'] = "Order #{$id} status updated to '{$newStatus}'. (Email notification failed to send)";
        }
    } else {
        $_SESSION['msg'] = "Order #{$id} status updated to '{$newStatus}'.";
    }
}

ob_end_clean();
header("Location: " . BASE_URL . "admin/orders/view.php?id=$id");
exit;
