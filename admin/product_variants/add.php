<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Add Product Variant";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$product_id = (int)($_GET['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
  ob_end_clean(); // clear any previous output
  header("Location: " . BASE_URL . "admin/product_variants/list.php?msg=Variant+not+found");
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
        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, size_id, color_id, sku, stock, price) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$product_id, $size_id, $color_id, $sku, $stock, $price]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/product_variants/list.php?product_id=$product_id&msg=Variant+added");
    }
}
require_once __DIR__ . "/../includes/header.php";
?>
<h1>Add Variant for <?= sanitize($product['name']) ?></h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">

<div class="form-group">
  <label for="size_id">Size</label>
  <select name="size_id" id="size_id">
    <option value="">-- Select --</option>
    <?php foreach ($sizes as $s): ?>
      <option value="<?= $s['id'] ?>"><?= sanitize($s['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>

<div class="form-group">
  <label for="color_id">Color</label>
  <select name="color_id" id="color_id">
    <option value="">-- Select --</option>
    <?php foreach ($colors as $c): ?>
      <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>

<div class="form-group">
  <label for="sku">SKU</label>
  <input type="text" name="sku" id="sku">
</div>

<div class="form-group">
  <label for="stock">Stock</label>
  <input type="number" name="stock" id="stock" min="0" value="0">
</div>

<div class="form-group">
  <label for="price">Price</label>
  <input type="number" step="0.01" name="price" id="price" required>
</div>

<div class="form-actions">
  <button type="submit" class="btn btn-primary">Add Variant</button>
  <a href="list.php?product_id=<?= $product_id ?>" class="btn btn-secondary">Cancel</a>
</div>
</form>
<?php require_once "../includes/footer.php"; ?>
