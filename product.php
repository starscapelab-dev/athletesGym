<?php
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . '/admin/includes/functions.php';

require_auth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container'><h2>Product Not Found</h2></div>";
    require_once "layouts/footer.php";
    exit;
}

// Images (all)
$images = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id=?");
$images->execute([$id]);
$images = $images->fetchAll(PDO::FETCH_COLUMN);

// Variants
$variantsStmt = $pdo->prepare("
    SELECT v.id, c.name AS color, s.name AS size, v.stock
    FROM product_variants v
    JOIN colors c ON v.color_id = c.id
    JOIN sizes s ON v.size_id = s.id
    WHERE v.product_id = ?
    ORDER BY c.name, s.name
");
$variantsStmt->execute([$id]);
$variants = $variantsStmt->fetchAll(PDO::FETCH_ASSOC);
// Group variants
$colors = [];
$sizes = [];
$variantMap = [];
foreach ($variants as $v) {
    $colors[$v['color']] = true;
    $sizes[$v['size']] = true;
    $variantMap[$v['color']][$v['size']] = $v['stock'];
}
?>

<script>
  const variants = <?= json_encode($variants) ?>;
</script>

<div class="container">
    <div class="product-detail-container">
        <!-- Gallery -->
        <div class="product-gallery">
            <img id="main-img" class="main-img" src="<?= BASE_URL ?>uploads/<?=htmlspecialchars($images[0] ?? 'no-image.png')?>" alt="<?=htmlspecialchars($product['name'])?>">
            <?php if (count($images) > 1): ?>
                <div class="product-thumbs">
                    <?php foreach ($images as $i => $img): ?>
                        <img src="<?= BASE_URL ?>uploads/<?=htmlspecialchars($img)?>" alt="thumb" class="<?=($i==0?'selected':'')?>"
                        onclick="document.getElementById('main-img').src=this.src;
                                 document.querySelectorAll('.product-thumbs img').forEach(img=>img.classList.remove('selected'));
                                 this.classList.add('selected');">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="product-info">
            <div class="product-title"><?=htmlspecialchars($product['name'])?></div>
            <div class="product-price">QAR <?=number_format($product['price'],2)?></div>
            
            <!-- Stock indicator when less than 5 items -->
            <?php
            // Get total stock for this product
            $stockStmt = $pdo->prepare("SELECT SUM(stock) as total_stock FROM product_variants WHERE product_id = ?");
            $stockStmt->execute([$product['id']]);
            $stockResult = $stockStmt->fetch();
            $totalProductStock = $stockResult['total_stock'] ?? 0;
            ?>
            <?php if ($totalProductStock > 0 && $totalProductStock < 5): ?>
                <div style="margin-top: 12px; padding: 11px 14px; background: #f0f8f5; border-left: 3px solid #2da06a; border-radius: 6px; font-size: 13px; font-weight: 600; color: #1a5e3d; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 17px;">⏱</span>
                    <span>Only <?= $totalProductStock ?> item<?= $totalProductStock === 1 ? '' : 's' ?> left</span>
                </div>
            <?php elseif ($totalProductStock === 0): ?>
                <div style="margin-top: 12px; padding: 11px 14px; background: #f5f5f5; border-left: 3px solid #999; border-radius: 6px; font-size: 13px; font-weight: 600; color: #666; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 17px;">—</span>
                    <span>Out of Stock</span>
                </div>
            <?php endif; ?>
            
            <div class="product-description"><?=nl2br(htmlspecialchars($product['description']))?></div>

            <div class="product-options">
                <!-- Colors -->
                <?php if ($colors): ?>
                    <div><strong>Color:</strong></div>
                    <div class="product-swatches">
                        <?php foreach (array_keys($colors) as $i => $color): ?>
                            <span class="swatch <?=($i==0?'selected':'')?>" data-color="<?=htmlspecialchars($color)?>" style="background:<?=htmlspecialchars($color)?>"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Sizes -->
                <?php if ($sizes): ?>
                    <div style="margin-top: 15px;"><strong>Size:</strong></div>
                    <div class="size-list">
                        <?php foreach (array_keys($sizes) as $i => $size): ?>
                            <button type="button" class="size-btn <?=($i==0?'selected':'')?>" data-size="<?=htmlspecialchars($size)?>"><?=htmlspecialchars($size)?></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add to Cart -->
            <div class="add-cart-row product" data-id="<?= $product['id'] ?>">
                <div class="quantity-control">
                    <button type="button" class="qty-btn qty-minus">−</button>
                    <input type="number" class="quantity" value="1" min="1" max="10">
                    <button type="button" class="qty-btn qty-plus">+</button>
                </div>
                <input type="hidden" class="variant" value="">
                <button class="add-to-cart-btn">Add to Cart</button>
            </div>

            <div id="stock-status" style="margin-top:10px;color:#d90429;font-weight:600;display: none;"></div>
        </div>
    </div>
</div>

<div class="reviews-container">
  <h3>Customer Reviews</h3>

  <?php
  // Fetch reviews
  $stmt = $pdo->prepare("SELECT r.*, u.name 
                         FROM product_reviews r 
                         LEFT JOIN users u ON r.user_id = u.id
                         WHERE r.product_id=? AND r.status='approved'
                         ORDER BY r.created_at DESC");
  $stmt->execute([$product['id']]);
  $reviews = $stmt->fetchAll();

  if ($reviews): ?>
    <div class="reviews-list">
      <?php foreach ($reviews as $rev): ?>
        <div class="review-item">
          <div class="review-header">
            <strong><?= htmlspecialchars($rev['full_name'] ?? 'Guest User') ?></strong>
            <?php if ($rev['is_verified']): ?>
              <span class="verified-badge">✔ Verified Purchase</span>
            <?php endif; ?>
          </div>
          <div class="review-stars">
            <?php for ($i=1; $i<=5; $i++): ?>
              <span class="star <?= ($i <= $rev['rating']) ? 'filled' : '' ?>">★</span>
            <?php endfor; ?>
          </div>
          <p class="review-text"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
          <small><?= date('d M Y', strtotime($rev['created_at'])) ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No reviews yet. Be the first to leave one!</p>
  <?php endif; ?>
</div>

<!-- Review Form -->
<?php if (isset($_SESSION['user_id'])): ?>
  <div class="review-form">
    <h4>Write a Review</h4>
    <form method="post" action="review_submit.php">
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      <div class="rating-stars">
        <?php for ($i=1; $i<=5; $i++): ?>
          <input type="radio" name="rating" id="star<?=$i?>" value="<?=$i?>" required>
          <label for="star<?=$i?>">★</label>
        <?php endfor; ?>
      </div>
      <textarea name="review_text" placeholder="Write your review..." required></textarea>
      <button type="submit" class="btn-primary">Submit Review</button>
    </form>
  </div>
<?php else: ?>
  <p>Sign in to leave a review.</p>
<?php 
endif; 

// Fetch suggested products (same category or gender)
$suggestionsStmt = $pdo->prepare("
  SELECT a1.id, a1.name, a1.price, a2.image_path
  FROM products a1 left join product_images a2 on a1.id = a2.product_id
  WHERE a1.id != ? AND (a1.category_id = ? OR a1.gender = ?)
  ORDER BY RAND()
  LIMIT 4
");

$suggestionsStmt->execute([$product['id'], $product['category_id'], $product['gender']]);
$suggestions = $suggestionsStmt->fetchAll();


if ($suggestions): ?>
<section class="related-products">
  <div class="container">
    <h3 class="related-title">You May Also Like</h3>
    <div class="related-grid">
      <?php foreach ($suggestions as $s): ?>
        <div class="related-card">
          <a href="product.php?id=<?= $s['id'] ?>" style="color: #000000;">
            <div class="related-thumb">
              <img src="<?= BASE_URL ?>uploads/<?= $s['image_path'] ?>" alt="<?= $s['image_path'] ?>">
            </div>
            <div class="related-info">
              <h4><?= $s['name'] ?></h4>
              <p class="price">QAR <?= number_format($s['price'], 2) ?></p>
            </div>
          </a>
          <button class="add-to-cart-btn small" data-id="<?= $s['id'] ?>">Add to Cart</button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php 
endif; 

require_once "layouts/footer2.php"; ?>
