<?php
require_once __DIR__ . '/../admin/includes/db.php';

try {
    // Check if T-Shirt already exists in Unisex
    $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = 'T-Shirt' AND gender = 'Unisex'");
    $checkStmt->execute();
    $exists = $checkStmt->fetch();

    if ($exists) {
        echo "✓ T-Shirt category already exists in Unisex (ID: {$exists['id']})\n";
    } else {
        // Insert T-Shirt category into Unisex
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image, gender, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['T-Shirt', 't-shirt', 'uploads/categories/cat_5.png', 'Unisex']);

        $newId = $pdo->lastInsertId();
        echo "✓ Successfully added T-Shirt category to Unisex (ID: {$newId})\n";
    }
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
