<?php
function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get product image URL with fallback to placeholder
 * @param string $imagePath The image path from database
 * @param string $baseUrl The site base URL
 * @return string The image URL or placeholder
 */
function getProductImageUrl($imagePath, $baseUrl = '') {
    if (empty($baseUrl)) {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';
    }
    
    $uploadsDir = __DIR__ . '/../../uploads/';
    
    // If image path is provided and file exists
    if (!empty($imagePath) && file_exists($uploadsDir . $imagePath)) {
        return $baseUrl . 'uploads/' . sanitize($imagePath);
    }
    
    // Return SVG placeholder
    return $baseUrl . 'uploads/no-image.svg';
}

/**
 * Check if product has images
 * @param int $productId Product ID
 * @param object $pdo PDO database connection
 * @return bool
 */
function hasProductImages($productId, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetchColumn() > 0;
}
