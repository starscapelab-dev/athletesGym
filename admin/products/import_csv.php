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

            if (!$headers || !in_array('Name', $headers)) {
                $errors[] = "Invalid CSV format. Required column: Name";
            } else {
                $rowNumber = 1;

                try {
                    $pdo->beginTransaction();

                    while (($data = fgetcsv($handle)) !== false) {
                        $rowNumber++;

                        // Skip empty rows
                        if (empty(array_filter($data))) {
                            continue;
                        }

                        // Map data to associative array
                        $row = array_combine($headers, $data);

                        // Validate required fields
                        if (empty($row['Name'])) {
                            $skippedCount++;
                            continue;
                        }

                        // Check if updating existing product
                        $productId = !empty($row['ID']) ? intval($row['ID']) : null;

                        // Prepare data
                        $name = trim($row['Name']);
                        $description = isset($row['Description']) ? trim($row['Description']) : '';
                        $categoryId = isset($row['Category ID']) && !empty($row['Category ID']) ? intval($row['Category ID']) : null;
                        $gender = isset($row['Gender']) && !empty($row['Gender']) ? trim($row['Gender']) : 'Unisex';
                        $type = isset($row['Type']) && !empty($row['Type']) ? trim($row['Type']) : 'Standard';
                        $price = isset($row['Price']) && !empty($row['Price']) ? floatval($row['Price']) : 0;
                        $stockThreshold = isset($row['Stock Threshold']) && !empty($row['Stock Threshold']) ? intval($row['Stock Threshold']) : 10;

                        // Validate price
                        if ($price <= 0) {
                            $errors[] = "Row $rowNumber: Invalid price for product '$name'";
                            $skippedCount++;
                            continue;
                        }

                        // Update existing product
                        if ($productId) {
                            $stmt = $pdo->prepare("
                                UPDATE products SET
                                    name = ?,
                                    description = ?,
                                    category_id = ?,
                                    gender = ?,
                                    type = ?,
                                    price = ?,
                                    stock_threshold = ?,
                                    updated_at = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([
                                $name,
                                $description,
                                $categoryId,
                                $gender,
                                $type,
                                $price,
                                $stockThreshold,
                                $productId
                            ]);

                            if ($stmt->rowCount() > 0) {
                                $updatedCount++;
                            } else {
                                $skippedCount++;
                            }
                        } else {
                            // Insert new product
                            $stmt = $pdo->prepare("
                                INSERT INTO products (name, description, category_id, gender, type, price, stock_threshold, created_at, updated_at)
                                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                            ");
                            $stmt->execute([
                                $name,
                                $description,
                                $categoryId,
                                $gender,
                                $type,
                                $price,
                                $stockThreshold
                            ]);
                            $importedCount++;
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
                <li><strong>Required columns:</strong> Name, Price</li>
                <li><strong>Optional columns:</strong> ID (for updates), Description, Category ID, Gender, Type, Stock Threshold</li>
                <li><strong>To update existing products:</strong> Include the product ID in the CSV</li>
                <li><strong>Gender options:</strong> Men, Women, Unisex</li>
                <li><strong>Type options:</strong> Standard, Premium, Limited Edition</li>
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
