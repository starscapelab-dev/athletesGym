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

/**
 * Sort sizes in proper order (XS, S, M, L, XL, XXL, etc.)
 * @param array $sizes Array of size names
 * @return array Sorted array of sizes
 */
function sortSizes($sizes) {
    $sizeOrder = [
        'XS' => 1,
        'S' => 2,
        'M' => 3,
        'L' => 4,
        'XL' => 5,
        'XXL' => 6,
        '2XL' => 6,
        '3XL' => 7,
        '4XL' => 8,
        'REGULAR' => 9,
        'ONE SIZE' => 10
    ];

    usort($sizes, function($a, $b) use ($sizeOrder) {
        $orderA = $sizeOrder[strtoupper($a)] ?? 999;
        $orderB = $sizeOrder[strtoupper($b)] ?? 999;
        if ($orderA === $orderB) {
            return strcasecmp($a, $b); // Alphabetical for unknown sizes
        }
        return $orderA - $orderB;
    });

    return $sizes;
}

/**
 * Get SQL ORDER BY clause for sizes in proper order
 * @return string SQL CASE statement for ordering sizes
 */
function getSizeSortSQL() {
    return "CASE UPPER(s.name)
        WHEN 'XS' THEN 1
        WHEN 'S' THEN 2
        WHEN 'M' THEN 3
        WHEN 'L' THEN 4
        WHEN 'XL' THEN 5
        WHEN 'XXL' THEN 6
        WHEN '2XL' THEN 6
        WHEN '3XL' THEN 7
        WHEN '4XL' THEN 8
        WHEN 'REGULAR' THEN 9
        WHEN 'ONE SIZE' THEN 10
        ELSE 999
    END";
}
