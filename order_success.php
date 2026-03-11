<?php
require_once __DIR__ . "/layouts/header-item.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/includes/cart_functions.php";
require_once __DIR__ . "/layouts/config.php";

// Session already started by header-item.php

// Check if preview mode is enabled (localhost only)
$isPreview = isset($_GET['preview']) && $_GET['preview'] == '1' && 
             in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:80', 'localhost:8080', '127.0.0.1']);

if ($isPreview) {
    // Mock data for preview
    $order = [
        'id' => 12345,
        'full_name' => 'Ahmed Al-Mansouri',
        'email' => 'ahmed@example.com',
        'phone' => '+974 3366 1234',
        'total' => 450.00,
        'order_status' => 'processing',
        'payment_status' => 'paid',
        'created_at' => date('Y-m-d H:i:s'),
        'customer_id' => 1,
        'session_id' => session_id()
    ];
    $items = [
        [
            'product_name' => 'Premium Protein Powder',
            'color' => 'Vanilla',
            'size' => '1kg',
            'quantity' => 2,
            'price' => 150.00,
            'total' => 300.00
        ],
        [
            'product_name' => 'Gym T-Shirt',
            'color' => 'Black',
            'size' => 'Large',
            'quantity' => 1,
            'price' => 75.00,
            'total' => 75.00
        ],
        [
            'product_name' => 'Workout Shorts',
            'color' => 'Blue',
            'size' => 'XL',
            'quantity' => 1,
            'price' => 75.00,
            'total' => 75.00
        ]
    ];
} else {
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
}
?>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  .order-success-wrapper {
    background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
    min-height: 100vh;
    padding: 120px 20px 40px;
    margin-top: 0;
  }

  .order-success {
    max-width: 900px;
    margin: 0 auto;
  }

  /* Success Header */
  .success-header {
    text-align: center;
    margin-bottom: 50px;
  }

  .success-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #2a9d8f 0%, #1e7a6f 100%);
    border-radius: 50%;
    color: white;
    font-size: 40px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(42, 157, 143, 0.3);
    animation: scaleIn 0.6s ease-out;
  }

  @keyframes scaleIn {
    from {
      transform: scale(0.5);
      opacity: 0;
    }
    to {
      transform: scale(1);
      opacity: 1;
    }
  }

  .success-header h1 {
    font-size: 2.5rem;
    color: #1a1a1a;
    margin-bottom: 10px;
    font-weight: 700;
    letter-spacing: -0.5px;
  }

  .success-header p {
    font-size: 1.1rem;
    color: #666;
    font-weight: 400;
  }

  .order-number {
    font-size: 1.3rem;
    color: #2a9d8f;
    font-weight: 600;
    margin-top: 15px;
  }

  /* Main Content Grid */
  .order-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 50px;
  }

  @media (max-width: 768px) {
    .order-content {
      grid-template-columns: 1fr;
      gap: 30px;
    }
  }

  /* Card Styling */
  .card {
    background: white;
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: box-shadow 0.3s ease;
  }

  .card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }

  .card h2 {
    font-size: 1.2rem;
    color: #1a1a1a;
    margin-bottom: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
  }

  .card-content {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 12px;
    border-bottom: 1px solid #f5f5f5;
  }

  .info-row:last-child {
    border-bottom: none;
  }

  .info-label {
    color: #999;
    font-size: 0.9rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .info-value {
    color: #1a1a1a;
    font-size: 1rem;
    font-weight: 500;
  }

  .info-value a {
    color: #2a9d8f;
    text-decoration: none;
  }

  .info-value a:hover {
    text-decoration: underline;
  }

  /* Status Badges */
  .status-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 15px;
  }

  .status-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    min-width: 150px;
  }

  .status-label {
    color: #999;
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: capitalize;
    text-align: center;
    width: 100%;
  }

  .status-paid {
    background: #e8f7f3;
    color: #1e7a6f;
    border: 1px solid #d0f0eb;
  }

  .status-pending {
    background: #fff9e6;
    color: #b8860b;
    border: 1px solid #ffeaa7;
  }

  .status-failed {
    background: #ffe8e8;
    color: #c72c2c;
    border: 1px solid #ffc9c9;
  }

  .status-processing {
    background: #e3f2fd;
    color: #1565c0;
    border: 1px solid #bbdefb;
  }

  /* Order Items */
  .order-items-section {
    grid-column: 1 / -1;
  }

  .items-container {
    display: grid;
    gap: 15px;
  }

  .item-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 20px;
    align-items: center;
  }

  @media (max-width: 600px) {
    .item-card {
      grid-template-columns: 1fr;
      gap: 15px;
    }
  }

  .item-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .item-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
  }

  .item-specs {
    display: flex;
    gap: 20px;
    font-size: 0.9rem;
    color: #666;
  }

  .item-spec {
    display: flex;
    gap: 5px;
  }

  .item-spec-label {
    color: #999;
    font-weight: 500;
  }

  .item-price {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .item-qty {
    font-size: 0.9rem;
    color: #666;
    line-height: 1;
  }

  .item-total {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2a9d8f;
  }

  /* Total Section */
  .total-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    text-align: right;
    grid-column: 1 / -1;
  }

  .total-row {
    display: flex;
    justify-content: flex-end;
    padding: 12px 0;
    border-bottom: 1px solid #f5f5f5;
    gap: 20px;
  }

  .total-row:last-of-type {
    border-bottom: 2px solid #2a9d8f;
    padding: 15px 0;
    margin-top: 10px;
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a1a;
  }

  .total-label {
    color: #666;
    flex: 1;
    text-align: left;
  }

  .total-value {
    min-width: 120px;
    color: #1a1a1a;
    font-weight: 600;
  }

  /* CTA Buttons */
  .cta-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 50px;
  }

  @media (max-width: 600px) {
    .cta-buttons {
      flex-direction: column;
      gap: 12px;
    }
  }

  .btn {
    padding: 14px 40px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #2a9d8f 0%, #1e7a6f 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(42, 157, 143, 0.4);
  }

  .btn-secondary {
    background: white;
    color: #2a9d8f;
    border: 2px solid #2a9d8f;
  }

  .btn-secondary:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
  }

  /* Support Info */
  .support-info {
    background: white;
    border-radius: 12px;
    padding: 30px;
    margin-top: 50px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  }

  .support-info h3 {
    color: #1a1a1a;
    margin-bottom: 12px;
    font-size: 1.1rem;
  }

  .support-info p {
    color: #666;
    font-size: 1rem;
  }

  .support-info a {
    color: #2a9d8f;
    text-decoration: none;
    font-weight: 600;
  }

  .support-info a:hover {
    text-decoration: underline;
  }
</style>

<div class="order-success-wrapper">
  <div class="order-success">
    <!-- Success Header -->
    <div class="success-header">
      <div class="success-icon">✓</div>
      <h1>Order Confirmed</h1>
      <p>Thank you for your purchase! Your order has been received.</p>
      <div class="order-number">Order #<?= htmlspecialchars($order['id']) ?></div>
    </div>

    <!-- Order Content -->
    <div class="order-content">
      <!-- Customer Info Card -->
      <div class="card">
        <h2>Customer Info</h2>
        <div class="card-content">
          <div class="info-row">
            <span class="info-label">Name</span>
            <span class="info-value"><?= htmlspecialchars($order['full_name']) ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value"><a href="mailto:<?= htmlspecialchars($order['email']) ?>"><?= htmlspecialchars($order['email']) ?></a></span>
          </div>
          <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value"><?= htmlspecialchars($order['phone']) ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Date</span>
            <span class="info-value"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
          </div>
        </div>
      </div>

      <!-- Status Info Card -->
      <div class="card">
        <h2>Order Status</h2>
        <div class="status-row">
          <div class="status-item">
            <span class="status-label">Payment</span>
            <span class="status-badge status-<?= htmlspecialchars($order['payment_status']) ?>">
              <?= htmlspecialchars($order['payment_status']) ?>
            </span>
          </div>
          <div class="status-item">
            <span class="status-label">Order</span>
            <span class="status-badge status-<?= htmlspecialchars($order['order_status']) ?>">
              <?= htmlspecialchars($order['order_status']) ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="order-items-section">
        <div class="card">
          <h2>Order Items</h2>
          <div class="items-container">
            <?php foreach ($items as $it): ?>
            <div class="item-card">
              <div class="item-details">
                <div class="item-name"><?= htmlspecialchars($it['product_name']) ?></div>
                <div class="item-specs">
                  <?php if ($it['color']): ?>
                  <div class="item-spec">
                    <span class="item-spec-label">Color:</span> <?= htmlspecialchars($it['color']) ?>
                  </div>
                  <?php endif; ?>
                  <?php if ($it['size']): ?>
                  <div class="item-spec">
                    <span class="item-spec-label">Size:</span> <?= htmlspecialchars($it['size']) ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="item-price">
                <div class="item-qty">Qty: <?= $it['quantity'] ?> × <?= number_format($it['price'], 2) ?> QR</div>
                <div class="item-total"><?= number_format($it['total'], 2) ?> QR</div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Total Section -->
      <div class="total-section">
        <div class="total-row">
          <span class="total-label">Grand Total</span>
          <span class="total-value"><?= number_format($order['total'], 2) ?> QR</span>
        </div>
      </div>
    </div>

    <!-- CTA Buttons -->
    <div class="cta-buttons">
      <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary">Continue Shopping</a>
      <a href="<?= BASE_URL ?>account/profile.php" class="btn btn-secondary">View My Orders</a>
    </div>

    <!-- Support Info -->
    <div class="support-info">
      <h3>Need Help?</h3>
      <p>If you have any questions about your order, please <a href="<?= BASE_URL ?>contact.php">contact us</a> or email <a href="mailto:info@athletesgym.qa">info@athletesgym.qa</a></p>
    </div>
  </div>
</div>

<?php require_once "layouts/footer.php"; ?>
