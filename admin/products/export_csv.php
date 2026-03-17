<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

// Check if user is admin
requireAdmin();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=products_export_' . date('Y-m-d_H-i-s') . '.csv');

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
        $product['description'],
        $product['category_id'],
        $product['category_name'],
        $product['gender'],
        $product['type'],
        $product['price'],
        $product['stock_threshold'],
        $product['created_at'],
        $product['updated_at']
    ]);
}

fclose($output);
exit;
