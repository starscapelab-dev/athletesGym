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
    if ($name === '') {
        $error = "Size name is required.";
    } else {
        $stmt = $pdo->prepare("UPDATE sizes SET name=? WHERE id=?");
        $stmt->execute([$name, $id]);
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

<div class="form-actions">
  <button type="submit" class="btn btn-primary">Update Size</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
</div>
</form>

<?php require_once "../includes/footer.php"; ?>
