<?php

session_start();
$error = $_SESSION['login_error'] ?? '';
$oldUsername = $_SESSION['old_username'] ?? '';
unset($_SESSION['login_error'], $_SESSION['old_username']); // clear after showing

require_once "../includes/csrf.php";
require_once "../layouts/header-item.php";
?>
<div class="auth-container">
  <div class="auth-card">
    <h2>Sign In</h2>

    <?php if ($error): ?>
      <div class="error-msg" style="background: #ffe6e6;color: #cc0000;  padding: 10px;  margin-bottom: 15px;  border-radius: 5px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form action="login_handler.php" method="POST" autocomplete="off">
      <?php csrfField(); ?>
      <label>Email</label>
      <input type="email" name="email" required autofocus value="<?= htmlspecialchars($oldUsername) ?>">
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit" class="btn btn-primary">Sign In & Continue</button>


      <div class="forgot-pass">
        <a href="forgot_password.php">Forgot Password?</a>
      </div>

    </form>
    <div class="auth-or">or</div>
    <form action="guest_checkout.php" method="POST">
      <?php csrfField(); ?>
      <button type="submit" class="btn btn-guest">Continue as Guest</button>
    </form>
    <div class="auth-link">
      New here? <a href="register.php">Create an Account</a>
    </div>
  </div>
</div>
<?php require_once "../layouts/footer.php"; ?>
