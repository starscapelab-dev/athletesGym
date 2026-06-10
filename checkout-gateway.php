<?php
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/includes/csrf.php";

// If already logged in, redirect to checkout
if (isset($_SESSION['user_id'])) {
    header("Location: checkout.php");
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<section class="checkout-gateway-section">
  <div class="container">
    <div class="gateway-wrapper">
      <h1 class="gateway-title">Ready to Checkout?</h1>
      <p class="gateway-subtitle">Sign in for faster checkout or continue as a guest</p>

      <?php if ($error): ?>
        <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #f5c6cb;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <div class="gateway-options">
        <!-- Sign In Option -->
        <div class="gateway-card">
          <div class="card-icon">
            <i class="fas fa-user"></i>
          </div>
          <h3>Returning Customer</h3>
          <p>Sign in to your account for a faster checkout experience</p>
          <a href="auth/login.php?redirect=checkout" class="btn-primary">Sign In</a>
        </div>

        <!-- Register Option -->
        <div class="gateway-card">
          <div class="card-icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <h3>New Customer</h3>
          <p>Create an account to track your orders and save your preferences</p>
          <a href="auth/register.php?redirect=checkout" class="btn-secondary">Create Account</a>
        </div>
      </div>

      <!-- Guest Checkout Option -->
      <div class="guest-option">
        <div class="divider">
          <span>OR</span>
        </div>
        <form method="POST" action="auth/guest_checkout.php" class="guest-form">
          <?php csrfField(); ?>
          <p class="guest-text">No account needed. Checkout quickly as a guest.</p>
          <button type="submit" class="btn-guest">
            <i class="fas fa-shopping-cart"></i>
            Continue as Guest
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<style>
.checkout-gateway-section {
  padding: 80px 20px;
  background: #f8f9fa;
  min-height: calc(100vh - 200px);
}

.gateway-wrapper {
  max-width: 900px;
  margin: 0 auto;
  background: white;
  padding: 50px;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.gateway-title {
  font-size: 2.5em;
  font-weight: 700;
  text-align: center;
  margin-bottom: 10px;
  color: #1a2233;
  font-family: "Inter", sans-serif;
}

.gateway-subtitle {
  text-align: center;
  color: #666;
  font-size: 1.1em;
  margin-bottom: 40px;
}

.gateway-options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 30px;
  margin-bottom: 40px;
}

.gateway-card {
  background: #f8f9fa;
  padding: 40px 30px;
  border-radius: 15px;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
  border: 2px solid transparent;
}

.gateway-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  border-color: #000;
}

.card-icon {
  width: 70px;
  height: 70px;
  background: #000;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 30px;
  margin: 0 auto 20px;
}

.gateway-card h3 {
  font-size: 1.5em;
  margin-bottom: 15px;
  color: #1a2233;
  font-family: "Inter", sans-serif;
}

.gateway-card p {
  color: #666;
  margin-bottom: 25px;
  font-size: 0.95em;
  line-height: 1.6;
}

.btn-primary, .btn-secondary, .btn-guest {
  display: inline-block;
  padding: 14px 40px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  font-size: 16px;
  font-family: "Inter", sans-serif;
}

.btn-primary {
  background: #000;
  color: white;
}

.btn-primary:hover {
  background: #333;
  transform: scale(1.05);
}

.btn-secondary {
  background: white;
  color: #000;
  border: 2px solid #000;
}

.btn-secondary:hover {
  background: #000;
  color: white;
  transform: scale(1.05);
}

.guest-option {
  margin-top: 50px;
}

.divider {
  text-align: center;
  position: relative;
  margin-bottom: 30px;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #ddd;
  z-index: 1;
}

.divider span {
  background: white;
  padding: 0 20px;
  position: relative;
  z-index: 2;
  color: #999;
  font-weight: 600;
  font-size: 14px;
}

.guest-form {
  text-align: center;
}

.guest-text {
  color: #666;
  margin-bottom: 20px;
  font-size: 1.05em;
}

.btn-guest {
  background: #28a745;
  color: white;
  padding: 16px 50px;
  font-size: 1.1em;
}

.btn-guest:hover {
  background: #218838;
  transform: scale(1.05);
}

.btn-guest i {
  margin-right: 10px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .gateway-wrapper {
    padding: 30px 20px;
  }

  .gateway-title {
    font-size: 1.8em;
  }

  .gateway-options {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .gateway-card {
    padding: 30px 20px;
  }

  .btn-primary, .btn-secondary, .btn-guest {
    padding: 12px 30px;
    font-size: 14px;
  }

  .btn-guest {
    padding: 14px 40px;
  }
}

@media (max-width: 480px) {
  .checkout-gateway-section {
    padding: 40px 15px;
  }

  .gateway-title {
    font-size: 1.5em;
  }

  .gateway-subtitle {
    font-size: 1em;
  }

  .card-icon {
    width: 60px;
    height: 60px;
    font-size: 24px;
  }

  .gateway-card h3 {
    font-size: 1.2em;
  }
}
</style>

<?php require_once __DIR__ . "/layouts/footer.php"; ?>
