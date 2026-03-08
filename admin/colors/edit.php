<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "Edit Color";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM colors WHERE id=?");
$stmt->execute([$id]);
$color = $stmt->fetch();

if (!$color) {
    ob_end_clean(); // clear any previous output
    header("Location: " . BASE_URL . "admin/colors/list.php");
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    if ($name === '') {
        $error = "Color name is required.";
    } else {
        $stmt = $pdo->prepare("UPDATE colors SET name=? WHERE id=?");
        $stmt->execute([$name, $id]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/colors/list.php");
    }
}

require_once __DIR__ . "/../includes/header.php";
?>

<h1>Edit Color</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">
<?php csrfField(); ?>

<div class="form-group">
  <label for="name">Color Name</label>
  <input type="text" name="name" id="name" value="<?= sanitize($color['name']) ?>" required>
</div>


  <div class="form-actions">
  <button type="submit" class="btn btn-primary">Update Color</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>

<?php require_once "../includes/footer.php"; ?>
