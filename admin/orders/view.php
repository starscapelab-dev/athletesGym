<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Order Details";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

// Session already active from header/session includes - no need to start again

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='admin-error'>Order not found.</div>";
    require_once "../includes/footer.php";
    exit;
}

$stmt = $pdo->prepare("
  SELECT oi.*, pi.image_path
  FROM order_items oi
  LEFT JOIN product_images pi ON oi.product_id = pi.product_id
  WHERE oi.order_id = ?
  GROUP BY oi.id
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success_msg'])): ?>
<div class="admin-alert admin-alert-success" style="margin-bottom: 20px;">
  <?= htmlspecialchars($_SESSION['success_msg']) ?>
  <?php unset($_SESSION['success_msg']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
<div class="admin-alert admin-alert-error" style="margin-bottom: 20px;">
  <?= htmlspecialchars($_SESSION['error_msg']) ?>
  <?php unset($_SESSION['error_msg']); ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
  <h1>Order #<?= $order['id'] ?></h1>
  <a href="list.php" class="btn btn-secondary">Back to Orders</a>
</div>

<div class="order-details-grid">
  <div class="order-section">
    <h2 class="admin-section-title">Customer Info</h2>
    <table class="admin-table">
      <tbody>
        <tr>
          <td style="width: 30%; font-weight: 600;">Name:</td>
          <td><?= htmlspecialchars($order['full_name']) ?></td>
        </tr>
        <tr>
          <td style="width: 30%; font-weight: 600;">Email:</td>
          <td><?= htmlspecialchars($order['email']) ?></td>
        </tr>
        <tr>
          <td style="width: 30%; font-weight: 600;">Phone:</td>
          <td><?= htmlspecialchars($order['phone']) ?></td>
        </tr>
        <tr>
          <td style="width: 30%; font-weight: 600;">Address:</td>
          <td><?= htmlspecialchars($order['shipping_address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['country']) ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="order-section">
    <h2 class="admin-section-title">Order Summary</h2>
    <table class="admin-table">
      <tbody>
        <tr>
          <td style="width: 30%; font-weight: 600;">Subtotal:</td>
          <td><?= number_format($order['subtotal'], 2) ?> QAR</td>
        </tr>
        <tr>
          <td style="width: 30%; font-weight: 600;">Total:</td>
          <td><strong style="font-size: 1.1em; color: #21335b;"><?= number_format($order['total'], 2) ?> QAR</strong></td>
        </tr>
        <tr>
          <td style="width: 30%; font-weight: 600;">Payment:</td>
          <td><span class="status-badge status-<?= strtolower($order['payment_status']) ?>"><?= ucfirst($order['payment_status']) ?></span></td>
        </tr>
        <tr>
          <td style="width: 30%; font-weight: 600;">Status:</td>
          <td><span class="status-badge status-<?= strtolower($order['order_status']) ?>"><?= ucfirst($order['order_status']) ?></span></td>
        </tr>
      </tbody>
    </table>

    <form method="post" action="update_status.php" class="admin-form" style="margin-top: 20px; max-width: 100%;">
      <input type="hidden" name="id" value="<?= $order['id'] ?>">
      <div class="form-group">
        <label for="status">Update Status:</label>
        <select name="status" id="status" class="form-control">
          <option value="pending" <?= $order['order_status']=='pending'?'selected':'' ?>>Pending</option>
          <option value="processing" <?= $order['order_status']=='processing'?'selected':'' ?>>Processing</option>
          <option value="shipped" <?= $order['order_status']=='shipped'?'selected':'' ?>>Shipped</option>
          <option value="delivered" <?= $order['order_status']=='delivered'?'selected':'' ?>>Delivered</option>
          <option value="cancelled" <?= $order['order_status']=='cancelled'?'selected':'' ?>>Cancelled</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>

    <!-- Resend Email Section -->
    <div style="margin-top: 20px; padding: 20px; background: #f0f4ff; border-radius: 8px; border-left: 4px solid #4CAF50;">
      <h3 style="margin: 0 0 15px 0; color: #333; font-size: 14px;">📧 Resend Emails</h3>
      <p style="margin: 0 0 15px 0; color: #666; font-size: 13px;">Click the button below to resend order confirmation emails to both the customer and admin.</p>
      <form method="post" action="resend_email.php" style="display: inline;">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to resend order emails?')">
          <span style="margin-right: 5px;">📧</span> Resend Order Emails
        </button>
      </form>
    </div>
  </div>
</div>

<h2 class="admin-section-title" style="margin-top: 40px;">Order Items</h2>
<table class="admin-table">
    <thead>
      <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Variant</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td style="text-align: center;">
          <?php if ($item['image_path']): ?>
            <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="max-width: 60px; max-height: 60px; border-radius: 4px;">
          <?php else: ?>
            <span style="color: #999; font-size: 12px;">No image</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td><?= htmlspecialchars($item['color']) ?> / <?= htmlspecialchars($item['size']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= number_format($item['price'], 2) ?></td>
        <td><?= number_format($item['total'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<style>
.order-details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
  gap: 30px;
  margin-bottom: 40px;
}

.order-section {
  background: #fff;
  padding: 0;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.order-section .admin-section-title {
  margin: 0;
  padding: 20px 25px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px 12px 0 0;
  border-left: none;
  border-bottom: 2px solid #21335b;
}

.order-section .admin-table {
  box-shadow: none;
  border-radius: 0 0 12px 12px;
  margin-top: 0;
}

.order-section .admin-table tbody tr:first-child td {
  padding-top: 20px;
}

.order-section .admin-table tbody tr:last-child td {
  padding-bottom: 20px;
}

.order-section .admin-form {
  padding: 20px 25px;
  background: #f8f9fa;
  border-radius: 0 0 12px 12px;
}

@media (max-width: 768px) {
  .order-details-grid {
    grid-template-columns: 1fr;
  }
}

.admin-alert {
  padding: 15px 20px;
  border-radius: 8px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 10px;
}

.admin-alert-success {
  background: #d4edda;
  color: #155724;
  border-left: 4px solid #28a745;
}

.admin-alert-error {
  background: #f8d7da;
  color: #721c24;
  border-left: 4px solid #f5365c;
}

.btn-success {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}
</style>

<?php require_once "../includes/footer.php"; ?>
