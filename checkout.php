<?php

require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/includes/cart_functions.php";
require_once __DIR__ . "/layouts/config.php";
require_once __DIR__ . "/includes/csrf.php";


require_auth();

$items = getCartItems($pdo);
if (!$items) {
    echo "<p>Your cart is empty. <a href='shop.php'>Go to shop</a></p>";
    require_once __DIR__ . "/layouts/footer.php"; exit;
}

// If logged in, prefill
$name = $_SESSION['user_name'] ?? "";
$email = $_SESSION['user_email'] ?? "";
$phone = $_SESSION['user_phone'] ?? "";
?>

<section class="checkout-section">
  <div class="container">
    <h1 class="checkout-title">Checkout</h1>

    <?php if (isset($_SESSION['checkout_error'])): ?>
      <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #f5c6cb;">
        <?= htmlspecialchars($_SESSION['checkout_error']) ?>
      </div>
      <?php unset($_SESSION['checkout_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['checkout_success'])): ?>
      <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #c3e6cb;">
        <?= htmlspecialchars($_SESSION['checkout_success']) ?>
      </div>
      <?php unset($_SESSION['checkout_success']); ?>
    <?php endif; ?>

    <div class="checkout-grid">
      <!-- Customer Info -->
      <div class="checkout-form-container">
        <form method="post" action="checkout_process.php" class="checkout-form">
          <?php csrfField(); ?>
          <h2>Billing Details</h2>

          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" placeholder="Enter your name" required>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" placeholder="you@example.com" required>
          </div>

          <div class="form-group">
            <label for="phone">Phone Number (+974) </label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($phone) ?>" maxlength="8" pattern="^[0-9]{6}|[0-9]{8}|[0-9]{10}$" placeholder="7070 0707" required>
          </div>

          <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address" placeholder="Street address, building, apartment" required></textarea>
          </div>

          <div class="form-group">
            <label for="notes">Order Notes (optional)</label>
            <textarea name="notes" id="notes" placeholder="Any special instructions for your order"></textarea>
          </div>

          <button type="submit" class="btn-primary checkout-btn">Place Order</button>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="order-summary">
        <h2>Order Summary</h2>

        <ul class="summary-list">
          <?php 
          $grand = 0; 
          foreach ($items as $item): 
            $line = $item['quantity'] * $item['price'];
            $grand += $line;
          ?>
            <li class="summary-item">
              <div class="item-info">
              <img src="<?= BASE_URL ?>uploads/<?= $item['image_path'] ?>" alt="<?= $item['name'] ?>">
                <div>
                  <p class="item-name"><?= htmlspecialchars($item['name']) ?></p>
                  <small><?= $item['quantity'] ?> × <?= number_format($item['price'], 2) ?> QR</small>
                </div>
              </div>
              <span class="item-total"><?= number_format($line, 2) ?> QR</span>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="summary-footer">
          <div class="line">
            <span>Subtotal</span>
            <span><?= number_format($grand, 2) ?> QR</span>
          </div>
          <div class="line">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="line total">
            <span>Total</span>
            <span><?= number_format($grand, 2) ?> QR</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . "/layouts/footer.php"; ?>
