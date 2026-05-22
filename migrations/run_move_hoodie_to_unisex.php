<?php
require_once __DIR__ . '/../admin/includes/db.php';

try {
    // Read and execute the migration
    $sql = file_get_contents(__DIR__ . '/move_hoodie_to_unisex.sql');
    $pdo->exec($sql);
    echo "✓ Successfully moved Hoodie category from Men to Unisex\n";
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
