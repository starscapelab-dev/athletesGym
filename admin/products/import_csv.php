<?php
ob_start();
$pageTitle = "Import Products CSV";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . "/../includes/header.php";

// Check if user is admin
requireAdmin();

$errors = [];
$success = false;
$importedCount = 0;
$updatedCount = 0;
$skippedCount = 0;
$preview = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    requireCsrfToken();

    $file = $_FILES['csv_file'];

    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error. Please try again.";
    } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $errors[] = "File is too large. Maximum size is 5MB.";
    } elseif (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
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
                $errors[] = "Invalid CSV format. Required columns: Name, Description, Category ID, Price";
            } else {
                $rowNumber = 1;

                try {
                    $pdo->beginTransaction();

                    while (($data = fgetcsv($handle)) !== false) {
                        $rowNumber++;

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
                        $gender = isset($row['Gender']) ? trim($row['Gender']) : 'Unisex';
                        $type = isset($row['Type']) ? trim($row['Type']) : 'Standard';
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
            <strong>Import Successful!</strong><br>
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
            <strong>Import Errors:</strong>
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

        <div class="info-box" style="margin-bottom: 20px; padding: 15px; background: #f0f8ff; border-left: 4px solid #2196F3; border-radius: 4px;">
            <strong>CSV Format Requirements:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li><strong>Required columns:</strong> Name, Price</li>
                <li><strong>Optional columns:</strong> ID (for updates), Description, Category ID, Gender, Type, Stock Threshold</li>
                <li><strong>To update existing products:</strong> Include the product ID in the CSV</li>
                <li><strong>Gender options:</strong> Men, Women, Unisex</li>
                <li><strong>Type options:</strong> Standard, Premium, Limited Edition</li>
            </ul>
            <p style="margin-top: 10px;">
                <a href="export_csv.php" class="btn btn-small">Download Sample CSV</a>
                <span style="margin-left: 10px; color: #666;">(Export current products as reference)</span>
            </p>
        </div>

        <form method="POST" enctype="multipart/form-data" style="max-width: 600px;">
            <?php csrfField(); ?>

            <div class="form-group">
                <label for="csv_file">Select CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                <small>Maximum file size: 5MB</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Import Products</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>

<style>
.alert {
    padding: 15px 20px;
    border-radius: 4px;
    margin-bottom: 20px;
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
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.card h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}
.form-group input[type="file"] {
    display: block;
    width: 100%;
    padding: 10px;
    border: 2px dashed #ddd;
    border-radius: 4px;
    background: #f9f9f9;
    cursor: pointer;
}
.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.9em;
}
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}
.btn-small {
    padding: 6px 12px;
    font-size: 0.9em;
}
</style>

<?php require_once "../includes/footer.php"; ?>
