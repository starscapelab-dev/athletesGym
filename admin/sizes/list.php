<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Sizes";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . "/../includes/header.php";

// Fetch sizes - ordered by sort_order for proper display (S, M, L, XL, XXL)
$stmt = $pdo->query("SELECT * FROM sizes ORDER BY sort_order, name");
$sizes = $stmt->fetchAll();
?>

<div class="admin-page-header">
  <h1>Sizes</h1>
  <a href="add.php" class="btn btn-primary">+ Add Size</a>
</div>

<?php if (isset($_GET['msg'])): ?>
  <div class="admin-message"><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>

<table class="admin-table">
  <thead>
    <tr>
      <th width="60">ID</th>
      <th>Name</th>
      <th width="120">Display Order</th>
      <th width="160">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sizes as $size): ?>
      <tr>
        <td><?= $size['id'] ?></td>
        <td><?= sanitize($size['name']) ?></td>
        <td><?= $size['sort_order'] ?></td>
        <td>
          <div class="actions">
            <a href="edit.php?id=<?= $size['id'] ?>" class="btn btn-edit">Edit</a>
            <form action="delete.php" method="POST" style="display:inline; width: 100%;" onsubmit="return confirm('Delete this size?')">
              <?php csrfField(); ?>
              <input type="hidden" name="id" value="<?= $size['id'] ?>">
              <button type="submit" class="btn btn-delete" style="width: 100%;">Delete</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
