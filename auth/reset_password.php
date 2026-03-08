<?php
require_once "../includes/session.php";
require_once "../includes/csrf.php";
require_once "../layouts/header-item.php";

// Check if user is authorized to reset password
$email = $_SESSION['reset_email'] ?? '';
$otpVerified = $_SESSION['otp_verified'] ?? false;

if (!$email || !$otpVerified) {
    $_SESSION['reset_error'] = "Unauthorized access. Please start the password reset process.";
    header("Location: forgot_password.php");
    exit;
}

// Display error/success messages
$error = $_SESSION['reset_error'] ?? '';
$success = $_SESSION['reset_success'] ?? '';
unset($_SESSION['reset_error'], $_SESSION['reset_success']);
?>
<div class="auth-container">
  <div class="auth-card">
    <h2>Reset Password</h2>
    <p class="auth-subtitle">Set a new password for your account: <b><?= htmlspecialchars($email) ?></b></p>

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

    <form method="post" action="reset_password_handler.php">
        <?php csrfField(); ?>
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 8 characters)" required minlength="8">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required minlength="8">

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
  </div>
</div>
<?php require_once "../layouts/footer.php"; ?>