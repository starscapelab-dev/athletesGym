<?php
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/includes/cart_functions.php";
require_once __DIR__ . "/layouts/config.php";

// Session already started by header-item.php

// Get order ID from URL
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    echo "<div class='container'><h2>Invalid Order Reference</h2></div>";
    require_once __DIR__ . "/layouts/footer.php";
    exit;
}

// Fetch order details
// SECURITY FIX: Verify user owns this order to prevent IDOR vulnerability
$stmt = $pdo->prepare("
    SELECT id, customer_id, session_id, full_name, email, phone, total, order_status, payment_status, created_at
    FROM orders
    WHERE id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='container'><h2>Order not found</h2></div>";
    require_once __DIR__ . "/layouts/footer.php";
    exit;
}

// SECURITY: Verify ownership - user must be logged in with matching customer_id OR have matching session_id
$userOwnsOrder = false;
if (!empty($_SESSION['user_id']) && $order['customer_id'] == $_SESSION['user_id']) {
    $userOwnsOrder = true;
} elseif (!empty($order['session_id']) && $order['session_id'] === session_id()) {
    $userOwnsOrder = true;
}

if (!$userOwnsOrder) {
    http_response_code(403);
    echo "<div class='container'><h2>Access Denied</h2><p>You do not have permission to view this order.</p></div>";
    require_once __DIR__ . "/layouts/footer.php";
    exit;
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT product_name, color, size, quantity, price, total
    FROM order_items
    WHERE order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.order-success {
  text-align: center;
  padding: 80px 20px;
}
.order-success h1 {
  color: #2a9d8f;
  font-size: 2.2rem;
  margin-bottom: 15px;
}
.order-summary {
  max-width: 700px;
  margin: 40px auto;
  text-align: left;
  background: #f9f9f9;
  border-radius: 12px;
  padding: 30px 40px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.order-summary h3 {
  border-bottom: 2px solid #eee;
  padding-bottom: 8px;
  margin-bottom: 15px;
}
.order-summary table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 25px;
}
.order-summary table th,
.order-summary table td {
  padding: 10px 5px;
  border-bottom: 1px solid #ddd;
}
.order-summary .total-line {
  text-align: right;
  font-size: 1.2rem;
  font-weight: 600;
  color: #333;
}
.btn-home {
  display: inline-block;
  background: #2a9d8f;
  color: #fff;
  text-decoration: none;
  padding: 12px 25px;
  border-radius: 8px;
  transition: background 0.3s;
}
.btn-home:hover {
  background: #21867a;
}
.status-badge {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.9rem;
  text-transform: capitalize;
}
.status-paid { background: #d4edda; color: #155724; }
.status-pending { background: #fff3cd; color: #856404; }
.status-failed { background: #f8d7da; color: #721c24; }
</style>

<div class="order-success container">
  <h1>🎉 Thank You for Your Purchase!</h1>
  <p>Your order has been placed successfully.</p>

  <div class="order-summary">
    <h3>Order Details</h3>
    <p><strong>Order ID:</strong> #<?= htmlspecialchars($order['id']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>

    <p>
      <strong>Payment Status:</strong>
      <span class="status-badge status-<?= htmlspecialchars($order['payment_status']) ?>">
        <?= htmlspecialchars($order['payment_status']) ?>
      </span>
      &nbsp; | &nbsp;
      <strong>Order Status:</strong>
      <span class="status-badge status-<?= htmlspecialchars($order['order_status']) ?>">
        <?= htmlspecialchars($order['order_status']) ?>
      </span>
    </p>

    <h3>Order Items</h3>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Color</th>
          <th>Size</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['product_name']) ?></td>
          <td><?= htmlspecialchars($it['color']) ?></td>
          <td><?= htmlspecialchars($it['size']) ?></td>
          <td><?= $it['quantity'] ?></td>
          <td><?= number_format($it['price'], 2) ?> QR</td>
          <td><?= number_format($it['total'], 2) ?> QR</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p class="total-line">Grand Total: <?= number_format($order['total'], 2) ?> QR</p>
  </div>

  <a href="<?= BASE_URL ?>shop.php" class="btn-home">Continue Shopping</a>
</div>

<?php require_once "layouts/footer.php"; ?>
