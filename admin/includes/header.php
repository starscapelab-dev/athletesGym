<?php
// SECURITY: Require admin authentication on all pages
if (!isset($noSessionCheck)) require_once __DIR__ . "/_session.php";
require_once __DIR__ . "/../../layouts/config.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel | <?= sanitize($pageTitle ?? 'Dashboard') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>admin/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
  <aside class="admin-sidebar">
    <div class="admin-sidebar-logo">
      <img src="<?= BASE_URL ?>assets/images/logo/logo-white.png" alt="Athletes Gym Logo">
    </div>
    <nav>
      <a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a>
      <a href="<?= BASE_URL ?>admin/users/list.php">Users</a>
      <a href="<?= BASE_URL ?>admin/orders/list.php">Orders</a>
      <a href="<?= BASE_URL ?>admin/category/list.php">Categories</a>
      <a href="<?= BASE_URL ?>admin/products/list.php">Products</a>
      <a href="<?= BASE_URL ?>admin/sizes/list.php">Sizes</a>
      <a href="<?= BASE_URL ?>admin/colors/list.php">Colors</a>
      <a href="<?= BASE_URL ?>admin/reviews/list.php">Reviews</a>
      <a href="<?= BASE_URL ?>admin/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>
  <main class="admin-content">
