<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Order Details";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='admin-error'>Order not found.</div>";
    require_once "../includes/footer.php";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

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
  </div>
</div>

<h2 class="admin-section-title" style="margin-top: 40px;">Items</h2>
  <table class="admin-table">
    <thead>
      <tr>
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
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td><?= htmlspecialchars($item['color']) ?> / <?= htmlspecialchars($item['size']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= number_format($item['price'], 2) ?></td>
        <td><?= number_format($item['total'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

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
</style>

<?php require_once "../includes/footer.php"; ?>
