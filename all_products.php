<?php 

require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . '/admin/includes/functions.php';

require_auth();

// Fetch all categories ordered by name
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

?>

<main class="main-wrapper">
    <!-- All Products Section -->
    <section class="blog-section section-padding bg-white">
        <div class="container">
            <div class="row align-items-center justify-content-between heading-row">
                <div class="col-xxl-5 col-xl-6 col-lg-6">
                    <div class="section-heading wow fadeInUp">
                        <span class="heading-tag">Our Shop</span>
                        <h3 style="color: #000;">All Products</h3>
                    </div>
                </div>
            </div>

            <?php foreach ($categories as $category): ?>
                <?php
                    // Fetch all products for this category
                    $products = $pdo->prepare("
                        SELECT p.*, (SELECT image_path FROM product_images WHERE product_id=p.id LIMIT 1) AS main_image
                        FROM products p  
                        WHERE category_id=? AND active='1'
                        ORDER BY p.id DESC
                    ");
                    $products->execute([$category['id']]);
                    $products = $products->fetchAll();
                ?>

                <?php if ($products): ?>
                    <!-- Category Section -->
                    <div style="margin-top: 50px; margin-bottom: 30px;">
                        <div style="border-bottom: 3px solid #1a1a1a; padding-bottom: 15px; margin-bottom: 30px;">
                            <h2 style="color: #1a1a1a; margin: 0; font-size: 1.8em;"><?= htmlspecialchars($category['name']) ?></h2>
                            <p style="color: #666; margin: 5px 0 0 0;">Browse our collection of <?= htmlspecialchars($category['name']) ?></p>
                        </div>

                        <div class="row justify-content-center">
                            <?php foreach ($products as $p): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="blog-wrapper wow fadeInUp product" 
                                        data-id="<?= $p['id'] ?>"
                                        data-name="<?= htmlspecialchars($p['name']) ?>"
                                        data-price="<?= $p['price'] ?>"
                                        data-size="">
                                        <a href="product.php?id=<?= $p['id'] ?>" class="img-wrapper" style="color:rgb(0 0 0)">
                                            <img loading='lazy' src="<?= getProductImageUrl($p['main_image'], BASE_URL) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                        </a>
                                        <div class="info">
                                            <span><?= number_format($p['price'],2) ?> QR</span>
                                        </div>
                                        <h2><?= htmlspecialchars($p['name']) ?></h2>
                                        <!-- Swatches: fetch from product_variants -->
                                        <?php
                                        $swatches = $pdo->prepare("SELECT DISTINCT c.name 
                                                                   FROM product_variants v 
                                                                   JOIN colors c ON v.color_id=c.id 
                                                                   WHERE v.product_id=?");
                                        $swatches->execute([$p['id']]);
                                        $swatches = $swatches->fetchAll();
                                        ?>
                                        <?php if ($swatches): ?>
                                        <div class="product-swatches mb-2">
                                            <?php foreach ($swatches as $sw): ?>
                                                <span class="swatch"  style="background-color: <?= htmlspecialchars($sw['name']) ?> " title="<?= htmlspecialchars($sw['name']) ?>"></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                        <!-- Stock indicator when less than 5 items -->
                                        <?php
                                        // Get total stock for this product
                                        $stockStmt = $pdo->prepare("
                                            SELECT SUM(stock) as total_stock
                                            FROM product_variants
                                            WHERE product_id = ?
                                        ");
                                        $stockStmt->execute([$p['id']]);
                                        $stockResult = $stockStmt->fetch();
                                        $totalStock = $stockResult['total_stock'] ?? 0;
                                        ?>
                                        <?php if ($totalStock > 0 && $totalStock < 5): ?>
                                            <div style="margin-top: 8px; padding: 7px 10px; background: #f0f8f5; border-left: 2px solid #2da06a; border-radius: 5px; font-size: 11px; font-weight: 600; color: #1a5e3d; text-align: center;">
                                                ⏱ Only <?= $totalStock ?> left
                                            </div>
                                        <?php elseif ($totalStock === 0): ?>
                                            <div style="margin-top: 8px; padding: 7px 10px; background: #f5f5f5; border-left: 2px solid #999; border-radius: 5px; font-size: 11px; font-weight: 600; color: #666; text-align: center;">
                                                Out of Stock
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </section>
</main>

<?php require_once "layouts/footer.php"; ?>
