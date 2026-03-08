<?php
ob_start();
$pageTitle = "Edit Product";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php';
require_once __DIR__ . '/../../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);

// Get product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect("list.php?msg=Product+not+found");
}

// Get product images
$imagesStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY id");
$imagesStmt->execute([$id]);
$images = $imagesStmt->fetchAll();

// Get product variants with size and color names
$variantsStmt = $pdo->prepare("
    SELECT pv.*, s.name as size_name, c.name as color_name, c.hex_code
    FROM product_variants pv
    LEFT JOIN sizes s ON pv.size_id = s.id
    LEFT JOIN colors c ON pv.color_id = c.id
    WHERE pv.product_id = ?
    ORDER BY s.name, c.name
");
$variantsStmt->execute([$id]);
$variants = $variantsStmt->fetchAll();

// Get all sizes and colors for dropdowns
$sizes = $pdo->query("SELECT * FROM sizes ORDER BY name")->fetchAll();
$colors = $pdo->query("SELECT * FROM colors ORDER BY name")->fetchAll();

// Get categories
$cats = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// Calculate total stock
$totalStock = 0;
foreach ($variants as $variant) {
    $totalStock += $variant['stock'];
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    requireCsrfToken();
    $action = $_POST['action'];

    // Update basic product info
    if ($action === 'update_product') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $category_id = (int)$_POST['category_id'];
        $gender = $_POST['gender'] ?? 'Accessories';
        $price = floatval($_POST['price']);
        $track_stock = isset($_POST['track_stock']) ? 1 : 0;
        $low_stock_threshold = (int)$_POST['low_stock_threshold'];
        $featured = isset($_POST['featured']) ? 1 : 0;

        if ($name === '' || $price <= 0 || !$category_id) {
            $error = "Name, category, and price are required.";
        } else {
            try {
                $updateStmt = $pdo->prepare("UPDATE products SET
                    name=?, description=?, category_id=?, gender=?, price=?,
                    track_stock=?, low_stock_threshold=?, featured=?
                    WHERE id=?");
                $updateStmt->execute([$name, $description, $category_id, $gender, $price,
                    $track_stock, $low_stock_threshold, $featured, $id]);
                $success = "Product updated successfully!";

                // Refresh product data
                $stmt->execute([$id]);
                $product = $stmt->fetch();
            } catch (PDOException $e) {
                $error = "Error updating product: " . $e->getMessage();
            }
        }
    }

    // Upload new image
    elseif ($action === 'upload_image') {
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['product_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $new_filename = 'img_' . uniqid() . '.' . $ext;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                    $alt_text = trim($_POST['alt_text'] ?? '');
                    $insertImg = $pdo->prepare("INSERT INTO product_images (product_id, image_path, alt_text) VALUES (?, ?, ?)");
                    $insertImg->execute([$id, $new_filename, $alt_text]);
                    $success = "Image uploaded successfully!";

                    // Refresh images
                    $imagesStmt->execute([$id]);
                    $images = $imagesStmt->fetchAll();
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid file type. Allowed: " . implode(', ', $allowed);
            }
        } else {
            $error = "Please select an image to upload.";
        }
    }

    // Delete image
    elseif ($action === 'delete_image') {
        $image_id = (int)$_POST['image_id'];
        $imgStmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ? AND product_id = ?");
        $imgStmt->execute([$image_id, $id]);
        $img = $imgStmt->fetch();

        if ($img) {
            $file_path = __DIR__ . '/../../uploads/products/' . $img['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$image_id]);
            $success = "Image deleted successfully!";

            // Refresh images
            $imagesStmt->execute([$id]);
            $images = $imagesStmt->fetchAll();
        }
    }

    // Add variant
    elseif ($action === 'add_variant') {
        $size_id = (int)$_POST['size_id'];
        $color_id = (int)$_POST['color_id'];
        $sku = trim($_POST['sku']);
        $stock = (int)$_POST['stock'];
        $variant_price = !empty($_POST['variant_price']) ? floatval($_POST['variant_price']) : $product['price'];

        if (!$size_id || !$color_id) {
            $error = "Size and color are required.";
        } else {
            // Check if variant already exists
            $checkStmt = $pdo->prepare("SELECT id FROM product_variants WHERE product_id = ? AND size_id = ? AND color_id = ?");
            $checkStmt->execute([$id, $size_id, $color_id]);
            if ($checkStmt->fetch()) {
                $error = "This size/color combination already exists.";
            } else {
                $insertVar = $pdo->prepare("INSERT INTO product_variants (product_id, size_id, color_id, sku, stock, price) VALUES (?, ?, ?, ?, ?, ?)");
                $insertVar->execute([$id, $size_id, $color_id, $sku, $stock, $variant_price]);
                $success = "Variant added successfully!";

                // Refresh variants
                $variantsStmt->execute([$id]);
                $variants = $variantsStmt->fetchAll();

                // Recalculate total stock
                $totalStock = 0;
                foreach ($variants as $variant) {
                    $totalStock += $variant['stock'];
                }
            }
        }
    }

    // Update variant
    elseif ($action === 'update_variant') {
        $variant_id = (int)$_POST['variant_id'];
        $sku = trim($_POST['sku']);
        $stock = (int)$_POST['stock'];
        $variant_price = !empty($_POST['variant_price']) ? floatval($_POST['variant_price']) : null;

        $updateVar = $pdo->prepare("UPDATE product_variants SET sku = ?, stock = ?, price = ? WHERE id = ? AND product_id = ?");
        $updateVar->execute([$sku, $stock, $variant_price, $variant_id, $id]);
        $success = "Variant updated successfully!";

        // Refresh variants
        $variantsStmt->execute([$id]);
        $variants = $variantsStmt->fetchAll();

        // Recalculate total stock
        $totalStock = 0;
        foreach ($variants as $variant) {
            $totalStock += $variant['stock'];
        }
    }

    // Delete variant
    elseif ($action === 'delete_variant') {
        $variant_id = (int)$_POST['variant_id'];
        $pdo->prepare("DELETE FROM product_variants WHERE id = ? AND product_id = ?")->execute([$variant_id, $id]);
        $success = "Variant deleted successfully!";

        // Refresh variants
        $variantsStmt->execute([$id]);
        $variants = $variantsStmt->fetchAll();

        // Recalculate total stock
        $totalStock = 0;
        foreach ($variants as $variant) {
            $totalStock += $variant['stock'];
        }
    }
}

require_once __DIR__ . "/../includes/header.php";
?>

<div class="page-header">
    <h1>Edit Product: <?= sanitize($product['name']) ?></h1>
    <div class="page-actions">
        <a href="list.php" class="btn btn-secondary">← Back to Products</a>
        <a href="<?= BASE_URL ?>products/<?= $product['id'] ?>" class="btn btn-secondary" target="_blank">View on Site</a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= sanitize($success) ?></div>
<?php endif; ?>

<!-- Product Stock Overview -->
<div class="product-overview">
    <div class="overview-card">
        <div class="overview-icon">📦</div>
        <div class="overview-content">
            <div class="overview-label">Total Stock</div>
            <div class="overview-value <?= $totalStock <= ($product['low_stock_threshold'] ?? 5) ? 'low-stock' : '' ?>">
                <?= $totalStock ?> units
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="overview-icon">🏷️</div>
        <div class="overview-content">
            <div class="overview-label">Base Price</div>
            <div class="overview-value">QAR <?= number_format($product['price'], 2) ?></div>
        </div>
    </div>
    <div class="overview-card">
        <div class="overview-icon">🎨</div>
        <div class="overview-content">
            <div class="overview-label">Variants</div>
            <div class="overview-value"><?= count($variants) ?></div>
        </div>
    </div>
    <div class="overview-card">
        <div class="overview-icon">🖼️</div>
        <div class="overview-content">
            <div class="overview-label">Images</div>
            <div class="overview-value"><?= count($images) ?></div>
        </div>
    </div>
</div>

<!-- Product Information Section -->
<div class="edit-section">
    <div class="section-header">
        <h2>Basic Information</h2>
    </div>
    <form method="POST" class="product-form">
        <?php csrfField(); ?>
        <input type="hidden" name="action" value="update_product">

        <div class="form-row">
            <div class="form-group">
                <label for="name">Product Name <span class="required">*</span></label>
                <input type="text" name="name" id="name" value="<?= sanitize($product['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Base Price (QAR) <span class="required">*</span></label>
                <input type="number" step="0.01" name="price" id="price" value="<?= sanitize($product['price']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4"><?= sanitize($product['description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category_id">Category <span class="required">*</span></label>
                <select name="category_id" id="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($cats as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id']==$product['category_id'] ? 'selected' : '' ?>>
                            <?= sanitize($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="gender">Gender/Type</label>
                <select name="gender" id="gender">
                    <option value="Accessories" <?= $product['gender']=='Accessories'?'selected':'' ?>>Accessories</option>
                    <option value="male" <?= $product['gender']=='male'?'selected':'' ?>>Male</option>
                    <option value="female" <?= $product['gender']=='female'?'selected':'' ?>>Female</option>
                    <option value="unisex" <?= $product['gender']=='unisex'?'selected':'' ?>>Unisex</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="low_stock_threshold">Low Stock Alert (units)</label>
                <input type="number" name="low_stock_threshold" id="low_stock_threshold"
                       value="<?= $product['low_stock_threshold'] ?? 5 ?>" min="0">
                <small class="form-hint">Alert when total stock falls below this number</small>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="track_stock" value="1"
                               <?= ($product['track_stock'] ?? 1) ? 'checked' : '' ?>>
                        <span>Track Stock Inventory</span>
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" value="1"
                               <?= ($product['featured'] ?? 0) ? 'checked' : '' ?>>
                        <span>Featured Product</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Product Info</button>
        </div>
    </form>
</div>

<!-- Product Images Section -->
<div class="edit-section">
    <div class="section-header">
        <h2>Product Images</h2>
        <button type="button" class="btn btn-secondary" onclick="toggleSection('upload-image-form')">+ Add Image</button>
    </div>

    <!-- Upload Form (Hidden by default) -->
    <form method="POST" enctype="multipart/form-data" class="upload-form" id="upload-image-form" style="display: none;">
        <?php csrfField(); ?>
        <input type="hidden" name="action" value="upload_image">

        <div class="form-row">
            <div class="form-group">
                <label for="product_image">Select Image <span class="required">*</span></label>
                <input type="file" name="product_image" id="product_image" accept="image/*" required>
                <small class="form-hint">Allowed: JPG, PNG, GIF, WEBP (Max 5MB)</small>
            </div>

            <div class="form-group">
                <label for="alt_text">Alt Text (Optional)</label>
                <input type="text" name="alt_text" id="alt_text" placeholder="Describe the image...">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Upload Image</button>
            <button type="button" class="btn btn-secondary" onclick="toggleSection('upload-image-form')">Cancel</button>
        </div>
    </form>

    <!-- Images Grid -->
    <?php if (empty($images)): ?>
        <p class="no-data">No images uploaded yet. Add images to showcase your product.</p>
    <?php else: ?>
        <div class="images-grid">
            <?php foreach ($images as $img): ?>
                <div class="image-card">
                    <img src="<?= BASE_URL ?>uploads/<?= $img['image_path'] ?>" alt="<?= sanitize($img['alt_text']) ?>" onerror="this.src='<?= BASE_URL ?>assets/images/placeholder.png'">
                    <div class="image-actions">
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this image?')">
                            <?php csrfField(); ?>
                            <input type="hidden" name="action" value="delete_image">
                            <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                            <button type="submit" class="btn-action btn-delete" title="Delete">🗑️</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Product Variants Section -->
<div class="edit-section">
    <div class="section-header">
        <h2>Product Variants (Size & Color Combinations)</h2>
        <button type="button" class="btn btn-primary" onclick="toggleSection('add-variant-form')">+ Add New Variant</button>
    </div>

    <!-- Add Variant Form (Hidden by default) -->
    <div id="add-variant-form" class="variant-form-card" style="display: none;">
        <form method="POST" class="variant-form-content">
            <?php csrfField(); ?>
            <input type="hidden" name="action" value="add_variant">

            <h3>Add New Variant</h3>

            <div class="variant-form-grid">
                <div class="form-group">
                    <label for="size_id">Size <span class="required">*</span></label>
                    <select name="size_id" id="size_id" required class="form-select">
                        <option value="">Select Size</option>
                        <?php foreach ($sizes as $size): ?>
                            <option value="<?= $size['id'] ?>"><?= sanitize($size['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="color_id">Color <span class="required">*</span></label>
                    <select name="color_id" id="color_id" required class="form-select">
                        <option value="">Select Color</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?= $color['id'] ?>" data-hex="<?= $color['hex_code'] ?>">
                                <?= sanitize($color['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity <span class="required">*</span></label>
                    <input type="number" name="stock" id="stock" value="0" min="0" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="variant_price">Price (QAR)</label>
                    <input type="number" step="0.01" name="variant_price" id="variant_price"
                           placeholder="<?= $product['price'] ?>" class="form-input">
                    <small class="form-hint">Leave empty to use base price (QAR <?= number_format($product['price'], 2) ?>)</small>
                </div>

                <div class="form-group">
                    <label for="sku">SKU (Optional)</label>
                    <input type="text" name="sku" id="sku" placeholder="e.g. PROD-S-BLK" class="form-input">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Variant</button>
                <button type="button" class="btn btn-secondary" onclick="toggleSection('add-variant-form')">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Variants Cards/Table -->
    <?php if (empty($variants)): ?>
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h3>No Variants Yet</h3>
            <p>Start by adding size and color combinations with their stock quantities.</p>
            <button type="button" class="btn btn-primary" onclick="toggleSection('add-variant-form')">Add Your First Variant</button>
        </div>
    <?php else: ?>
        <div class="variants-grid">
            <?php foreach ($variants as $variant): ?>
                <div class="variant-card">
                    <div class="variant-header">
                        <div class="variant-title">
                            <span class="variant-size"><?= sanitize($variant['size_name']) ?></span>
                            <span class="variant-divider">•</span>
                            <div class="variant-color-info">
                                <?php if ($variant['hex_code']): ?>
                                    <span class="color-swatch-large" style="background: <?= $variant['hex_code'] ?>"></span>
                                <?php endif; ?>
                                <span class="variant-color-name"><?= sanitize($variant['color_name']) ?></span>
                            </div>
                        </div>
                        <div class="variant-actions-top">
                            <button type="button" class="btn-icon" onclick="openEditModal(<?= $variant['id'] ?>, '<?= addslashes($variant['size_name']) ?>', '<?= addslashes($variant['color_name']) ?>', '<?= addslashes($variant['sku']) ?>', <?= $variant['stock'] ?>, <?= $variant['price'] ?? $product['price'] ?>)" title="Edit">
                                ✏️
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this variant?')">
                                <?php csrfField(); ?>
                                <input type="hidden" name="action" value="delete_variant">
                                <input type="hidden" name="variant_id" value="<?= $variant['id'] ?>">
                                <button type="submit" class="btn-icon btn-icon-danger" title="Delete">🗑️</button>
                            </form>
                        </div>
                    </div>

                    <div class="variant-details">
                        <div class="variant-detail-item">
                            <span class="detail-label">Stock</span>
                            <span class="stock-badge-large <?= $variant['stock'] <= 0 ? 'out-of-stock' : ($variant['stock'] <= 5 ? 'low-stock' : '') ?>">
                                <?= $variant['stock'] ?> units
                            </span>
                        </div>
                        <div class="variant-detail-item">
                            <span class="detail-label">Price</span>
                            <span class="detail-value">QAR <?= number_format($variant['price'] ?? $product['price'], 2) ?></span>
                        </div>
                        <?php if ($variant['sku']): ?>
                            <div class="variant-detail-item">
                                <span class="detail-label">SKU</span>
                                <span class="detail-value"><?= sanitize($variant['sku']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Edit Variant Modal -->
<div id="edit-modal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeEditModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Variant</h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">×</button>
        </div>
        <form method="POST" id="edit-variant-form">
            <?php csrfField(); ?>
            <input type="hidden" name="action" value="update_variant">
            <input type="hidden" name="variant_id" id="edit_variant_id">

            <div class="modal-body">
                <div class="form-group">
                    <label>Size & Color</label>
                    <div class="variant-display" id="edit_variant_display"></div>
                </div>

                <div class="variant-form-grid">
                    <div class="form-group">
                        <label for="edit_stock">Stock Quantity <span class="required">*</span></label>
                        <input type="number" name="stock" id="edit_stock" min="0" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="edit_variant_price">Price (QAR)</label>
                        <input type="number" step="0.01" name="variant_price" id="edit_variant_price" class="form-input">
                    </div>

                    <div class="form-group full-width">
                        <label for="edit_sku">SKU</label>
                        <input type="text" name="sku" id="edit_sku" class="form-input">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section.style.display === 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

function openEditModal(variantId, sizeName, colorName, sku, stock, price) {
    document.getElementById('edit_variant_id').value = variantId;
    document.getElementById('edit_stock').value = stock;
    document.getElementById('edit_variant_price').value = price;
    document.getElementById('edit_sku').value = sku || '';
    document.getElementById('edit_variant_display').innerHTML =
        '<span class="variant-size">' + sizeName + '</span>' +
        '<span class="variant-divider">•</span>' +
        '<span class="variant-color-name">' + colorName + '</span>';
    document.getElementById('edit-modal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>

<?php require_once "../includes/footer.php"; ?>
