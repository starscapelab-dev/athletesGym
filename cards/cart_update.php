<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');
ob_start();


try {
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../admin/includes/db.php";
require_once __DIR__ . "/../includes/cart_functions.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_auth();

$itemId = (int)($_POST['item_id'] ?? 0);
$newQty = (int)($_POST['qty'] ?? 1);
if ($newQty < 0) $newQty = 0;

// Fetch item + variant info from DB
$stmt = $pdo->prepare("
    SELECT ci.id AS cart_item_id, ci.product_id, ci.variant_id, ci.quantity,
           p.name, p.price, v.stock
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    LEFT JOIN product_variants v ON ci.variant_id = v.id
    WHERE ci.id = ?
");
$stmt->execute([$itemId]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    $_SESSION['cart_message'] = "Invalid cart item.";
    header("Location: ../cart.php");
    exit;
}

// Determine available stock
$availableStock = $item['stock'] ?? 9999; // fallback for non-variant products

if ($availableStock <= 0) {
    // FIXED: Delete item if out of stock
    $pdo->prepare("DELETE FROM cart_items WHERE id=?")->execute([$itemId]);
    $_SESSION['cart_message'] = "Item '{$item['name']}' is out of stock and was removed.";
}
elseif ($newQty == 0) {
    // FIXED: Delete item instead of marking as removed
    $pdo->prepare("DELETE FROM cart_items WHERE id=?")->execute([$itemId]);
    $_SESSION['cart_message'] = "Item removed from cart.";
}
elseif ($newQty > $availableStock) {
    // Adjust down to max stock
    $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?")->execute([$availableStock, $itemId]);
    $_SESSION['cart_message'] = "Only {$availableStock} pcs available for '{$item['name']}'. Quantity adjusted.";
}
else {
    // Valid quantity update
    $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?")->execute([$newQty, $itemId]);
    $_SESSION['cart_message'] = "Cart updated successfully.";
}

header("Location: ../cart.php");
exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred. Please try again later.'
    ]);
    
}