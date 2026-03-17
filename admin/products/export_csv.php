<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors as they'll corrupt CSV

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=products_export_' . date('Y-m-d_H-i-s') . '.csv');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers matching client's format
fputcsv($output, [
    'CODE',
    'SIZE',
    'QUANTITY',
    '1 UNIT COST (QR)',
    'CATEGORY',
    'PRODUCT NAME'
]);

try {
    // Fetch all products with their variants
    $stmt = $pdo->query("
        SELECT
            p.id as product_code,
            p.name as product_name,
            c.name AS category_name,
            p.gender,
            p.price,
            s.name as size_name,
            pv.stock as quantity,
            pv.price as variant_price
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_variants pv ON pv.product_id = p.id
        LEFT JOIN sizes s ON pv.size_id = s.id
        WHERE p.active = 1
        ORDER BY p.id ASC, s.name ASC
    ");

    // Track if product has been output (for products without variants)
    $productsOutput = [];

    // Write product data
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $code = $row['product_code'] ?? '';
        $productName = $row['product_name'] ?? '';
        $category = strtoupper($row['gender'] ?? 'UNISEX');
        $size = $row['size_name'] ?? '';
        $quantity = $row['quantity'] ?? 0;
        $unitCost = $row['variant_price'] ?? $row['price'] ?? 0;

        // If product has variants, output each variant as a row
        if (!empty($size)) {
            fputcsv($output, [
                $code,
                $size,
                $quantity,
                number_format($unitCost, 2, '.', ''),
                $category,
                $productName
            ]);
            $productsOutput[$code] = true;
        }
        // If product has no variants and hasn't been output yet, output once
        elseif (!isset($productsOutput[$code])) {
            fputcsv($output, [
                $code,
                '',
                0,
                number_format($unitCost, 2, '.', ''),
                $category,
                $productName
            ]);
            $productsOutput[$code] = true;
        }
    }
} catch (Exception $e) {
    // Log error but don't output it (would corrupt CSV)
    error_log("CSV Export Error: " . $e->getMessage());
}

fclose($output);
exit;
