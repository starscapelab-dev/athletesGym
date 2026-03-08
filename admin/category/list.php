<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Category";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . "/../includes/header.php";

// Fetch Category
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<div class="admin-page-header">
  <h1>Categories</h1>
  <a href="add.php" class="btn btn-primary">+ Add Category</a>
</div>

<?php if (isset($_GET['msg'])): ?>
  <div class="admin-message"><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>

<table class="admin-table">
  <thead>
    <tr>
      <th width="60">ID</th>
      <th>Image</th>
      <th>Name</th>
      <th>Gender</th>
      <th>Slug</th>
      <th width="160">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($categories as $category): ?>
      <tr>
        <td data-label="ID"><?= $category['id'] ?></td>
        <td data-label="Image"><?php if ($category['image']): ?><img src="../../<?= $category['image'] ?>" width="60"><?php endif; ?></td>
        <td data-label="Name"><?= sanitize($category['name']) ?></td>
        <td data-label="Gender"><?= sanitize($category['gender']) ?></td>
        <td data-label="Slug"><?= sanitize($category['slug']) ?></td>
        <td data-label="Actions">
          <div class="actions">
            <a href="edit.php?id=<?= $category['id'] ?>" class="btn btn-edit">Edit</a>
            <form action="delete.php" method="POST" style="display:inline; width: 100%;" onsubmit="return confirm('Delete this category?')">
              <?php csrfField(); ?>
              <input type="hidden" name="id" value="<?= $category['id'] ?>">
              <button type="submit" class="btn btn-delete" style="width: 100%;">Delete</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
