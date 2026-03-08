<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "Add Category";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    $gender = $_POST["gender"] ?? 'Accessories';
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));

    $imagePath = null;

    if ($name === '') {
        $error = "Category name is required.";
    }

    // Handle image upload if provided

    if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 10 * 1024 * 1024; // 2MB
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        $fileSize = $_FILES['image']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $error = "Only JPG, PNG, and WEBP images are allowed.";
        } elseif ($fileSize > $maxSize) {
            $error = "Image must be less than 2MB.";
        } else {
            $uploadDir = __DIR__ . "/../../uploads/categories/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid("cat_") . "." . $ext;
            $target = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = "uploads/categories/" . $filename;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    if ($name === '') {
        $error = "Category name is required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image, gender) VALUES (?,?,?,?)");
        $stmt->execute([$name, $slug, $imagePath, $gender]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/category/list.php");
    }
}

require_once __DIR__ . "/../includes/header.php";
?>

<h1>Add Category</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form" enctype="multipart/form-data">
<?php csrfField(); ?>
<div class="form-group">
  <label for="name">Category Name</label>
  <input type="text" name="name" id="name" required>
  </div>


  <div class="form-group">
  <label for="gender">Category Type</label>
  <select name="gender" id="gender" required>
      <option value="Men">Men</option>
      <option value="Women">Women</option>
      <option value="Accessories" selected>Accessories (Unisex)</option>
  </select>
  </div>


  <div class="form-group">
  <label for="image">Category Image</label>
  <input type="file" name="image" id="image" accept="image/*">
  </div>


  <div class="form-actions">
  <button type="submit" class="btn btn-primary">Add Category</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
  </div>

</form>

<?php require_once "../includes/footer.php"; ?>
