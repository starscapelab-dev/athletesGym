<?php
// Session already started by session.php
require_once "admin/includes/db.php";
require_once "includes/session.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: shop.php");
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$productId = (int)$_POST['product_id'];
$rating = (int)$_POST['rating'];
$text = trim($_POST['review_text']);

if ($rating < 1 || $rating > 5 || empty($text)) {
    $_SESSION['review_error'] = "Please provide rating and review text.";
    header("Location: shop_product.php?id=$productId");
    exit;
}

// Verify if the user has bought the product
$isVerified = 0;
if ($userId) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE o.customer_id = ? AND oi.product_id = ?
    ");
    $stmt->execute([$userId, $productId]);
    if ($stmt->fetchColumn() > 0) $isVerified = 1;
}

$stmt = $pdo->prepare("
    INSERT INTO product_reviews (product_id, user_id, rating, review_text, is_verified, status)
    VALUES (?, ?, ?, ?, ?, 'pending')
");
$stmt->execute([$productId, $userId, $rating, $text, $isVerified]);

$_SESSION['review_success'] = "Thank you! Your review is awaiting approval.";
header("Location: product.php?id=$productId");
exit;
?>
