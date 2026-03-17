<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "Import Products CSV";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . "/../includes/header.php";

$errors = [];
$success = false;
$importedCount = 0;
$updatedCount = 0;
$skippedCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    requireCsrfToken();

    $file = $_FILES['csv_file'];

    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error. Please try again.";
    } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $errors[] = "File is too large. Maximum size is 5MB.";
    } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv') {
        $errors[] = "Invalid file type. Please upload a CSV file.";
    } else {
        // Process CSV file
        $handle = fopen($file['tmp_name'], 'r');

        if ($handle !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            // Read header row
            $headers = fgetcsv($handle);

            // Normalize headers (trim and convert to uppercase for comparison)
            $normalizedHeaders = array_map(function($h) { return strtoupper(trim($h)); }, $headers);

            // Required columns
            $requiredColumns = ['CODE', 'PRODUCT NAME', '1 UNIT COST (QR)'];
            $missingColumns = array_diff($requiredColumns, $normalizedHeaders);

            if (!empty($missingColumns)) {
                $errors[] = "Invalid CSV format. Missing required columns: " . implode(', ', $missingColumns);
            } else {
                $rowNumber = 1;

                try {
                    $pdo->beginTransaction();

                    // Track products and their variants
                    $productsToProcess = [];

                    // First pass: Group variants by product code
                    while (($data = fgetcsv($handle)) !== false) {
                        $rowNumber++;

                        // Skip empty rows
                        if (empty(array_filter($data))) {
                            continue;
                        }

                        // Map data to associative array
                        $row = array_combine($headers, $data);

                        // Extract and trim data
                        $code = !empty($row['CODE']) ? trim($row['CODE']) : null;
                        $productName = !empty($row['PRODUCT NAME']) ? trim($row['PRODUCT NAME']) : null;
                        $size = !empty($row['SIZE']) ? trim($row['SIZE']) : null;
                        $quantity = isset($row['QUANTITY']) ? intval($row['QUANTITY']) : 0;
                        $unitCost = !empty($row['1 UNIT COST (QR)']) ? floatval($row['1 UNIT COST (QR)']) : 0;
                        $category = !empty($row['CATEGORY']) ? strtolower(trim($row['CATEGORY'])) : 'unisex';

                        // Validate required fields
                        if (empty($productName) || $unitCost <= 0) {
                            $skippedCount++;
                            continue;
                        }

                        // Store product data
                        if (!isset($productsToProcess[$code])) {
                            $productsToProcess[$code] = [
                                'name' => $productName,
                                'price' => $unitCost,
                                'category' => $category,
                                'variants' => []
                            ];
                        }

                        // Add variant if size is provided
                        if (!empty($size)) {
                            $productsToProcess[$code]['variants'][] = [
                                'size' => $size,
                                'quantity' => $quantity,
                                'price' => $unitCost
                            ];
                        }
                    }

                    // Second pass: Process products
                    foreach ($productsToProcess as $code => $productData) {
                        $productName = $productData['name'];
                        $price = $productData['price'];
                        $gender = $productData['category'];

                        // Find or get default category (use first available)
                        $categoryStmt = $pdo->query("SELECT id FROM categories ORDER BY id LIMIT 1");
                        $categoryRow = $categoryStmt->fetch(PDO::FETCH_ASSOC);
                        $categoryId = $categoryRow ? $categoryRow['id'] : 1;

                        // Check if product exists by code
                        $checkStmt = $pdo->prepare("SELECT id FROM products WHERE id = ? OR name = ?");
                        $checkStmt->execute([$code, $productName]);
                        $existingProduct = $checkStmt->fetch(PDO::FETCH_ASSOC);

                        if ($existingProduct) {
                            // Update existing product
                            $productId = $existingProduct['id'];
                            $updateStmt = $pdo->prepare("
                                UPDATE products SET
                                    name = ?,
                                    price = ?,
                                    gender = ?,
                                    category_id = ?
                                WHERE id = ?
                            ");
                            $updateStmt->execute([$productName, $price, $gender, $categoryId, $productId]);

                            // Delete existing variants
                            $deleteVariantsStmt = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
                            $deleteVariantsStmt->execute([$productId]);

                            $updatedCount++;
                        } else {
                            // Insert new product
                            $insertStmt = $pdo->prepare("
                                INSERT INTO products (id, name, price, gender, category_id, active, created_at)
                                VALUES (?, ?, ?, ?, ?, 1, NOW())
                            ");
                            $insertStmt->execute([$code, $productName, $price, $gender, $categoryId]);
                            $productId = !empty($code) ? $code : $pdo->lastInsertId();
                            $importedCount++;
                        }

                        // Insert variants
                        if (!empty($productData['variants'])) {
                            $variantStmt = $pdo->prepare("
                                INSERT INTO product_variants (product_id, size_id, color_id, stock, price)
                                VALUES (?, ?, ?, ?, ?)
                            ");

                            // Get default color (usually Black, ID 1)
                            $colorId = 1;

                            foreach ($productData['variants'] as $variant) {
                                // Find or create size
                                $sizeStmt = $pdo->prepare("SELECT id FROM sizes WHERE UPPER(name) = UPPER(?)");
                                $sizeStmt->execute([$variant['size']]);
                                $sizeRow = $sizeStmt->fetch(PDO::FETCH_ASSOC);

                                if ($sizeRow) {
                                    $sizeId = $sizeRow['id'];
                                } else {
                                    // Create new size
                                    $insertSizeStmt = $pdo->prepare("INSERT INTO sizes (name) VALUES (?)");
                                    $insertSizeStmt->execute([$variant['size']]);
                                    $sizeId = $pdo->lastInsertId();
                                }

                                // Insert variant
                                $variantStmt->execute([
                                    $productId,
                                    $sizeId,
                                    $colorId,
                                    $variant['quantity'],
                                    $variant['price']
                                ]);
                            }
                        }
                    }

                    $pdo->commit();
                    $success = true;

                } catch (Exception $e) {
                    $pdo->rollBack();
                    $errors[] = "Database error: " . $e->getMessage();
                }
            }

            fclose($handle);
        } else {
            $errors[] = "Could not read the CSV file.";
        }
    }
}

?>

<div class="admin-page-header">
    <h1>Import Products from CSV</h1>
    <a href="list.php" class="btn btn-secondary">← Back to Products</a>
</div>

<div class="admin-content">

    <?php if ($success): ?>
        <div class="alert alert-success">
            <strong>✓ Import Successful!</strong><br><br>
            • <?= $importedCount ?> new products imported<br>
            • <?= $updatedCount ?> products updated<br>
            <?php if ($skippedCount > 0): ?>
                • <?= $skippedCount ?> rows skipped (invalid data or no changes)
            <?php endif; ?>
        </div>
        <div style="margin-top: 20px;">
            <a href="list.php" class="btn btn-primary">View Products</a>
            <a href="import_csv.php" class="btn btn-secondary">Import Another File</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>⚠ Import Errors:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <div class="card">
        <h2>Upload CSV File</h2>

        <div class="info-box" style="margin-bottom: 20px; padding: 15px; background: #e8f4f8; border-left: 4px solid #2196F3; border-radius: 4px;">
            <strong>📋 CSV Format Requirements:</strong>
            <ul style="margin: 10px 0 0 20px; line-height: 1.8;">
                <li><strong>Required columns:</strong> CODE, PRODUCT NAME, 1 UNIT COST (QR)</li>
                <li><strong>Optional columns:</strong> SIZE, QUANTITY, CATEGORY</li>
                <li><strong>Format:</strong> Each row represents one size variant of a product</li>
                <li><strong>Example:</strong> Product "JACKET" with sizes XL, L, M, S = 4 rows with same CODE</li>
                <li><strong>Category options:</strong> WOMENS, MENS, UNISEX, ACCESSORIES</li>
                <li><strong>To update existing products:</strong> Use the same CODE as existing product</li>
            </ul>
            <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #b3d9e8;">
                <a href="export_csv.php" class="btn btn-small" style="display: inline-block; padding: 8px 16px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">
                    ⬇ Download Sample CSV
                </a>
                <span style="margin-left: 10px; color: #666;">(Export current products as reference)</span>
            </p>
        </div>

        <form method="POST" enctype="multipart/form-data" style="max-width: 600px;">
            <?php csrfField(); ?>

            <div class="form-group">
                <label for="csv_file">Select CSV File *</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                <small style="color: #666;">Maximum file size: 5MB</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">📤 Import Products</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>

<style>
.admin-content {
    max-width: 900px;
    margin: 0 auto;
}
.alert {
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    line-height: 1.6;
}
.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}
.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
.card {
    background: white;
    padding: 35px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.card h2 {
    margin-top: 0;
    margin-bottom: 25px;
    color: #333;
    font-size: 24px;
}
.form-group {
    margin-bottom: 25px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 15px;
}
.form-group input[type="file"] {
    display: block;
    width: 100%;
    padding: 12px;
    border: 2px dashed #ccc;
    border-radius: 6px;
    background: #fafafa;
    cursor: pointer;
    transition: border-color 0.2s;
}
.form-group input[type="file"]:hover {
    border-color: #2196F3;
    background: #f0f8ff;
}
.form-group small {
    display: block;
    margin-top: 6px;
    color: #666;
    font-size: 13px;
}
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}
.btn {
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}
.btn-primary {
    background: #2196F3;
    color: white;
}
.btn-primary:hover {
    background: #1976D2;
}
.btn-secondary {
    background: #6c757d;
    color: white;
}
.btn-secondary:hover {
    background: #5a6268;
}
.btn-small {
    padding: 8px 16px;
    font-size: 14px;
}
</style>

<?php require_once "../includes/footer.php"; ?>
