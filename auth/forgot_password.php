<?php
require_once "../includes/session.php";
require_once "../includes/csrf.php";
require_once "../layouts/header-item.php";

// Display error/success messages
$error = $_SESSION['forgot_error'] ?? '';
$success = $_SESSION['forgot_success'] ?? '';
unset($_SESSION['forgot_error'], $_SESSION['forgot_success']);
?>
<div class="auth-container">
  <div class="auth-card">

    <h2>Forgot Password</h2>
    <p class="auth-subtitle">Enter your registered email address to receive a secure OTP for password reset.</p>

    <?php if ($error): ?>
      <div class="error-msg">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-msg">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="forgot_password_handler.php">
        <?php csrfField(); ?>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" class="btn btn-primary">Send OTP</button>
    </form>
    </div>
    </div>
<?php require_once "../layouts/footer.php"; ?>
