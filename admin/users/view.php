<?php
$pageTitle = "User Details";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$userId = $_GET['id'] ?? 0;

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='error-message'>User not found.</div>";
    require_once __DIR__ . "/../includes/footer.php";
    exit;
}

// Get user's order statistics
$orderStats = $pdo->prepare("SELECT
    COUNT(*) as total_orders,
    SUM(total) as total_spent,
    MAX(created_at) as last_order_date
    FROM orders
    WHERE customer_id = ?");
$orderStats->execute([$userId]);
$stats = $orderStats->fetch();

// Get user's orders
$ordersStmt = $pdo->prepare("SELECT
    o.id, o.full_name, o.email, o.phone, o.total, o.payment_status, o.order_status, o.created_at
    FROM orders o
    WHERE o.customer_id = ?
    ORDER BY o.created_at DESC
    LIMIT 10");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll();
?>

<div class="page-header">
    <h1>User Details</h1>
    <div class="page-actions">
        <a href="list.php" class="btn btn-secondary">← Back to List</a>
        <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-primary">Edit User</a>
    </div>
</div>

<div class="user-details-container">
    <!-- User Information Card -->
    <div class="details-card">
        <h2>Personal Information</h2>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">User ID:</span>
                <span class="detail-value"><?= $user['id'] ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Full Name:</span>
                <span class="detail-value"><?= htmlspecialchars($user['name']) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Gender:</span>
                <span class="detail-value"><?= $user['gender'] ? ucfirst($user['gender']) : 'N/A' ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Date of Birth:</span>
                <span class="detail-value"><?= $user['dob'] ? date('M d, Y', strtotime($user['dob'])) : 'N/A' ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Country:</span>
                <span class="detail-value"><?= htmlspecialchars($user['country'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">City:</span>
                <span class="detail-value"><?= htmlspecialchars($user['city'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Address:</span>
                <span class="detail-value"><?= htmlspecialchars($user['address'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Newsletter:</span>
                <span class="detail-value">
                    <?php if ($user['newsletter']): ?>
                        <span class="badge badge-green">Subscribed</span>
                    <?php else: ?>
                        <span class="badge badge-gray">Not Subscribed</span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Registered:</span>
                <span class="detail-value"><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="details-card">
        <h2>Order Statistics</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-label">Total Orders</span>
                <span class="stat-value"><?= $stats['total_orders'] ?? 0 ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Spent</span>
                <span class="stat-value">QAR <?= number_format($stats['total_spent'] ?? 0, 2) ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Last Order</span>
                <span class="stat-value">
                    <?= $stats['last_order_date'] ? date('M d, Y', strtotime($stats['last_order_date'])) : 'Never' ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="details-card">
        <h2>Recent Orders</h2>
        <?php if (empty($orders)): ?>
            <p class="no-data">No orders found for this user.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= $order['id'] ?></strong></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td>QAR <?= number_format($order['total'], 2) ?></td>
                                <td>
                                    <span class="badge badge-<?= $order['payment_status'] === 'paid' ? 'green' : 'yellow' ?>">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $order['order_status'] === 'delivered' ? 'green' : ($order['order_status'] === 'cancelled' ? 'red' : 'blue') ?>">
                                        <?= ucfirst($order['order_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="../orders/view.php?id=<?= $order['id'] ?>" class="btn-action btn-view">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($stats['total_orders'] > 10): ?>
                <p class="table-note">Showing 10 most recent orders. Total: <?= $stats['total_orders'] ?> orders</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
