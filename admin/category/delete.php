<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
        $stmt->execute([$id]);
        ob_end_clean(); // clear any previous output
        header("Location: " . BASE_URL . "admin/category/list.php");
    }
}
header("Location: " . BASE_URL . "admin/category/list.php");
