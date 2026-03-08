<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "Color";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . "/../includes/header.php";

// Fetch Color
$stmt = $pdo->query("SELECT * FROM colors ORDER BY id DESC");
$colors = $stmt->fetchAll();
?>

<div class="admin-page-header">
  <h1>Color</h1>
  <a href="add.php" class="btn btn-primary">+ Add Color</a>
</div>

<?php if (isset($_GET['msg'])): ?>
  <div class="admin-message"><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>

<table class="admin-table">
  <thead>
    <tr>
      <th width="60">ID</th>
      <th>Name</th>
      <th width="160">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($colors as $color): ?>
      <tr>
        <td><?= $color['id'] ?></td>
        <td><?= sanitize($color['name']) ?></td>
        <td>
          <div class="actions">
            <a href="edit.php?id=<?= $color['id'] ?>" class="btn btn-edit">Edit</a>
            <form action="delete.php" method="POST" style="display:inline; width: 100%;" onsubmit="return confirm('Delete this Color?')">
              <?php csrfField(); ?>
              <input type="hidden" name="id" value="<?= $color['id'] ?>">
              <button type="submit" class="btn btn-delete" style="width: 100%;">Delete</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
