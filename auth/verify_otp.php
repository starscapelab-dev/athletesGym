<?php
require_once "../includes/session.php";
require_once "../includes/csrf.php";
require_once "../layouts/header-item.php";

// Get email from session
$email = $_SESSION['reset_email'] ?? '';

// Redirect if no email in session
if (!$email) {
    $_SESSION['otp_error'] = "Please start the password reset process from the beginning.";
    header("Location: forgot_password.php");
    exit;
}

// Display error/success messages
$error = $_SESSION['otp_error'] ?? '';
$success = $_SESSION['otp_success'] ?? '';
unset($_SESSION['otp_error'], $_SESSION['otp_success']);
?>
<div class="auth-container">

  <div class="auth-card">

    <h2>Verify OTP</h2>
    <p class="auth-subtitle">Enter the 6‑digit code sent to <b><?= htmlspecialchars($email) ?></b> to continue.</p>

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

    <form method="post" action="verify_otp_handler.php">
        <?php csrfField(); ?>
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required pattern="[0-9]{6}" inputmode="numeric">
        <button type="submit" class="btn btn-primary">Verify OTP</button>
    </form>

    <p style="margin-top: 15px; text-align: center;">
      <a href="forgot_password.php">Didn't receive code? Request again</a>
    </p>    

  </div>
</div>
<?php require_once "../layouts/footer.php"; ?>