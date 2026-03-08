<?php
require_once "layouts/header-item.php";
require_once "admin/includes/db.php";
require_once "layouts/config.php";

// Get search query
$searchQuery = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';

// Build search query
$sql = "SELECT DISTINCT p.* FROM products p WHERE 1=1";
$params = [];

if (!empty($searchQuery)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $searchParam = "%{$searchQuery}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if (!empty($category)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories for filter
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll();
?>

<style>
.search-container {
    max-width: 1400px;
    margin: 150px auto 60px;
    padding: 0 20px;
}
.search-header {
    margin-bottom: 30px;
}
.search-header h2 {
    color: #21335b;
    font-size: 2rem;
    margin-bottom: 10px;
}
.search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}
.search-form input[type="text"] {
    flex: 1;
    min-width: 250px;
    padding: 12px 20px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}
.search-form select {
    padding: 12px 20px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    min-width: 150px;
}
.search-form button {
    padding: 12px 30px;
    background: #21335b;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.search-form button:hover {
    background: #1a2847;
}
.search-results {
    margin-bottom: 20px;
    color: #666;
}
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}
.product-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
    color: inherit;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
.product-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
}
.product-info {
    padding: 20px;
}
.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #21335b;
    margin-bottom: 10px;
}
.product-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2a9d8f;
    margin-bottom: 15px;
}
.product-category {
    display: inline-block;
    padding: 4px 12px;
    background: #f0f0f0;
    border-radius: 15px;
    font-size: 0.85rem;
    color: #666;
}
.no-results {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}
.no-results h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}
</style>

<div class="search-container">
    <div class="search-header">
        <h2>Search Products</h2>
    </div>

    <form class="search-form" method="GET" action="search.php">
        <input
            type="text"
            name="q"
            placeholder="Search for products..."
            value="<?= htmlspecialchars($searchQuery) ?>"
        >
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($searchQuery) || !empty($category)): ?>
        <div class="search-results">
            <strong><?= count($products) ?></strong> product(s) found
            <?php if (!empty($searchQuery)): ?>
                for "<strong><?= htmlspecialchars($searchQuery) ?></strong>"
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="no-results">
            <h3>No products found</h3>
            <p>Try adjusting your search or browse all products</p>
            <a href="<?= BASE_URL ?>shop.php" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background: #21335b; color: #fff; text-decoration: none; border-radius: 8px;">Browse All Products</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <a href="<?= BASE_URL ?>product.php?id=<?= $product['id'] ?>" class="product-card">
                    <?php
                    $imageStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY display_order LIMIT 1");
                    $imageStmt->execute([$product['id']]);
                    $image = $imageStmt->fetchColumn();
                    $imagePath = $image ? BASE_URL . $image : BASE_URL . 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-price"><?= number_format($product['price'], 2) ?> QR</div>
                        <?php
                        $catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                        $catStmt->execute([$product['category_id']]);
                        $catName = $catStmt->fetchColumn();
                        ?>
                        <?php if ($catName): ?>
                            <span class="product-category"><?= htmlspecialchars($catName) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once "layouts/footer.php"; ?>
