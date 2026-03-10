<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');
ob_start();

try {
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../admin/includes/db.php";
require_once __DIR__ . "/../includes/cart_functions.php";

require_auth();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

try {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input || !isset($input['product_id'])) {
        throw new Exception("Invalid request payload");
    }

    $productId = (int)$input['product_id'];
    $variantId = (int)($input['variant_id'] ?? 0);
    $qty = (int)($input['quantity'] ?? 1);
    if ($qty < 1) $qty = 1;

    // Enable PDO exception mode for debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Fetch product ---
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        throw new Exception("Product not found (ID: $productId)");
    }

    // --- Validate variant ---
    if ($variantId > 0) {
        $stmt = $pdo->prepare("SELECT id, stock, color_id, size_id FROM product_variants WHERE id=? AND product_id=?");
        $stmt->execute([$variantId, $productId]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$variant) {
            throw new Exception("Variant not found for this product.");
        }
        $stock = (int)$variant['stock'];
    } else {
        throw new Exception("Variant ID is required for this product.");
    }

    // --- Get cart ID using the same logic as getCartId() function ---
    $cartId = getCartId($pdo);

    // --- Check if item already exists in the cart ---
    $stmt = $pdo->prepare("
        SELECT id, quantity 
        FROM cart_items 
        WHERE cart_id = ? AND product_id = ? AND variant_id = ?
    ");
    $stmt->execute([$cartId, $productId, $variantId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // --- Get current user ID for cart_items table ---
    $currentUserId = $_SESSION['user_id'] ?? null;
    $currentSessionId = session_id();

    $currentQty = $existing ? (int)$existing['quantity'] : 0;
    $newQty = $currentQty + $qty;

    // --- Validate stock limit ---
    if ($newQty > $stock) {
        echo json_encode([
            "success" => false,
            "message" => "Only {$stock} in stock for this variant. You already have {$currentQty} in your cart."
        ]);
        exit;
    }

    // --- Insert or update cart item ---
    if ($existing) {
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newQty, $existing['id']]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (cart_id, user_id, product_id, variant_id, session_id, quantity, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$cartId, $currentUserId, $productId, $variantId, $currentSessionId, $qty]);
    }

    // --- Count total items for cart indicator ---
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cartId]);
    $cartCount = (int)$stmt->fetchColumn();

    echo json_encode([
        "success" => true,
        "message" => "✅ Added to cart successfully",
        "item" => [
            "id" => $product["id"],
            "name" => $product["name"],
            "quantity" => $newQty,
            "variant_id" => $variantId
        ],
        "cart_count" => $cartCount
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred. Please try again later.'
    ]);
    
}
?>
