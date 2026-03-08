<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();

require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../admin/includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_auth();
// Ensure a cart exists
function getCartId($pdo) {
    if (isset($_SESSION['user_id'])) {
        // Logged-in user
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id=? and status = 'active' LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $cart = $stmt->fetchColumn();
        if (!$cart) {
            $pdo->prepare("INSERT INTO carts (user_id, status) VALUES (?, 'active')")->execute([$_SESSION['user_id']]);
            return $pdo->lastInsertId();
        }
        return $cart;
    } else {
        // Guest user (session-based)
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
            $pdo->prepare("INSERT INTO carts (session_id, status) VALUES (?, 'active')")->execute([$_SESSION['cart_session_id']]);
        }
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id=? and status = 'active' LIMIT 1");
        $stmt->execute([$_SESSION['cart_session_id']]);
        return $stmt->fetchColumn();
    }
}

// Add item
function addToCart($pdo, $productId, $variantId = null, $qty = 1) {
    $cartId = getCartId($pdo);
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id=? AND product_id=? AND variant_id <=> ?");
    $stmt->execute([$cartId, $productId, $variantId]);
    $item = $stmt->fetch();
    if ($item) {
        $newQty = $item['quantity'] + $qty;
        $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?")->execute([$newQty, $item['id']]);
    } else {
        $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, variant_id, quantity) VALUES (?,?,?,?)")
            ->execute([$cartId, $productId, $variantId, $qty]);
    }
}

// Update quantity
function updateCartItem($pdo, $itemId, $qty) {
    if ($qty > 0) {
        $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?")->execute([$qty, $itemId]);
    } else {
        $pdo->prepare("UPDATE carts SET status='checked_out', updated_at=NOW() WHERE id=?")->execute([$itemId]);
    }
}

// Get all cart items
function getCartItems($pdo) {
    $cartId = getCartId($pdo);
    $stmt = $pdo->prepare("SELECT ci.id as cart_item_id, ci.quantity, p.name, p.price, siz.name as size, col.name as color, (
    SELECT image_path 
    FROM product_images 
    WHERE product_id = p.id 
    ORDER BY id ASC 
    LIMIT 1
  ) AS image_path, ci.variant_id
                           FROM cart_items ci
                           JOIN products p ON ci.product_id=p.id
                           LEFT JOIN product_variants v ON ci.variant_id=v.id
                           LEFT JOIN sizes siz ON siz.id=v.size_id
                           LEFT JOIN colors col ON col.id=v.color_id
                           WHERE ci.cart_id=? and ci.is_removed = 0
                           ");
    $stmt->execute([$cartId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Empty cart
function clearCart($pdo) {
    $cartId = getCartId($pdo);
    $pdo->prepare("UPDATE carts SET status='checked_out', updated_at=NOW() WHERE id=?")->execute([$cartId]);

}
