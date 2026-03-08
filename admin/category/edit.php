<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "Edit Category";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$id]);
$categories = $stmt->fetch();

if (!$categories) {
    ob_end_clean(); // clear any previous output
    header("Location: " . BASE_URL . "admin/category/list.php");
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    $gender = $_POST['gender'] ?? 'Accessories';

    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));


    $imagePath = $categories['image'] ; // keep old image if no new upload



    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/../../uploads/categories/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $fileName = "cat_" . $id . "." . $ext; // ✅ always same name based on category ID
        $targetPath = $uploadDir . $fileName;

        // ✅ Delete old file if extension changed
        if (!empty($categories['image'])) {
            $oldFile = __DIR__ . "/../../" . $category['image'];
            if (file_exists($oldFile) && $oldFile !== $targetPath) {
                unlink($oldFile);
            }
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = "uploads/categories/" . $fileName;
        } else {
            $error = "❌ Upload failed. Check folder permissions.";
        }
    } 

    if ($name === '') {
        $error = "Category name is required.";
    } else {
        $stmt = $pdo->prepare("UPDATE categories SET name=?, gender=?, slug=?, image=? WHERE id=?");
        $stmt->execute([$name, $gender, $slug, $imagePath, $id]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/category/list.php");
        // redirect("list.php?msg=Category+updated");
    }
}

require_once __DIR__ . "/../includes/header.php";
?>

<h1>Edit Category</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form" enctype="multipart/form-data">
<?php csrfField(); ?>
<div class="form-group">

  <label for="name">Category Name</label>
  <input type="text" name="name" id="name" value="<?= sanitize($categories['name']) ?>" required>
</div>

  <div class="form-group">

  <label for="gender">Category Type</label>
  <select name="gender" id="gender" required>
      <option value="Men" <?= $categories['gender'] === 'Men' ? 'selected' : '' ?>>Men</option>
      <option value="Women" <?= $categories['gender'] === 'Women' ? 'selected' : '' ?>>Women</option>
      <option value="Accessories" <?= $categories['gender'] === 'Accessories' ? 'selected' : '' ?>>Accessories (Unisex)</option>
  </select>
  </div>

  <div class="form-group">

  <label for="image">Category Image</label>
  <?php if ($categories['image']): ?>
    <div><img src="../../<?= $categories['image'] ?>" alt="" width="120"></div>
  <?php endif; ?>
  <input type="file" name="image" id="image" accept="image/*">
  </div>

  <div class="form-actions">

  <button type="submit" class="btn btn-primary">Update Category</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>

<?php require_once "../includes/footer.php"; ?>
