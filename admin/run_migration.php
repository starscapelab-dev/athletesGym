<?php
/**
 * Migration Runner - Apply stock management columns to products table
 * This script safely applies the migration and displays the results
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/includes/db.php";

// Check if columns already exist
$columns = $pdo->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN, 0);

$missingColumns = [];
if (!in_array('track_stock', $columns)) {
    $missingColumns[] = 'track_stock';
}
if (!in_array('low_stock_threshold', $columns)) {
    $missingColumns[] = 'low_stock_threshold';
}
if (!in_array('featured', $columns)) {
    $missingColumns[] = 'featured';
}

if (empty($missingColumns)) {
    echo "<h2>✓ All migration columns already exist!</h2>";
    echo "<p>The following columns are already in the products table:</p>";
    echo "<ul>";
    echo "<li>track_stock</li>";
    echo "<li>low_stock_threshold</li>";
    echo "<li>featured</li>";
    echo "</ul>";
} else {
    echo "<h2>Running Migration...</h2>";
    echo "<p>Adding missing columns to products table...</p>";
    
    try {
        // Add columns if they don't exist
        if (in_array('track_stock', $missingColumns)) {
            $pdo->exec("ALTER TABLE `products` ADD COLUMN `track_stock` TINYINT(1) DEFAULT 1 COMMENT '1=Track stock, 0=Don''t track'");
            echo "<p>✓ Added track_stock column</p>";
        }
        
        if (in_array('low_stock_threshold', $missingColumns)) {
            $pdo->exec("ALTER TABLE `products` ADD COLUMN `low_stock_threshold` INT DEFAULT 5 COMMENT 'Alert when stock below this number'");
            echo "<p>✓ Added low_stock_threshold column</p>";
        }
        
        if (in_array('featured', $missingColumns)) {
            $pdo->exec("ALTER TABLE `products` ADD COLUMN `featured` TINYINT(1) DEFAULT 0 COMMENT '1=Featured product, 0=Regular'");
            echo "<p>✓ Added featured column</p>";
        }
        
        echo "<h3>Migration completed successfully!</h3>";
        echo "<p><a href='products/list.php'>Return to Products List</a></p>";
    } catch (Exception $e) {
        echo "<h2>✗ Migration Failed</h2>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
