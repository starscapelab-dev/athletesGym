<?php

require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/includes/cart_functions.php";
require_once __DIR__ . "/layouts/config.php";
require_once __DIR__ . "/includes/csrf.php";

// ✅ REQUIRE LOGIN - No guest checkout allowed
if (empty($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php?redirect=checkout");
    exit;
}

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

          <div class="form-group phone-group">
            <div class="phone-country-select">
              <label for="country_code">Country Code</label>
              <select name="country_code" id="country_code" required>
                <option value="">Select Country Code</option>
                <option value="+1">+1 (US/Canada)</option>
                <option value="+44">+44 (UK)</option>
                <option value="+91">+91 (India)</option>
                <option value="+61">+61 (Australia)</option>
                <option value="+81">+81 (Japan)</option>
                <option value="+86">+86 (China)</option>
                <option value="+33">+33 (France)</option>
                <option value="+49">+49 (Germany)</option>
                <option value="+39">+39 (Italy)</option>
                <option value="+34">+34 (Spain)</option>
                <option value="+41">+41 (Switzerland)</option>
                <option value="+46">+46 (Sweden)</option>
                <option value="+47">+47 (Norway)</option>
                <option value="+31">+31 (Netherlands)</option>
                <option value="+32">+32 (Belgium)</option>
                <option value="+43">+43 (Austria)</option>
                <option value="+45">+45 (Denmark)</option>
                <option value="+358">+358 (Finland)</option>
                <option value="+353">+353 (Ireland)</option>
                <option value="+30">+30 (Greece)</option>
                <option value="+48">+48 (Poland)</option>
                <option value="+420">+420 (Czech Republic)</option>
                <option value="+36">+36 (Hungary)</option>
                <option value="+40">+40 (Romania)</option>
                <option value="+359">+359 (Bulgaria)</option>
                <option value="+385">+385 (Croatia)</option>
                <option value="+387">+387 (Bosnia)</option>
                <option value="+381">+381 (Serbia)</option>
                <option value="+355">+355 (Albania)</option>
                <option value="+371">+371 (Latvia)</option>
                <option value="+370">+370 (Lithuania)</option>
                <option value="+372">+372 (Estonia)</option>
                <option value="+60">+60 (Malaysia)</option>
                <option value="+65">+65 (Singapore)</option>
                <option value="+66">+66 (Thailand)</option>
                <option value="+84">+84 (Vietnam)</option>
                <option value="+62">+62 (Indonesia)</option>
                <option value="+63">+63 (Philippines)</option>
                <option value="+852">+852 (Hong Kong)</option>
                <option value="+886">+886 (Taiwan)</option>
                <option value="+82">+82 (South Korea)</option>
                <option value="+234">+234 (Nigeria)</option>
                <option value="+27">+27 (South Africa)</option>
                <option value="+212">+212 (Morocco)</option>
                <option value="+55">+55 (Brazil)</option>
                <option value="+54">+54 (Argentina)</option>
                <option value="+56">+56 (Chile)</option>
                <option value="+57">+57 (Colombia)</option>
                <option value="+507">+507 (Panama)</option>
                <option value="+505">+505 (Nicaragua)</option>
                <option value="+52">+52 (Mexico)</option>
                <option value="+503">+503 (El Salvador)</option>
                <option value="+506">+506 (Costa Rica)</option>
                <option value="+51">+51 (Peru)</option>
                <option value="+64">+64 (New Zealand)</option>
                <option value="+880">+880 (Bangladesh)</option>
                <option value="+94">+94 (Sri Lanka)</option>
                <option value="+92">+92 (Pakistan)</option>
                <option value="+90">+90 (Turkey)</option>
                <option value="+971" selected>+971 (UAE)</option>
                <option value="+966">+966 (Saudi Arabia)</option>
                <option value="+965">+965 (Kuwait)</option>
                <option value="+974">+974 (Qatar)</option>
                <option value="+973">+973 (Bahrain)</option>
                <option value="+968">+968 (Oman)</option>
              </select>
            </div>
            <div class="phone-input-wrapper">
              <label for="phone">Phone Number</label>
              <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="1234567890" required>
            </div>
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
