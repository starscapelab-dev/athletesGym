<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Product Images";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$product_id = (int)($_GET['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product){
  ob_end_clean(); // clear any previous output
  header("Location: " . BASE_URL . "admin/products/list.php?msg=Product+not+found");
  }

$images = $pdo->prepare("SELECT * FROM product_images WHERE product_id=? ORDER BY id DESC");
$images->execute([$product_id]);
$images = $images->fetchAll();
?>
<div class="admin-page-header">
  <h1>Images for <?= sanitize($product['name']) ?></h1>
  <a href="add.php?product_id=<?= $product_id ?>" class="btn btn-primary">+ Add Image</a>
  <a href="../products/list.php" class="btn btn-secondary">Back to Products</a>
</div>
<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Alt Text</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($images as $img): ?>
      <tr>
        <td><?= $img['id'] ?></td>
        <td>
          <img src="<?= BASE_URL ?>uploads/<?= sanitize($img['image_path']) ?>" style="max-height:60px;">
        </td>
        <td><?= sanitize($img['alt_text']) ?></td>
        <td>
          <div class="actions">
            <a href="edit.php?id=<?= $img['id'] ?>&product_id=<?= $product_id ?>" class="btn btn-edit">Edit</a>
            <a href="delete.php?id=<?= $img['id'] ?>&product_id=<?= $product_id ?>" class="btn btn-delete" onclick="return confirm('Delete this image?')">Delete</a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once "../includes/footer.php"; ?>
