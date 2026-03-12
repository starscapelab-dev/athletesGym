<?php
require_once "../includes/session.php";
require_auth();
require_once "../admin/includes/db.php";
require_once "../layouts/config.php";

// Get order ID from URL
$orderId = $_GET['id'] ?? 0;

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, u.id as user_id
    FROM orders o
    LEFT JOIN users u ON o.customer_id = u.id
    WHERE o.id = ? AND (o.customer_id = ? OR o.session_id = ?)
");
$stmt->execute([$orderId, $_SESSION['user_id'], session_id()]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: profile.php?page=orders");
    exit;
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Athletes Gym</title>
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
                    <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                </div>
                <h2 class="account-user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></h2>
                <p class="account-user-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
            </div>

            <nav class="account-nav">
                <a href="profile.php?page=profile" class="account-nav-link">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="profile.php?page=orders" class="account-nav-link active">
                    <i class="fas fa-shopping-bag"></i>
                    <span>My Orders</span>
                </a>
                <a href="profile.php?page=password" class="account-nav-link">
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
            <div class="account-page">
                <div class="page-header">
                    <a href="profile.php?page=orders" style="color: #2a9d8f; text-decoration: none; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                    <h1>Order #<?= htmlspecialchars($order['id']) ?></h1>
                    <p>Order placed on <?= date('F d, Y', strtotime($order['created_at'])) ?></p>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
                    <!-- Order Items -->
                    <div>
                        <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 30px;">
                            <h3 style="font-size: 1.3rem; color: #21335b; margin-bottom: 20px; font-weight: 700;">Order Items</h3>
                            
                            <div style="border-bottom: 2px solid #eee; padding-bottom: 20px;">
                                <?php foreach ($items as $item): ?>
                                <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                                    <div>
                                        <h4 style="color: #2c3e50; margin-bottom: 5px;">
                                            <?= htmlspecialchars($item['product_name'] ?? $item['product_name']) ?>
                                        </h4>
                                        <small style="color: #999;">
                                            <?php 
                                            $details = [];
                                            if ($item['color']) $details[] = "Color: " . htmlspecialchars($item['color']);
                                            if ($item['size']) $details[] = "Size: " . htmlspecialchars($item['size']);
                                            echo implode(" | ", $details);
                                            ?>
                                        </small><br>
                                        <small style="color: #666;">Qty: <?= $item['quantity'] ?></small>
                                    </div>
                                    <div style="text-align: right;">
                                        <strong><?= number_format($item['total'], 2) ?> QR</strong>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                            <h3 style="font-size: 1.3rem; color: #21335b; margin-bottom: 20px; font-weight: 700;">Shipping Address</h3>
                            <p style="margin-bottom: 10px;"><strong><?= htmlspecialchars($order['full_name']) ?></strong></p>
                            <p style="color: #666; margin-bottom: 8px;"><?= htmlspecialchars($order['shipping_address']) ?></p>
                            <p style="color: #666; margin-bottom: 8px;"><?= htmlspecialchars($order['city'] . ', ' . $order['country']) ?></p>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div>
                        <!-- Status Card -->
                        <div style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
                            <h4 style="color: #21335b; margin-bottom: 20px; font-weight: 700;">Order Status</h4>
                            
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 0.9rem; color: #666; margin-bottom: 5px; display: block;">Order Status</label>
                                <span class="status-badge status-<?= strtolower($order['order_status']) ?>">
                                    <?= htmlspecialchars(ucfirst($order['order_status'])) ?>
                                </span>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 0.9rem; color: #666; margin-bottom: 5px; display: block;">Payment Status</label>
                                <span class="status-badge status-<?= strtolower($order['payment_status']) ?>">
                                    <?= htmlspecialchars(ucfirst($order['payment_status'])) ?>
                                </span>
                            </div>

                            <div style="padding-top: 15px; border-top: 1px solid #eee;">
                                <small style="color: #999;">Order placed on</small><br>
                                <small style="color: #2c3e50; font-weight: 600;">
                                    <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?>
                                </small>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                            <h4 style="color: #21335b; margin-bottom: 20px; font-weight: 700;">Price Summary</h4>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #666;">Subtotal</span>
                                <strong><?= number_format($order['subtotal'], 2) ?> QR</strong>
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #666;">Shipping</span>
                                <strong><?= number_format($order['shipping_fee'], 2) ?> QR</strong>
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #666;">Tax</span>
                                <strong><?= number_format($order['tax'], 2) ?> QR</strong>
                            </div>

                            <div style="padding-top: 15px; border-top: 2px solid #eee; display: flex; justify-content: space-between;">
                                <span style="font-weight: 700; color: #21335b;">Total</span>
                                <strong style="font-size: 1.2rem; color: #2a9d8f;">
                                    <?= number_format($order['total'], 2) ?> QR
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php require_once __DIR__ . "/../layouts/footer.php"; ?>
</body>
</html>
