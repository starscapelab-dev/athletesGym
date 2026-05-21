<?php 
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . '/admin/includes/functions.php';

require_auth();

$gender = $_GET['gender'] ?? 'Accessories';

// Fetch categories for the selected gender
$stmt = $pdo->prepare("SELECT * FROM categories WHERE gender = ? ORDER BY name");
$stmt->execute([$gender]);
$categories = $stmt->fetchAll();

// If Unisex is requested, fetch products marked as unisex (or in Unisex categories)
$unisexProducts = [];
if (strtolower($gender) === 'unisex') {
  $productsStmt = $pdo->prepare(
    "SELECT p.*, (SELECT image_path FROM product_images WHERE product_id=p.id ORDER BY is_featured DESC, id ASC LIMIT 1) AS main_image
     FROM products p
     WHERE LOWER(p.gender) = 'unisex' OR p.category_id IN (SELECT id FROM categories WHERE gender = 'Unisex')
     ORDER BY p.created_at DESC"
  );
  $productsStmt->execute();
  $unisexProducts = $productsStmt->fetchAll();
}
?>

<main class="main-wrapper">
  <div class="container">
    <h1 class="shop-title">Shop <?= htmlspecialchars($gender) ?> Categories</h1>
    <?php if (strtolower($gender) === 'unisex'): ?>
      <?php if (!$unisexProducts): ?>
        <p class="no-data">No products found for Unisex.</p>
      <?php else: ?>
        <div class="products-grid">
          <?php foreach ($unisexProducts as $product): ?>
            <div class="product-card" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>">
              <a href="product.php?id=<?= $product['id'] ?>">
                <img src="<?= getProductImageUrl($product['main_image'] ?? '', BASE_URL) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
              </a>
              <h4><?= htmlspecialchars($product['name']) ?></h4>
              <p><?= number_format($product['price'], 2) ?> QR</p>
              <input type="hidden" class="quantity" value="1">
              <button class="btn-primary add-to-cart-btn">Add to Cart</button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php else: ?>
    <!-- Categories Section -->
    <div class="categories-grid">
      <?php
      foreach ($categories as $cat):
        // Handle both old (just filename) and new (full path) image formats
        $imagePath = $cat['image'];
        if ($imagePath && strpos($imagePath, 'uploads/') !== 0) {
          $imagePath = 'uploads/categories/' . $imagePath;
        }
      ?>
        <div class="category-card">
          <a href="items.php?category=<?= $cat['id'] ?>" style="color:rgb(0 0 0)">
            <div class="category-thumb">
              <img src="<?= BASE_URL ?><?= $imagePath ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
            </div>
            <h3><?= htmlspecialchars($cat['name']) ?></h3>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <hr>

    <!-- Products Section -->
    <!-- <div class="products-grid">
      <?php
      $where = "";
      $params = [];
      if (isset($_GET['category'])) {
        $where = "WHERE category_id=?";
        $params[] = (int)$_GET['category'];
      }

      $stmt = $pdo->prepare("SELECT * FROM products $where ORDER BY created_at DESC");
      $stmt->execute($params);
      $products = $stmt->fetchAll();

      if (!$products) {
        echo "<p>No products found in this category.</p>";
      } else {
        foreach ($products as $product): ?>
          <div class="product-card" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>">
            <a href="product.php?id=<?= $product['id'] ?>">
              <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </a>
            <h4><?= htmlspecialchars($product['name']) ?></h4>
            <p><?= number_format($product['price'], 2) ?> QR</p>
            <input type="hidden" class="quantity" value="1">
            <button class="btn-primary add-to-cart-btn">Add to Cart</button>
          </div>
      <?php endforeach;
      } ?>
    </div> -->
  </div>
</main>

<?php require_once "layouts/footer.php"; ?>