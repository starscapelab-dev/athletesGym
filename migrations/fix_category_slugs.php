<?php
require_once __DIR__ . '/../admin/includes/db.php';

try {
    // Get all categories
    $stmt = $pdo->query("SELECT id, name, slug FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $pdo->prepare("UPDATE categories SET slug = ? WHERE id = ?");

    foreach ($categories as $category) {
        // Generate correct slug
        $newSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $category['name']));
        $newSlug = trim($newSlug, '-'); // Remove leading/trailing dashes

        // Update if different
        if ($category['slug'] !== $newSlug) {
            $updateStmt->execute([$newSlug, $category['id']]);
            echo "✓ Updated '{$category['name']}': '{$category['slug']}' -> '{$newSlug}'\n";
        } else {
            echo "- '{$category['name']}' already has correct slug: '{$newSlug}'\n";
        }
    }

    echo "\n✓ Successfully fixed all category slugs\n";
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
