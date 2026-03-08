<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$id = (int)($_GET['id'] ?? 0);
$product_id = (int)($_GET['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE id=? AND product_id=?");
$stmt->execute([$id, $product_id]);
$img = $stmt->fetch();
if ($img) {
    // Optionally delete file: unlink("../../uploads/".$img['image_path']);
    $pdo->prepare("DELETE FROM product_images WHERE id=?")->execute([$id]);
}
ob_end_clean(); // clear any previous output
header("Location: " . BASE_URL . "admin/product_images/list.php?product_id=$product_id&msg=Image+deleted");
