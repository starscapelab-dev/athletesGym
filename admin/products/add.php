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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $gender = $_POST['gender'];
    $price = floatval($_POST['price']);

    if ($name === '' || $price <= 0 || !$category_id) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, category_id, gender, price) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $description, $category_id, $gender, $price]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/products/list.php");
    }
}

$cats = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();


require_once __DIR__ . "/../includes/header.php";
?>
<h1>Add Product</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">
<?php csrfField(); ?>
<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" required>
</div>
<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description"></textarea>
</div>
<div class="form-group">
  <label for="category_id">Category</label>
  <select name="category_id" id="category_id" required>
    <option value="">-- Select Category --</option>
    <?php foreach ($cats as $cat): ?>
      <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>
  <div class="form-group">
  <label for="gender">Gender</label>
  <select name="gender" id="gender">
    <option value="male">Male</option>
    <option value="female">Female</option>
    <option value="Accessories">Accessories</option>
  </select>
  </div>
  <div class="form-group">
  <label for="price">Price</label>
  <input type="number" step="0.01" name="price" id="price" required>
  </div>

<div class="form-actions">
  <button type="submit" class="btn btn-primary">Add Product</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
</div>
</form>
<?php require_once "../includes/footer.php"; ?>
