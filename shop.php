<?php 
require_once __DIR__ . "/includes/session.php";

require_once __DIR__ . "/layouts/header-item.php"; 
require_once __DIR__ . "/layouts/config.php";

require_auth();

?>

<main class="main-wrapper">
  <div class="container">
    <h1 class="shop-title">Shop by Gender</h1>
    <div class="gender-grid">
      <a href="shop_categories.php?gender=Men" class="gender-card men">
        <img src="<?= BASE_URL ?>assets/pic/hoodie/DSC04436.png" alt="Men">
        <div class="overlay">Men</div>
      </a>
      <a href="shop_categories.php?gender=Women" class="gender-card women">
        <img src="<?= BASE_URL ?>assets/pic/croptop/Facetune_19-07-2025-00-30-36.png" alt="Women">
        <div class="overlay">Women</div>
      </a>
      <a href="shop_categories.php?gender=Accessories" class="gender-card unisex">
        <img src="<?= BASE_URL ?>assets/pic/socks/SGE08828.png" alt="Accessories">
        <div class="overlay">Accessories</div>
      </a>
    </div>
  </div>
</main>

<?php require_once "layouts/footer.php"; ?>
