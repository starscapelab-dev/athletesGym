<?php
// Error reporting is now handled in db.php via environment configuration
$pageTitle = "Dashboard";
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/includes/header.php"; // Header includes _session.php for admin auth
require_once __DIR__ . '/../layouts/config.php'; // ensure BASE_URL is loaded

// Fetch comprehensive statistics
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$sizeCount = $pdo->query("SELECT COUNT(*) FROM sizes")->fetchColumn();
$colorCount = $pdo->query("SELECT COUNT(*) FROM colors")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Order Statistics
$orderStats = $pdo->query("SELECT
    COUNT(*) as total_orders,
    COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN order_status = 'processing' THEN 1 END) as processing_orders,
    COUNT(CASE WHEN order_status = 'completed' THEN 1 END) as completed_orders,
    COUNT(CASE WHEN order_status = 'cancelled' THEN 1 END) as cancelled_orders
    FROM orders")->fetch();

// Sales Statistics
$salesStats = $pdo->query("SELECT
    SUM(total) as total_revenue,
    SUM(CASE WHEN order_status = 'completed' THEN total ELSE 0 END) as completed_revenue,
    AVG(total) as avg_order_value
    FROM orders")->fetch();

// Review Statistics
$reviewStats = $pdo->query("SELECT
    COUNT(*) as total_reviews,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_reviews,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_reviews,
    AVG(rating) as avg_rating
    FROM product_reviews")->fetch();

// Stock Alert (Low stock products) - products with stock <= 5
$lowStockCount = $pdo->query("SELECT COUNT(DISTINCT pv.product_id)
    FROM product_variants pv
    WHERE pv.stock > 0 AND pv.stock <= 5")->fetchColumn();

// Recent activity - Last 30 days
$recentStats = $pdo->query("SELECT
    COUNT(CASE WHEN o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as orders_last_30_days,
    COUNT(CASE WHEN u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as users_last_30_days,
    COUNT(CASE WHEN r.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as reviews_last_30_days
    FROM orders o
    CROSS JOIN users u
    CROSS JOIN product_reviews r")->fetch();

// Today's statistics
$todayStats = $pdo->query("SELECT
    COUNT(CASE WHEN DATE(o.created_at) = CURDATE() THEN 1 END) as orders_today,
    SUM(CASE WHEN DATE(o.created_at) = CURDATE() THEN o.total ELSE 0 END) as revenue_today
    FROM orders o")->fetch();

// Last 7 days sales data for chart
$last7Days = $pdo->query("SELECT
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(total) as revenue
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC")->fetchAll();

// Prepare chart data
$chartDates = [];
$chartOrders = [];
$chartRevenue = [];
foreach ($last7Days as $day) {
    $chartDates[] = date('M d', strtotime($day['date']));
    $chartOrders[] = $day['orders'];
    $chartRevenue[] = round($day['revenue'], 2);
}

// Top 5 products by orders
$topProducts = $pdo->query("SELECT
    p.name,
    COUNT(oi.id) as order_count
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.id
    ORDER BY order_count DESC
    LIMIT 5")->fetchAll();

?>

<div class="modern-dashboard">
  <!-- Dashboard Header -->
  <div class="dashboard-header-bar">
    <div>
      <h1 class="dashboard-main-title">Dashboard Overview</h1>
      <p class="dashboard-subtitle"><?= date('l, F j, Y') ?></p>
    </div>
  </div>

  <!-- Key Metrics Cards -->
  <div class="metrics-grid">
    <div class="metric-card metric-primary">
      <div class="metric-icon">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="metric-content">
        <h3 class="metric-label">Total Revenue</h3>
        <p class="metric-value"><?= number_format($salesStats['total_revenue'] ?? 0, 2) ?> <span class="currency">QAR</span></p>
        <p class="metric-change positive"><i class="fas fa-arrow-up"></i> All time</p>
      </div>
    </div>

    <div class="metric-card metric-success">
      <div class="metric-icon">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="metric-content">
        <h3 class="metric-label">Total Orders</h3>
        <p class="metric-value"><?= $orderStats['total_orders'] ?></p>
        <p class="metric-change"><i class="fas fa-calendar"></i> <?= $recentStats['orders_last_30_days'] ?? 0 ?> this month</p>
      </div>
    </div>

    <div class="metric-card metric-info">
      <div class="metric-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="metric-content">
        <h3 class="metric-label">Total Users</h3>
        <p class="metric-value"><?= $userCount ?></p>
        <p class="metric-change positive"><i class="fas fa-user-plus"></i> <?= $recentStats['users_last_30_days'] ?? 0 ?> new</p>
      </div>
    </div>

    <div class="metric-card <?= $lowStockCount > 0 ? 'metric-warning' : 'metric-neutral' ?>">
      <div class="metric-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="metric-content">
        <h3 class="metric-label">Low Stock Alert</h3>
        <p class="metric-value"><?= $lowStockCount ?></p>
        <p class="metric-change"><?= $lowStockCount > 0 ? 'Needs attention' : 'All stocked' ?></p>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="charts-row">
    <div class="chart-card">
      <div class="chart-header">
        <h2 class="chart-title"><i class="fas fa-chart-line"></i> Sales Overview (Last 7 Days)</h2>
      </div>
      <div class="chart-container">
        <canvas id="salesChart"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-header">
        <h2 class="chart-title"><i class="fas fa-chart-pie"></i> Order Status Distribution</h2>
      </div>
      <div class="chart-container">
        <canvas id="orderStatusChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Data Tables Row -->
  <div class="data-row">
    <!-- Order Status Table -->
    <div class="data-card">
      <div class="data-header">
        <h2 class="data-title"><i class="fas fa-box"></i> Orders by Status</h2>
        <a href="orders/list.php" class="data-link">View All <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="status-list">
        <div class="status-item">
          <div class="status-info">
            <i class="fas fa-clock status-icon status-pending"></i>
            <span class="status-name">Pending</span>
          </div>
          <span class="status-count"><?= $orderStats['pending_orders'] ?></span>
        </div>
        <div class="status-item">
          <div class="status-info">
            <i class="fas fa-sync status-icon status-processing"></i>
            <span class="status-name">Processing</span>
          </div>
          <span class="status-count"><?= $orderStats['processing_orders'] ?></span>
        </div>
        <div class="status-item">
          <div class="status-info">
            <i class="fas fa-check-circle status-icon status-completed"></i>
            <span class="status-name">Completed</span>
          </div>
          <span class="status-count"><?= $orderStats['completed_orders'] ?></span>
        </div>
        <div class="status-item">
          <div class="status-info">
            <i class="fas fa-times-circle status-icon status-cancelled"></i>
            <span class="status-name">Cancelled</span>
          </div>
          <span class="status-count"><?= $orderStats['cancelled_orders'] ?></span>
        </div>
      </div>
    </div>

    <!-- Top Products -->
    <div class="data-card">
      <div class="data-header">
        <h2 class="data-title"><i class="fas fa-star"></i> Top Products</h2>
        <a href="products/list.php" class="data-link">View All <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="product-list">
        <?php if (empty($topProducts)): ?>
          <p class="no-data-text">No product data available</p>
        <?php else: ?>
          <?php foreach ($topProducts as $index => $product): ?>
            <div class="product-item">
              <span class="product-rank">#<?= $index + 1 ?></span>
              <span class="product-name"><?= htmlspecialchars($product['name']) ?></span>
              <span class="product-orders"><?= $product['order_count'] ?> orders</span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="data-card">
      <div class="data-header">
        <h2 class="data-title"><i class="fas fa-tachometer-alt"></i> Quick Stats</h2>
      </div>
      <div class="quick-stats-list">
        <div class="quick-stat-item">
          <i class="fas fa-box-open quick-stat-icon"></i>
          <div class="quick-stat-info">
            <span class="quick-stat-label">Products</span>
            <span class="quick-stat-value"><?= $productCount ?></span>
          </div>
        </div>
        <div class="quick-stat-item">
          <i class="fas fa-folder quick-stat-icon"></i>
          <div class="quick-stat-info">
            <span class="quick-stat-label">Categories</span>
            <span class="quick-stat-value"><?= $categoryCount ?></span>
          </div>
        </div>
        <div class="quick-stat-item">
          <i class="fas fa-star-half-alt quick-stat-icon"></i>
          <div class="quick-stat-info">
            <span class="quick-stat-label">Reviews</span>
            <span class="quick-stat-value"><?= $reviewStats['total_reviews'] ?> (<?= number_format($reviewStats['avg_rating'] ?? 0, 1) ?> avg)</span>
          </div>
        </div>
        <div class="quick-stat-item">
          <i class="fas fa-chart-bar quick-stat-icon"></i>
          <div class="quick-stat-info">
            <span class="quick-stat-label">Avg Order Value</span>
            <span class="quick-stat-value"><?= number_format($salesStats['avg_order_value'] ?? 0, 2) ?> QAR</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Sales Overview Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartDates) ?>,
        datasets: [{
            label: 'Revenue (QAR)',
            data: <?= json_encode($chartRevenue) ?>,
            borderColor: '#21335b',
            backgroundColor: 'rgba(33, 51, 91, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Order Status Pie Chart
const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
        datasets: [{
            data: [
                <?= $orderStats['pending_orders'] ?>,
                <?= $orderStats['processing_orders'] ?>,
                <?= $orderStats['completed_orders'] ?>,
                <?= $orderStats['cancelled_orders'] ?>
            ],
            backgroundColor: [
                '#ffc107',
                '#17a2b8',
                '#28a745',
                '#dc3545'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            }
        }
    }
});
</script>

<?php require_once "includes/footer.php"; ?>
