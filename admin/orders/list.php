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
  <div class="bulk-actions-bar" id="bulkActionsBar" style="display: none;">
    <span id="selectedCount">0 selected</span>
    <button type="button" class="btn btn-danger" id="bulkDeleteBtn">Delete Selected</button>
  </div>
</div>

<div class="admin-table-container">
  <form id="ordersForm">
    <table class="admin-table">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
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
            echo "<tr><td colspan='10' style='text-align:center;'>No orders found.</td></tr>";
        } else {
            foreach ($orders as $order): ?>
            <tr>
              <td><input type="checkbox" class="order-checkbox" name="order_ids[]" value="<?= $order['id'] ?>"></td>
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
  </form>
</div>

<style>
.admin-table-container {
  overflow-x: auto;
  margin-top: 20px;
  -webkit-overflow-scrolling: touch;
}

.admin-page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
}

.bulk-actions-bar {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 10px 15px;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 5px;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.bulk-actions-bar span {
  font-weight: 600;
  color: #495057;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-danger:hover {
  background-color: #c82333;
}

.order-checkbox, #selectAll {
  cursor: pointer;
  width: 18px;
  height: 18px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const selectAllCheckbox = document.getElementById('selectAll');
  const orderCheckboxes = document.querySelectorAll('.order-checkbox');
  const bulkActionsBar = document.getElementById('bulkActionsBar');
  const selectedCountSpan = document.getElementById('selectedCount');
  const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

  // Select all functionality
  selectAllCheckbox.addEventListener('change', function() {
    orderCheckboxes.forEach(checkbox => {
      checkbox.checked = this.checked;
    });
    updateBulkActionsBar();
  });

  // Individual checkbox change
  orderCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      updateSelectAllState();
      updateBulkActionsBar();
    });
  });

  // Update select all checkbox state
  function updateSelectAllState() {
    const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
    selectAllCheckbox.checked = checkedCount === orderCheckboxes.length && checkedCount > 0;
    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < orderCheckboxes.length;
  }

  // Update bulk actions bar visibility
  function updateBulkActionsBar() {
    const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
    if (checkedCount > 0) {
      bulkActionsBar.style.display = 'flex';
      selectedCountSpan.textContent = `${checkedCount} selected`;
    } else {
      bulkActionsBar.style.display = 'none';
    }
  }

  // Bulk delete functionality
  bulkDeleteBtn.addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
    const orderIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (orderIds.length === 0) {
      alert('Please select at least one order to delete.');
      return;
    }

    if (!confirm(`Are you sure you want to delete ${orderIds.length} order(s)? This action cannot be undone.`)) {
      return;
    }

    // Send delete request
    fetch('delete_orders.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ order_ids: orderIds })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while deleting orders.');
    });
  });
});
</script>

<?php require_once "../includes/footer.php"; ?>
