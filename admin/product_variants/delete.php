<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded

$id = (int)($_GET['id'] ?? 0);
$product_id = (int)($_GET['product_id'] ?? 0);

if ($id > 0 && $product_id > 0) {
    $pdo->prepare("DELETE FROM product_variants WHERE id=? AND product_id=?")->execute([$id, $product_id]);
    ob_end_clean(); // clear any previous output
    header("Location: " . BASE_URL . "admin/product_variants/list.php?product_id=$product_id&msg=Variant+deleted");
} else {
    ob_end_clean(); // clear any previous output
    header("Location: " . BASE_URL . "admin/product_variants/list.php?product_id=$product_id&msg=Invalid+delete+request");
}
