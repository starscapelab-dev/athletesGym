<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Products";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . "/../includes/header.php";

$stmt = $pdo->query("SELECT p.*, c.name AS category_name,
                     (SELECT pi.image_path FROM product_images pi
                      WHERE pi.product_id = p.id
                      ORDER BY pi.id ASC LIMIT 1) as thumbnail
                     FROM products p
                     LEFT JOIN categories c ON p.category_id=c.id
                     ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();

?>
<div class="admin-page-header">
  <h1>Products</h1>
  <div class="header-actions">
    <a href="import_csv.php" class="btn btn-secondary" style="margin-right: 10px;">
      <i class="fas fa-file-import"></i> Import CSV
    </a>
    <a href="export_csv.php" class="btn btn-secondary" style="margin-right: 10px;">
      <i class="fas fa-file-export"></i> Export CSV
    </a>
    <a href="add.php" class="btn btn-primary">+ Add Product</a>
  </div>
</div>

<style>
.admin-page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
}
.header-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.btn-secondary {
  background-color: #6c757d;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}
.btn-secondary:hover {
  background-color: #5a6268;
  color: white;
}
.btn-secondary i {
  font-size: 14px;
}
</style>

<table class="admin-table">
  <thead>
    <tr>
      <th width="60">ID</th>
      <th width="80">Image</th>
      <th>Name</th>
      <th>Category</th>
      <th>Gender</th>
      <th>Price</th>
      <th width="180">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td data-label="ID"><?= $p['id'] ?></td>
        <td data-label="Image">
          <?php if ($p['thumbnail']): ?>
            <img src="<?= BASE_URL ?>uploads/<?= $p['thumbnail'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-thumbnail">
          <?php else: ?>
            <div class="product-thumbnail-placeholder">No Image</div>
          <?php endif; ?>
        </td>
        <td data-label="Name"><?= $p['name'] ?></td>
        <td data-label="Category"><?= $p['category_name'] ?></td>
        <td data-label="Gender"><?= $p['gender'] ?></td>
        <td data-label="Price"><?= number_format($p['price'],2) ?> QAR</td>
        <td data-label="Actions">
          <div class="actions">
            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-edit">Edit</a>
            <form method="post" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this product?')">
              <?php csrfField(); ?>
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-delete">Delete</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
