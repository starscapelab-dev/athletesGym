<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Edit Product Image";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$id = (int)($_GET['id'] ?? 0);
$product_id = (int)($_GET['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE id=? AND product_id=?");
$stmt->execute([$id, $product_id]);
$img = $stmt->fetch();
if (!$img){
  ob_end_clean(); // clear any previous output
  header("Location: " . BASE_URL . "admin/product_images/list.php?msg=Variant+not+found");
  }

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alt_text = trim($_POST['alt_text']);
    $stmt = $pdo->prepare("UPDATE product_images SET alt_text=? WHERE id=?");
    $stmt->execute([$alt_text, $id]);
    ob_end_clean(); // clear any previous output
    header("Location: " . BASE_URL . "admin/product_images/list.php?product_id=$product_id&msg=Image+updated");
}

require_once __DIR__ . "/../includes/header.php";
?>
<h1>Edit Image</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">

<div class="form-group">
  <img src="<?= BASE_URL ?>uploads/<?= sanitize($img['image_path']) ?>" style="width: 25%;margin-bottom:20px;">
</div>

<div class="form-group">
  <label for="alt_text">Alt Text</label>
  <input type="text" name="alt_text" id="alt_text" value="<?= sanitize($img['alt_text']) ?>">
</div>

<div class="form-actions">
  <button type="submit" class="btn btn-primary">Update</button>
  <a href="list.php?product_id=<?= $product_id ?>" class="btn btn-secondary">Cancel</a>
</div>
</form>
<?php require_once "../includes/footer.php"; ?>
