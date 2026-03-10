<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Add Product";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $gender = $_POST['gender'];
    $price = floatval($_POST['price']);
    $track_stock = isset($_POST['track_stock']) ? 1 : 0;
    $low_stock_threshold = (int)($_POST['low_stock_threshold'] ?? 5);
    $featured = isset($_POST['featured']) ? 1 : 0;

    if ($name === '' || $price <= 0 || !$category_id) {
        $error = "All fields are required.";
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Create product
            $stmt = $pdo->prepare("INSERT INTO products (name, description, category_id, gender, price, track_stock, low_stock_threshold, featured) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$name, $description, $category_id, $gender, $price, $track_stock, $low_stock_threshold, $featured]);
            $product_id = $pdo->lastInsertId();
            
            // Add variants if provided
            if (!empty($_POST['variants'])) {
                $variantStmt = $pdo->prepare("INSERT INTO product_variants (product_id, size_id, color_id, sku, stock, price) VALUES (?, ?, ?, ?, ?, ?)");
                
                foreach ($_POST['variants'] as $variant) {
                    $size_id = (int)($variant['size_id'] ?? 0);
                    $color_id = (int)($variant['color_id'] ?? 0);
                    $sku = trim($variant['sku'] ?? '');
                    $stock = (int)($variant['stock'] ?? 0);
                    $variant_price = !empty($variant['price']) ? floatval($variant['price']) : null;
                    
                    // Only add if size and color are selected
                    if ($size_id && $color_id) {
                        $variantStmt->execute([$product_id, $size_id, $color_id, $sku, $stock, $variant_price]);
                    }
                }
            }
            
            // Handle image uploads
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (isset($_FILES['product_images']) && is_array($_FILES['product_images']['name'])) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $imageStmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, alt_text) VALUES (?, ?, ?)");
                
                for ($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
                    if ($_FILES['product_images']['error'][$i] === 0) {
                        $filename = $_FILES['product_images']['name'][$i];
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        
                        if (in_array($ext, $allowed)) {
                            $new_filename = 'img_' . uniqid() . '.' . $ext;
                            $upload_path = $upload_dir . $new_filename;
                            
                            if (move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $upload_path)) {
                                $alt_text = trim($_POST['image_alt'][$i] ?? $name);
                                $imageStmt->execute([$product_id, $new_filename, $alt_text]);
                            }
                        }
                    }
                }
            }
            
            // Commit transaction
            $pdo->commit();
            
            ob_end_clean(); // clear any previous output
            header("Location: " . BASE_URL . "admin/products/edit.php?id=" . $product_id . "&msg=Product+created+successfully");
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error adding product: " . $e->getMessage();
        }
    }
}

$cats = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$sizes = $pdo->query("SELECT id, name FROM sizes ORDER BY name")->fetchAll();
$colors = $pdo->query("SELECT id, name, hex_code FROM colors ORDER BY name")->fetchAll();


require_once __DIR__ . "/../includes/header.php";
?>

<style>
.add-product-container {
    max-width: 1400px;
}

.product-form-wrapper {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    margin-top: 20px;
}

.form-main {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.form-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.edit-section {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
}

.section-header {
    margin-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.section-header h2 {
    margin: 0;
    font-size: 1.2em;
    color: #333;
}

.section-description {
    margin: 8px 0 0 0;
    font-size: 0.9em;
    color: #666;
}

.form-grid-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-grid-full {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 500;
    color: #333;
    font-size: 0.95em;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.95em;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: 500;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-hint {
    font-size: 0.85em;
    color: #666;
    margin-top: 4px;
}

.required {
    color: #dc3545;
}

/* Image Upload Section */
.image-upload-section {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
}

.image-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 25px;
    text-align: center;
    background: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-upload-area:hover {
    border-color: #007bff;
    background: #f0f7ff;
}

.image-upload-area.drag-over {
    border-color: #007bff;
    background: #e7f3ff;
}

.upload-icon {
    font-size: 2.5em;
    margin-bottom: 10px;
}

.upload-text {
    margin: 0;
    color: #666;
}

.upload-text strong {
    color: #007bff;
}

.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-top: 15px;
}

.image-preview-item {
    position: relative;
    border-radius: 6px;
    overflow: hidden;
    background: #f0f0f0;
    aspect-ratio: 1;
}

.image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-preview-item .remove-btn {
    position: absolute;
    top: 0;
    right: 0;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    padding: 0;
    cursor: pointer;
    font-size: 1.2em;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-preview-item .remove-btn:hover {
    background: #dc3545;
}

.placeholder-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    color: #999;
    font-size: 2.5em;
}

/* Variants Section */
.variant-row {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr auto;
    gap: 10px;
    align-items: flex-end;
}

.variant-row .form-group {
    margin: 0;
}

.variant-row label {
    font-size: 0.85rem;
}

.variant-row input,
.variant-row select {
    width: 100%;
}

.remove-variant-btn {
    padding: 8px 12px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
}

.remove-variant-btn:hover {
    background: #c82333;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 0.95em;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.alert {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

@media (max-width: 1024px) {
    .product-form-wrapper {
        grid-template-columns: 1fr;
    }
    
    .form-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .form-grid-2col {
        grid-template-columns: 1fr;
    }
    
    .variant-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="add-product-container">
    <div class="page-header">
        <h1>Add New Product</h1>
        <div class="page-actions">
            <a href="list.php" class="btn btn-secondary">← Back to Products</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= sanitize($success) ?></div>
    <?php endif; ?>

    <form method="post" class="product-form" enctype="multipart/form-data">
        <?php csrfField(); ?>
        
        <div class="product-form-wrapper">
            <!-- Main Form Content -->
            <div class="form-main">
                
                <!-- Basic Information Section -->
                <div class="edit-section">
                    <div class="section-header">
                        <h2>Basic Information</h2>
                    </div>

                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label for="name">Product Name <span class="required">*</span></label>
                            <input type="text" name="name" id="name" placeholder="e.g. Premium Sports T-Shirt" required>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category <span class="required">*</span></label>
                            <select name="category_id" id="category_id" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($cats as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" placeholder="Enter detailed product description..."></textarea>
                    </div>

                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label for="gender">Gender/Type</label>
                            <select name="gender" id="gender">
                                <option value="Accessories">Accessories</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="unisex">Unisex</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">Base Price (QAR) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" placeholder="0.00" required>
                        </div>
                    </div>
                </div>

                <!-- Stock Management Section -->
                <div class="edit-section">
                    <div class="section-header">
                        <h2>Stock Management</h2>
                    </div>

                    <div class="form-grid-full">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="track_stock" id="track_stock" value="1" checked>
                                <span>Track Stock Inventory</span>
                            </label>
                            <small class="form-hint">Enable inventory tracking for this product</small>
                        </div>

                        <div class="form-group">
                            <label for="low_stock_threshold">Low Stock Threshold (units)</label>
                            <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="5" min="0" placeholder="5">
                            <small class="form-hint">Alert when total stock falls below this number</small>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" id="featured" value="1">
                                <span>Featured Product</span>
                            </label>
                            <small class="form-hint">Highlight this product on the homepage</small>
                        </div>
                    </div>
                </div>

                <!-- Product Variants Section -->
                <div class="edit-section">
                    <div class="section-header">
                        <h2>Product Variants</h2>
                        <p class="section-description">Add size and color combinations with stock and SKU information</p>
                    </div>

                    <div id="variants-container">
                        <!-- Variants will be added here dynamically -->
                    </div>

                    <button type="button" class="btn btn-secondary" onclick="addVariantRow()">+ Add Variant</button>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">✓ Create Product</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>

            </div>

            <!-- Sidebar - Images Upload -->
            <div class="form-sidebar">
                <div class="image-upload-section">
                    <div class="section-header" style="margin-bottom: 15px;">
                        <h2 style="font-size: 1.1em;">Product Images</h2>
                    </div>

                    <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('product_images').click()">
                        <div class="upload-icon">🖼️</div>
                        <p class="upload-text"><strong>Click to upload</strong> or drag and drop</p>
                        <p style="font-size: 0.85em; color: #999; margin: 5px 0 0 0;">JPG, PNG, GIF or WebP (max 5MB each)</p>
                    </div>

                    <input type="file" id="product_images" name="product_images[]" multiple accept="image/*" style="display: none;">

                    <div class="image-preview-grid" id="imagePreviewGrid">
                        <!-- Image previews will appear here -->
                    </div>

                    <p style="font-size: 0.85em; color: #666; margin-top: 10px; text-align: center;">
                        You can add more images after creating the product
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let variantCount = 0;
const uploadArea = document.getElementById('imageUploadArea');
const fileInput = document.getElementById('product_images');
const previewGrid = document.getElementById('imagePreviewGrid');

// Drag and drop functionality
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    fileInput.files = e.dataTransfer.files;
    handleImagePreview();
});

fileInput.addEventListener('change', handleImagePreview);

function handleImagePreview() {
    previewGrid.innerHTML = '';
    
    Array.from(fileInput.files).forEach((file, index) => {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const preview = document.createElement('div');
            preview.className = 'image-preview-item';
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Product image ${index + 1}">
                <button type="button" class="remove-btn" onclick="removeImagePreview(${index})" title="Remove">×</button>
            `;
            previewGrid.appendChild(preview);
        };
        
        reader.readAsDataURL(file);
    });
}

function removeImagePreview(index) {
    const dataTransfer = new DataTransfer();
    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dataTransfer.items.add(file);
        }
    });
    fileInput.files = dataTransfer.files;
    handleImagePreview();
}

function addVariantRow() {
    const container = document.getElementById('variants-container');
    variantCount++;
    
    const rowHTML = `
        <div class="variant-row" id="variant-row-${variantCount}">
            <div class="form-group">
                <label for="size_${variantCount}">Size</label>
                <select name="variants[${variantCount}][size_id]" id="size_${variantCount}">
                    <option value="">Select Size</option>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= $size['id'] ?>"><?= sanitize($size['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="color_${variantCount}">Color</label>
                <select name="variants[${variantCount}][color_id]" id="color_${variantCount}">
                    <option value="">Select Color</option>
                    <?php foreach ($colors as $color): ?>
                        <option value="<?= $color['id'] ?>"><?= sanitize($color['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stock_${variantCount}">Stock Qty</label>
                <input type="number" name="variants[${variantCount}][stock]" id="stock_${variantCount}" value="0" min="0" placeholder="0">
            </div>

            <div class="form-group">
                <label for="sku_${variantCount}">SKU</label>
                <input type="text" name="variants[${variantCount}][sku]" id="sku_${variantCount}" placeholder="e.g. PROD-S-RED">
            </div>

            <div class="form-group">
                <label for="vprice_${variantCount}">Price (QAR)</label>
                <input type="number" step="0.01" name="variants[${variantCount}][price]" id="vprice_${variantCount}" placeholder="Leave empty">
            </div>

            <button type="button" class="remove-variant-btn" onclick="removeVariantRow(${variantCount})">Remove</button>
        </div>
    `;
    
    container.innerHTML += rowHTML;
}

function removeVariantRow(id) {
    const row = document.getElementById(`variant-row-${id}`);
    if (row) {
        row.remove();
    }
}

// Add one empty variant row on page load
window.addEventListener('load', function() {
    const container = document.getElementById('variants-container');
    if (container.children.length === 0) {
        addVariantRow();
    }
});
</script>

<?php require_once "../includes/footer.php"; ?>
