<?php 
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . '/admin/includes/functions.php';

$gender = $_GET['gender'] ?? 'Accessories';

// Fetch categories for the selected gender
$stmt = $pdo->prepare("SELECT * FROM categories WHERE gender = ? ORDER BY name");
$stmt->execute([$gender]);
$categories = $stmt->fetchAll();
?>

<main class="main-wrapper">
  <div class="container">
    <h1 class="shop-title">Shop <?= htmlspecialchars($gender) ?> Categories</h1>

    <!-- Categories Section -->
    <?php if (empty($categories)): ?>
      <p class="no-data">No categories found for <?= htmlspecialchars($gender) ?>.</p>
    <?php else: ?>
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
  </div>
</main>

<?php require_once "layouts/footer.php"; ?>