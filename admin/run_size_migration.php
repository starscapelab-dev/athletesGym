<?php
/**
 * Migration Script: Add sort_order to sizes table
 * Run this once to update the database schema
 */

require_once __DIR__ . "/includes/db.php";

echo "<!DOCTYPE html><html><head><title>Size Sort Order Migration</title>";
echo "<style>body{font-family:Arial;padding:20px;max-width:800px;margin:0 auto;}";
echo ".success{color:green;padding:10px;background:#e8f5e9;border-left:4px solid green;margin:10px 0;}";
echo ".error{color:red;padding:10px;background:#ffebee;border-left:4px solid red;margin:10px 0;}";
echo "h1{color:#333;}</style></head><body>";

echo "<h1>Size Sort Order Migration</h1>";

try {
    // Check if column already exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM sizes LIKE 'sort_order'")->fetch();

    if ($checkColumn) {
        echo "<div class='error'>⚠️ Column 'sort_order' already exists in sizes table. Migration already applied.</div>";
    } else {
        // Add sort_order column
        $pdo->exec("ALTER TABLE `sizes` ADD COLUMN `sort_order` INT NOT NULL DEFAULT 999 AFTER `name`");
        echo "<div class='success'>✅ Added 'sort_order' column to sizes table</div>";

        // Update existing sizes with proper sort order
        $updates = [
            ['name' => 'S', 'order' => 1],
            ['name' => 'M', 'order' => 2],
            ['name' => 'L', 'order' => 3],
            ['name' => 'XL', 'order' => 4],
            ['name' => 'XXL', 'order' => 5],
            ['name' => 'S1', 'order' => 10],
            ['name' => 'S2', 'order' => 11],
            ['name' => 'M1', 'order' => 12],
            ['name' => 'M2', 'order' => 13],
            ['name' => 'Regular', 'order' => 20],
        ];

        $stmt = $pdo->prepare("UPDATE `sizes` SET `sort_order` = ? WHERE `name` = ?");
        foreach ($updates as $update) {
            $stmt->execute([$update['order'], $update['name']]);
            echo "<div class='success'>✅ Updated '{$update['name']}' with sort_order = {$update['order']}</div>";
        }

        // Add index
        $pdo->exec("CREATE INDEX `idx_sort_order` ON `sizes`(`sort_order`)");
        echo "<div class='success'>✅ Created index on sort_order column</div>";

        echo "<h2 style='color:green;'>Migration Completed Successfully!</h2>";
        echo "<p>All sizes now have proper sort order: S, M, L, XL, XXL</p>";
    }

    // Display current sizes order
    echo "<h2>Current Sizes (Ordered by sort_order):</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f5f5f5;'><th>ID</th><th>Name</th><th>Sort Order</th></tr>";

    $sizes = $pdo->query("SELECT * FROM sizes ORDER BY sort_order, name")->fetchAll();
    foreach ($sizes as $size) {
        echo "<tr><td>{$size['id']}</td><td><strong>{$size['name']}</strong></td><td>{$size['sort_order']}</td></tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "<br><p><a href='dashboard.php'>← Back to Dashboard</a></p>";
echo "</body></html>";
?>
