<?php
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/includes/cart_functions.php";
require_once __DIR__ . "/layouts/config.php";

require_auth();

$items = getCartItems($pdo);

?>

<section class="cart-section">
  <div class="container">
    <h1 class="cart-title">Your Shopping Cart</h1>
    <?php if (!empty($_SESSION['checkout_error'])): ?>
  <div class="cart-message error">
    <?= htmlspecialchars($_SESSION['checkout_error']) ?>
  </div>
  <?php unset($_SESSION['checkout_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['checkout_success'])): ?>
  <div class="cart-message success">
    <?= htmlspecialchars($_SESSION['checkout_success']) ?>
  </div>
  <?php unset($_SESSION['checkout_success']); ?>
<?php endif; ?>



    <?php if (!$items): 
        ?>
      <div class="empty-cart">
        <img src="<?= BASE_URL ?>assets/site/empty-cart.webp" alt="Empty Cart">
        <p>Your cart is empty.</p>
        <a href="<?= BASE_URL ?>shop.php" class="btn-primary">Continue Shopping</a>
      </div>
    <?php else: ?>

      <div class="cart-grid">
        <!-- Cart Items -->
        <div class="cart-items">
          <?php 
          $grandTotal = 0;
          foreach ($items as $item): 
            $total = $item['price'] * $item['quantity']; 
            $grandTotal += $total;

          ?>
          <div class="cart-item">
            <div class="item-image">
              <img src="<?= BASE_URL ?>uploads/<?= $item['image_path'] ?>" alt="<?= $item['name'] ?>">
            </div>
            <div class="item-details">
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p class="item-meta">
                <?= htmlspecialchars($item['size'] ?? '-') ?> | <?= $item['color'] ?? '-' ?>
              </p>
              <p class="item-price"><?= number_format($item['price'], 2) ?> QR</p>

              <div class="item-actions">
                <form method="post" action="cards/cart_update.php" class="update-form">
                  <input type="hidden" name="item_id" value="<?= $item['cart_item_id'] ?>">
                  <select name="qty" class="qty-select" onchange="this.form.submit();">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                      <option value="<?= $i ?>" <?= ($item['quantity'] === $i) ? 'selected' : '' ?>>
                        <?= $i ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </form>

                <form method="post" action="cards/cart_update.php" class="remove-form" style="margin: 0;">
                  <input type="hidden" name="item_id" value="<?= $item['cart_item_id'] ?>">
                  <input type="hidden" name="qty" value="0">
                  <button type="submit" class="btn-remove">Remove</button>
                </form>
              </div>
            </div>
            <div class="item-total"><?= number_format($total, 2) ?> QR</div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div class="cart-summary">
          <h2>Order Summary</h2>
          <div class="summary-line">
            <span>Subtotal</span>
            <span><?= number_format($grandTotal, 2) ?> QR</span>
          </div>
          <div class="summary-line">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="summary-total">
            <span>Total</span>
            <span><?= number_format($grandTotal, 2) ?> QR</span>
          </div>

          <div class="cart-buttons">
            <a href="<?= BASE_URL ?>shop.php" class="btn-secondary">Continue Shopping</a>
            <a href="<?= BASE_URL ?>checkout.php" class="btn-primary">Proceed to Checkout</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once "layouts/footer.php"; ?>
