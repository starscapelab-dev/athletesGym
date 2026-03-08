<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Product Reviews";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$stmt = $pdo->query("
  SELECT r.*, p.name AS product_name, u.name
  FROM product_reviews r
  JOIN products p ON r.product_id = p.id
  LEFT JOIN users u ON r.user_id = u.id
  ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<h1>Customer Reviews</h1>

<table class="admin-table">
  <thead>
    <tr>
      <th>Product</th>
      <th>User</th>
      <th>Rating</th>
      <th>Review</th>
      <th>Status</th>
      <th>Verified</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($reviews as $r): ?>
    <tr>
      <td><?= htmlspecialchars($r['product_name']) ?></td>
      <td><?= htmlspecialchars($r['full_name'] ?? 'Guest') ?></td>
      <td><?= $r['rating'] ?>★</td>
      <td><?= htmlspecialchars(substr($r['review_text'], 0, 50)) ?>...</td>
      <td><?= ucfirst($r['status']) ?></td>
      <td><?= $r['is_verified'] ? '✔' : '-' ?></td>
      <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
      <td>
        <div class="actions">
          <a href="update_status.php?id=<?= $r['id'] ?>&status=approved" class="btn btn-success">Approve</a>
          <a href="update_status.php?id=<?= $r['id'] ?>&status=rejected" class="btn btn-delete">Reject</a>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
