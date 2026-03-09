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
  <!-- Mobile Menu Toggle Button -->
  <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-logo">
      <img src="<?= BASE_URL ?>assets/images/logo/logo-white.png" alt="Athletes Gym Logo">
    </div>
    <nav>
      <a href="<?= BASE_URL ?>admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
      <a href="<?= BASE_URL ?>admin/users/list.php"><i class="fas fa-users"></i> Users</a>
      <a href="<?= BASE_URL ?>admin/orders/list.php"><i class="fas fa-shopping-cart"></i> Orders</a>
      <a href="<?= BASE_URL ?>admin/category/list.php"><i class="fas fa-folder"></i> Categories</a>
      <a href="<?= BASE_URL ?>admin/products/list.php"><i class="fas fa-box"></i> Products</a>
      <a href="<?= BASE_URL ?>admin/sizes/list.php"><i class="fas fa-ruler"></i> Sizes</a>
      <a href="<?= BASE_URL ?>admin/colors/list.php"><i class="fas fa-palette"></i> Colors</a>
      <a href="<?= BASE_URL ?>admin/reviews/list.php"><i class="fas fa-star"></i> Reviews</a>
      <a href="<?= BASE_URL ?>admin/change_password.php"><i class="fas fa-key"></i> Change Password</a>
      <a href="<?= BASE_URL ?>admin/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </aside>
  <main class="admin-content">
