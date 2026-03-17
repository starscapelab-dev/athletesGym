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

// CSV Headers
fputcsv($output, [
    'ID',
    'Name',
    'Description',
    'Category ID',
    'Category Name',
    'Gender',
    'Type',
    'Price',
    'Stock Threshold',
    'Created At',
    'Updated At'
]);

try {
    // Fetch all products
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id ASC
    ");

    // Write product data
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $product['id'],
            $product['name'],
            $product['description'] ?? '',
            $product['category_id'] ?? '',
            $product['category_name'] ?? '',
            $product['gender'] ?? '',
            $product['type'] ?? '',
            $product['price'],
            $product['stock_threshold'] ?? 10,
            $product['created_at'] ?? '',
            $product['updated_at'] ?? ''
        ]);
    }
} catch (Exception $e) {
    // Log error but don't output it (would corrupt CSV)
    error_log("CSV Export Error: " . $e->getMessage());
}

fclose($output);
exit;
