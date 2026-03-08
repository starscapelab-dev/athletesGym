<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Edit Product Variant";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$id = (int)($_GET['id'] ?? 0);
$product_id = (int)($_GET['product_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM product_variants WHERE id=? AND product_id=?");
$stmt->execute([$id, $product_id]);
$variant = $stmt->fetch();
if (!$variant) {
  ob_end_clean(); // clear any previous output
  header("Location: " . BASE_URL . "admin/products_variants/list.php?msg=Variant+not+found");
  }

$sizes = $pdo->query("SELECT * FROM sizes ORDER BY name")->fetchAll();
$colors = $pdo->query("SELECT * FROM colors ORDER BY name")->fetchAll();

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $size_id = (int)($_POST['size_id'] ?? null);
    $color_id = (int)($_POST['color_id'] ?? null);
    $sku = trim($_POST['sku']);
    $stock = (int)$_POST['stock'];
    $price = floatval($_POST['price']);
    if ($price < 0) $error = "Price can't be negative.";
    else {
        $stmt = $pdo->prepare("UPDATE product_variants SET size_id=?, color_id=?, sku=?, stock=?, price=? WHERE id=?");
        $stmt->execute([$size_id, $color_id, $sku, $stock, $price, $id]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/product_variants/list.php?product_id=$product_id&msg=Variant+updated");
    }
}
require_once __DIR__ . "/../includes/header.php";
?>
<h1>Edit Variant</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">
  <label for="size_id">Size</label>
  <select name="size_id" id="size_id">
    <option value="">-- Select --</option>
    <?php foreach ($sizes as $s): ?>
      <option value="<?= $s['id'] ?>" <?= $s['id'] == $variant['size_id'] ? 'selected' : '' ?>><?= sanitize($s['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <label for="color_id">Color</label>
  <select name="color_id" id="color_id">
    <option value="">-- Select --</option>
    <?php foreach ($colors as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $c['id'] == $variant['color_id'] ? 'selected' : '' ?>><?= sanitize($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <label for="sku">SKU</label>
  <input type="text" name="sku" id="sku" value="<?= sanitize($variant['sku']) ?>">
  <label for="stock">Stock</label>
  <input type="number" name="stock" id="stock" min="0" value="<?= $variant['stock'] ?>">
  <label for="price">Price</label>
  <input type="number" step="0.01" name="price" id="price" value="<?= $variant['price'] ?>" required>
  <button type="submit" class="btn btn-primary">Update Variant</button>
  <a href="list.php?product_id=<?= $product_id ?>" class="btn btn-secondary">Cancel</a>
</form>
<?php require_once "../includes/footer.php"; ?>
