<?php
require_once "../includes/session.php";
require_auth(); // block non-logged-in users
require_once "../admin/includes/db.php";
require_once "../layouts/config.php";

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) exit("User not found.");

// Get user's orders
$stmt = $pdo->prepare("
    SELECT id, full_name, total, order_status, payment_status, created_at
    FROM orders
    WHERE customer_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Get active page
$activePage = 'profile';
if (!empty($_GET['page'])) {
    $activePage = $_GET['page'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Athletes Gym</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>account/account-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php require_once __DIR__ . "/../layouts/header-item.php"; ?>
    
    <div class="account-dashboard">
        <!-- Sidebar Navigation -->
        <aside class="account-sidebar">
            <div class="account-sidebar-header">
                <div class="account-avatar-circle">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <h2 class="account-user-name"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="account-user-email"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <nav class="account-nav">
                <a href="profile.php?page=profile" class="account-nav-link <?= $activePage === 'profile' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="profile.php?page=orders" class="account-nav-link <?= $activePage === 'orders' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <span>My Orders</span>
                </a>
                <a href="profile.php?page=password" class="account-nav-link <?= $activePage === 'password' ? 'active' : '' ?>">
                    <i class="fas fa-lock"></i>
                    <span>Change Password</span>
                </a>
                <a href="<?= BASE_URL ?>auth/logout.php" class="account-nav-link logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="account-content">
            <?php if (!empty($_GET['msg'])): ?>
                <div class="account-alert success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <!-- Profile Page -->
            <?php if ($activePage === 'profile'): ?>
            <div class="account-page">
                <div class="page-header">
                    <h1>My Profile</h1>
                    <p>Manage your personal information</p>
                </div>

                <div class="profile-form-container">
                    <h3 class="form-section-title">Account Details</h3>
                    <p class="form-section-description">Update your personal information so we can tailor your Athletes Gym experience.</p>

                    <form action="profile_update.php" method="POST" class="account-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                <small class="form-help">Email cannot be changed</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required pattern="[0-9+ ]{7,16}" maxlength="16">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?= ($user['gender'] === 'male' ? 'selected' : '') ?>>Male</option>
                                    <option value="female" <?= ($user['gender'] === 'female' ? 'selected' : '') ?>>Female</option>
                                    <option value="other" <?= ($user['gender'] === 'other' ? 'selected' : '') ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($user['dob']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" value="<?= htmlspecialchars($user['country']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Orders Page -->
            <?php elseif ($activePage === 'orders'): ?>
            <div class="account-page">
                <div class="page-header">
                    <h1>My Orders</h1>
                    <p>View and manage your purchases</p>
                </div>

                <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Start shopping now!</p>
                    <a href="<?= BASE_URL ?>shop.php" class="btn-primary">
                        <i class="fas fa-shopping-cart"></i> Continue Shopping
                    </a>
                </div>
                <?php else: ?>
                <div class="orders-table-wrapper">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Total</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td><?= number_format($order['total'], 2) ?> QR</td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['order_status'])) ?>">
                                        <?= htmlspecialchars(ucfirst($order['order_status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['payment_status'])) ?>">
                                        <?= htmlspecialchars(ucfirst($order['payment_status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>account/view_order.php?id=<?= $order['id'] ?>" class="view-order-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Password Change Page -->
            <?php elseif ($activePage === 'password'): ?>
            <div class="account-page">
                <div class="page-header">
                    <h1>Change Password</h1>
                    <p>Keep your account secure with a strong password</p>
                </div>

                <div class="password-form-container">
                    <form action="profile_password.php" method="POST" class="account-form">
                        <div class="form-group">
                            <label for="old_password">Current Password</label>
                            <input type="password" id="old_password" name="old_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required minlength="8">
                            <small class="form-help">Minimum 8 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_new_password">Confirm New Password</label>
                            <input type="password" id="confirm_new_password" name="confirm_new_password" required minlength="8">
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-lock"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <?php require_once __DIR__ . "/../layouts/footer.php"; ?>
</body>
</html>
