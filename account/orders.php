<?php
require_once "../includes/session.php";
require_auth();
require_once "../admin/includes/db.php";
require_once "../layouts/header-item.php";

// Get user ID from session
$userId = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $pdo->prepare("
    SELECT id, full_name, total, order_status, payment_status, created_at
    FROM orders
    WHERE customer_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<style>
.orders-container {
    max-width: 1200px;
    margin: 150px auto 60px;
    padding: 0 20px;
}
.orders-header {
    margin-bottom: 30px;
}
.orders-header h2 {
    color: #21335b;
    font-size: 2.2rem;
    margin-bottom: 10px;
}
.orders-table {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}
.orders-table table {
    width: 100%;
    border-collapse: collapse;
}
.orders-table th {
    background: #21335b;
    color: #fff;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}
.orders-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}
.orders-table tr:hover {
    background: #f9f9f9;
}
.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    text-transform: capitalize;
    font-weight: 500;
}
.status-paid { background: #d4edda; color: #155724; }
.status-pending { background: #fff3cd; color: #856404; }
.status-failed { background: #f8d7da; color: #721c24; }
.status-processing { background: #d1ecf1; color: #0c5460; }
.status-shipped { background: #cce5ff; color: #004085; }
.status-delivered { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.view-btn {
    display: inline-block;
    padding: 6px 15px;
    background: #21335b;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 0.9rem;
    transition: background 0.2s;
}
.view-btn:hover {
    background: #1a2847;
    color: #fff;
}
.no-orders {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}
.no-orders h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}
.shop-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 30px;
    background: #21335b;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}
.shop-btn:hover {
    background: #1a2847;
    color: #fff;
}
@media (max-width: 768px) {
    .orders-table {
        overflow-x: auto;
    }
    .orders-table table {
        min-width: 600px;
    }
}
</style>

<div class="orders-container">
    <div class="orders-header">
        <h2>My Orders</h2>
        <p>View and track all your orders</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="no-orders">
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="<?= BASE_URL ?>shop.php" class="shop-btn">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-table">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($order['id']) ?></td>
                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                        <td><?= number_format($order['total'], 2) ?> QR</td>
                        <td>
                            <span class="status-badge status-<?= strtolower($order['payment_status']) ?>">
                                <?= htmlspecialchars($order['payment_status']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= strtolower($order['order_status']) ?>">
                                <?= htmlspecialchars($order['order_status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>order_success.php?id=<?= $order['id'] ?>" class="view-btn">View Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once "../layouts/footer.php"; ?>
