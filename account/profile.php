<?php
require_once "../includes/session.php";
require_auth(); // block non-logged-in users
require_once "../admin/includes/db.php";
require_once "../layouts/header-item.php";

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) exit("User not found.");
?>
<div class="profile-container">
  <div class="profile-card">
    <div class="profile-header-row">
      <div class="profile-header-main">
        <div class="profile-avatar">
          <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <div>
          <h2 class="profile-meta-title">My Profile</h2>
          <p class="profile-meta-email"><?= htmlspecialchars($user['email']) ?></p>
        </div>
      </div>
      <a href="orders.php" class="profile-orders-link">View My Orders</a>
    </div>
    <?php if (!empty($_GET['msg'])): ?>
      <div class="profile-success"><?=htmlspecialchars($_GET['msg'])?></div>
    <?php endif; ?>

    <div class="profile-section-title">Account details</div>
    <div class="profile-section-subtitle">Update your personal information so we can tailor your Athletes Gym experience.</div>

    <form action="profile_update.php" method="POST">
      <label>Full Name</label>
      <input type="text" name="name" value="<?=htmlspecialchars($user['name'])?>" required>
      <label>Phone Number</label>
      <input type="text" name="phone" value="<?=htmlspecialchars($user['phone'])?>" required pattern="[0-9+ ]{7,16}" maxlength="16">
      <label>Gender</label>
      <select name="gender" required>
        <option value="">Select</option>
        <option value="male" <?=($user['gender']=='male'?'selected':'')?>>Male</option>
        <option value="female" <?=($user['gender']=='female'?'selected':'')?>>Female</option>
        <option value="other" <?=($user['gender']=='other'?'selected':'')?>>Other</option>
      </select>
      <label>Date of Birth</label>
      <input type="date" name="dob" value="<?=htmlspecialchars($user['dob'])?>">
      <label>Country</label>
      <input type="text" name="country" value="<?=htmlspecialchars($user['country'])?>" required>
      <label>City</label>
      <input type="text" name="city" value="<?=htmlspecialchars($user['city'])?>" required>
      <label>Address</label>
      <input type="text" name="address" value="<?=htmlspecialchars($user['address'])?>" required>
      <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <hr class="profile-divider">

    <h3>Change Password</h3>
    <div class="profile-section-subtitle">Keep your account secure by choosing a strong, unique password.</div>

    <form action="profile_password.php" method="POST">
      <label>Current Password</label>
      <input type="password" name="old_password" required>
      <label>New Password</label>
      <input type="password" name="new_password" required minlength="6">
      <label>Confirm New Password</label>
      <input type="password" name="confirm_new_password" required minlength="6">
      <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
  </div>
</div>
<?php require_once "../layouts/footer.php"; ?>

<style>

</style>
