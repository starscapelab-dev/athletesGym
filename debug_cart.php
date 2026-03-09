<?php
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/includes/cart_functions.php";

require_auth();

// Get cart info
$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();

echo "<h2>Debug Cart Information</h2>";
echo "<p>User ID: " . ($userId ? $userId : "Not logged in") . "</p>";
echo "<p>Session ID: " . $sessionId . "</p>";

// Get cart ID
$cartId = null;
if ($userId) {
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id=? and status = 'active' LIMIT 1");
    $stmt->execute([$userId]);
    $cartId = $stmt->fetchColumn();
    echo "<p>Cart found for user: " . ($cartId ? "Yes (ID: $cartId)" : "No") . "</p>";
} else {
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id=? and status = 'active' LIMIT 1");
    $stmt->execute([$sessionId]);
    $cartId = $stmt->fetchColumn();
    echo "<p>Cart found for session: " . ($cartId ? "Yes (ID: $cartId)" : "No") . "</p>";
}

// Check cart_items structure
echo "<h3>Cart Items Table Structure:</h3>";
$stmt = $pdo->query("DESCRIBE cart_items");
echo "<pre>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
echo "</pre>";

// Check items in cart
if ($cartId) {
    echo "<h3>Items in Cart (ID: $cartId):</h3>";
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id=?");
    $stmt->execute([$cartId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($items);
    echo "</pre>";
    
    // Try getCartItems function
    echo "<h3>getCartItems() Result:</h3>";
    $items = getCartItems($pdo);
    echo "<pre>";
    print_r($items);
    echo "</pre>";
}
?>
