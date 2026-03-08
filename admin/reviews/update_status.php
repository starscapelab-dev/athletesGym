<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . '/../../layouts/config.php'; // ensure BASE_URL is loaded
session_start();

$id = (int)$_GET['id'];
$status = $_GET['status'];

$stmt = $pdo->prepare("UPDATE product_reviews SET status=? WHERE id=?");
$stmt->execute([$status, $id]);

$_SESSION['msg'] = "Review updated to $status.";
ob_end_clean(); // clear any previous output
header("Location: " . BASE_URL . "admin/reviews/list.php?id=$id");
exit;
?>
