<?php
session_start();
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Set JSON header
header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['order_ids']) || !is_array($data['order_ids']) || empty($data['order_ids'])) {
    echo json_encode(['success' => false, 'message' => 'No order IDs provided']);
    exit;
}

$orderIds = $data['order_ids'];

try {
    $pdo->beginTransaction();

    // Prepare placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

    // Delete order items first (foreign key constraint)
    $stmtItems = $pdo->prepare("DELETE FROM order_items WHERE order_id IN ($placeholders)");
    $stmtItems->execute($orderIds);
    $itemsDeleted = $stmtItems->rowCount();

    // Delete orders
    $stmtOrders = $pdo->prepare("DELETE FROM orders WHERE id IN ($placeholders)");
    $stmtOrders->execute($orderIds);
    $ordersDeleted = $stmtOrders->rowCount();

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => "Successfully deleted $ordersDeleted order(s) and $itemsDeleted order item(s)."
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error deleting orders: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete orders: ' . $e->getMessage()
    ]);
}
?>
