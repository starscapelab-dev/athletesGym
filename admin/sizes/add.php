<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Add Size";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $name = trim($_POST['name']);
    if ($name === '') {
        $error = "Size name is required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO sizes (name) VALUES (?)");
        $stmt->execute([$name]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/sizes/list.php");
    }
}

require_once __DIR__ . "/../includes/header.php";
?>

<h1>Add Size</h1>
<?php if ($error): ?><div class="admin-error"><?= sanitize($error) ?></div><?php endif; ?>
<form method="post" class="admin-form">
<?php csrfField(); ?>
  <div class="form-group">
  <label for="name">Size Name</label>
  <input type="text" name="name" id="name" required>
  </div>

  <div class="form-actions">
  <button type="submit" class="btn btn-primary">Add Size</button>
  <a href="list.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>

<?php require_once "../includes/footer.php"; ?>
