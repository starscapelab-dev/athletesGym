<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Product Variants";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$product_id = (int)($_GET['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product){
  ob_end_clean(); // clear any previous output
  header("Location: " . BASE_URL . "admin/products/list.php?msg=Product+not+found");
  }

$variants = $pdo->prepare(
    "SELECT pv.*, s.name AS size_name, c.name AS color_name
     FROM product_variants pv
     LEFT JOIN sizes s ON pv.size_id = s.id
     LEFT JOIN colors c ON pv.color_id = c.id
     WHERE pv.product_id=? ORDER BY pv.id DESC"
);
$variants->execute([$product_id]);
$variants = $variants->fetchAll();
?>
<div class="admin-page-header">
  <h1>Variants for <?= sanitize($product['name']) ?></h1>
  <a href="add.php?product_id=<?= $product_id ?>" class="btn btn-primary">+ Add Variant</a>
  <a href="../products/list.php" class="btn btn-secondary">Back to Products</a>
</div>
<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Size</th>
      <th>Color</th>
      <th>SKU</th>
      <th>Stock</th>
      <th>Price</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($variants as $v): ?>
      <tr>
        <td><?= $v['id'] ?></td>
        <td><?= sanitize($v['size_name']) ?></td>
        <td><?= sanitize($v['color_name']) ?></td>
        <td><?= sanitize($v['sku']) ?></td>
        <td><?= $v['stock'] ?></td>
        <td><?= number_format($v['price'],2) ?></td>
        <td>
          <div class="actions">
            <a href="edit.php?id=<?= $v['id'] ?>&product_id=<?= $product_id ?>" class="btn btn-edit">Edit</a>
            <a href="delete.php?id=<?= $v['id'] ?>&product_id=<?= $product_id ?>" class="btn btn-delete" onclick="return confirm('Delete this variant?')">Delete</a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once "../includes/footer.php"; ?>
