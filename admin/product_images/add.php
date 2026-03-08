<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Add Product Image";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$product_id = (int)($_GET['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
ob_end_clean(); // clear any previous output
header("Location: " . BASE_URL . "admin/product_images/list.php?msg=Product+not+found");
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alt_text = trim($_POST['alt_text']);
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        $error = "Please select an image.";
    } else {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $imgName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            die("Invalid image file type.");
        }

        // 3. Unique filename (to avoid overwriting)
        $uniqueName = uniqid('img_', true) . '.' . $ext;

        // 4. Destination
        $destPath = __DIR__ . "/../../uploads/" . $uniqueName;

        // 5. Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
            // Save $uniqueName in your DB as product image
            // Example: INSERT INTO product_images (product_id, image_path) VALUES (?, ?)
            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, alt_text) VALUES (?,?,?)");
            $stmt->execute([$product_id, $uniqueName, $alt_text]);
            ob_end_clean(); // clear any previous output
            header("Location: " . BASE_URL . "admin/product_images/list.php?product_id=$product_id&msg=Image+added");
        } else {
            echo "Failed to move uploaded file.";
        }
    }
}

require_once __DIR__ . "/../includes/header.php";
?>
<h1>Add Image for <?= sanitize($product['name']) ?></h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="admin-form">

<div class="form-group">
  <label for="image">Image</label>
  <input type="file" name="image" id="image" required>
</div>

<div class="form-group">
  <label for="alt_text">Alt Text</label>
  <input type="text" name="alt_text" id="alt_text">
</div>

<div class="form-actions">
  <button type="submit" class="btn btn-primary">Upload</button>
  <a href="list.php?product_id=<?= $product_id ?>" class="btn btn-secondary">Cancel</a>
</div>
</form>
<?php require_once "../includes/footer.php"; ?>
