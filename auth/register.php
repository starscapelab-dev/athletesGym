<?php
require_once "../includes/csrf.php";
require_once "../layouts/header-item.php";

// Get the redirect parameter from URL
$redirect = $_GET['redirect'] ?? '';
?>
<div class="auth-container">
  <div class="auth-card auth-card-wide">
    <div class="auth-header">
      <h2>Create Account</h2>
      <p class="auth-subtitle">Join Athletes Gym to book, track and manage your workouts and orders.</p>
    </div>

    <?php if (!empty($_GET['error'])): ?>
      <div class="auth-error"><?=htmlspecialchars($_GET['error'])?></div>
    <?php endif; ?>

    <form action="register_handler.php" method="POST" autocomplete="off">
      <?php csrfField(); ?>
      <?php if ($redirect): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <?php endif; ?>

      <div class="form-section-title">Personal Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone" placeholder="+974 1234 5678" required pattern="[0-9+ ]{7,16}" maxlength="16">
        </div>

        <div class="form-group">
          <label>Gender</label>
          <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Date of Birth</label>
          <input type="date" name="dob" max="<?=date('Y-m-d')?>">
        </div>
      </div>

      <div class="form-section-title">Security</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Minimum 8 characters" required minlength="8">
        </div>

        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" placeholder="Re-enter password" required minlength="8">
        </div>
      </div>

      <div class="form-section-title">Address Details</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Country</label>
          <input type="text" name="country" placeholder="e.g., Qatar" required>
        </div>

        <div class="form-group">
          <label>City</label>
          <input type="text" name="city" placeholder="e.g., Doha" required>
        </div>

        <div class="form-group form-group-full">
          <label>Address</label>
          <input type="text" name="address" placeholder="Street address, building, apartment" required>
        </div>
      </div>

      <div class="form-checkboxes">
        <div class="checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" name="newsletter" value="1">
            <span>Sign me up for exclusive offers & news</span>
          </label>
        </div>
        <div class="checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" name="terms" value="1" required>
            <span>I agree to the <a href="/terms.php" target="_blank">Terms & Conditions</a></span>
          </label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Register & Continue</button>
    </form>

    <div class="auth-link">
      Already have an account? <a href="login.php">Sign In</a>
    </div>
  </div>
</div>
<?php require_once "../layouts/footer.php"; ?>
