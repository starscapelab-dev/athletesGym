<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Edit Size";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM sizes WHERE id=?");
$stmt->execute([$id]);
$size = $stmt->fetch();

if (!$size) {
    redirect("list.php?msg=Size+not+found");
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    $sort_order = (int)($_POST['sort_order'] ?? 999);
    if ($name === '') {
        $error = "Size name is required.";
    } else {
        $stmt = $pdo->prepare("UPDATE sizes SET name=?, sort_order=? WHERE id=?");
        $stmt->execute([$name, $sort_order, $id]);
        redirect("list.php?msg=Size+updated");
    }
}

require_once "../includes/header.php";
?>

<h1>Edit Size</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">
<?php csrfField(); ?>

<div class="form-group">
  <label for="name">Size Name</label>
  <input type="text" name="name" id="name" value="<?= sanitize($size['name']) ?>" required>
</div>

<div class="form-group">
  <label for="sort_order">Display Order</label>
  <input type="number" name="sort_order" id="sort_order" value="<?= $size['sort_order'] ?>" min="1" max="999" required>
  <small style="color:#666;">Lower numbers appear first (e.g., S=1, M=2, L=3, XL=4, XXL=5)</small>
</div>

<div class="form-actions">
  <button type="submit" class="btn btn-primary">Update Size</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
</div>
</form>

<?php require_once "../includes/footer.php"; ?>
