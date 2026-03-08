<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Orders";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="admin-page-header">
  <h1>Orders</h1>
</div>

<div class="admin-table-container">
  <table class="admin-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Total (QAR)</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
      $orders = $stmt->fetchAll();

      if (!$orders) {
          echo "<tr><td colspan='9' style='text-align:center;'>No orders found.</td></tr>";
      } else {
          foreach ($orders as $order): ?>
          <tr>
            <td data-label="#">#<?= $order['id'] ?></td>
            <td data-label="Customer"><?= htmlspecialchars($order['full_name']) ?></td>
            <td data-label="Email"><?= htmlspecialchars($order['email']) ?></td>
            <td data-label="Phone"><?= htmlspecialchars($order['phone']) ?></td>
            <td data-label="Total"><?= number_format($order['total'], 2) ?> QAR</td>
            <td data-label="Payment"><span class="status-badge status-<?= strtolower($order['payment_status']) ?>"><?= ucfirst($order['payment_status']) ?></span></td>
            <td data-label="Status"><span class="status-badge status-<?= strtolower($order['order_status']) ?>"><?= ucfirst($order['order_status']) ?></span></td>
            <td data-label="Date"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
            <td data-label="Action">
              <a href="view.php?id=<?= $order['id'] ?>" class="btn btn-primary">View</a>
            </td>
          </tr>
      <?php endforeach; } ?>
    </tbody>
  </table>
</div>

<style>
.admin-table-container {
  overflow-x: auto;
  margin-top: 20px;
  -webkit-overflow-scrolling: touch;
}
</style>

<?php require_once "../includes/footer.php"; ?>
