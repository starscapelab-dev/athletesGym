<?php
$pageTitle = "Manage Users";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

// Get success/error messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Search and filter parameters
$search = $_GET['search'] ?? '';
$gender_filter = $_GET['gender'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query with filters
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(name LIKE :search OR email LIKE :search OR phone LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($gender_filter)) {
    $where[] = "gender = :gender";
    $params[':gender'] = $gender_filter;
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$countSql = "SELECT COUNT(*) FROM users $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

// Get users with pagination
$sql = "SELECT id, name, email, phone, gender, dob, country, city, created_at
        FROM users
        $whereClause
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Get user statistics
$stats = $pdo->query("SELECT
    COUNT(*) as total,
    COUNT(CASE WHEN gender = 'male' THEN 1 END) as male_count,
    COUNT(CASE WHEN gender = 'female' THEN 1 END) as female_count,
    COUNT(CASE WHEN newsletter = 1 THEN 1 END) as newsletter_count
    FROM users")->fetch();
?>

<div class="page-header">
    <h1>Manage Users</h1>
    <div class="page-actions">
        <span class="user-count">Total: <?= $totalUsers ?> users</span>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-box">
        <span class="stat-label">Total Users</span>
        <span class="stat-value"><?= $stats['total'] ?></span>
    </div>
    <div class="stat-box">
        <span class="stat-label">Male</span>
        <span class="stat-value"><?= $stats['male_count'] ?></span>
    </div>
    <div class="stat-box">
        <span class="stat-label">Female</span>
        <span class="stat-value"><?= $stats['female_count'] ?></span>
    </div>
    <div class="stat-box">
        <span class="stat-label">Newsletter Subscribed</span>
        <span class="stat-value"><?= $stats['newsletter_count'] ?></span>
    </div>
</div>

<!-- Search and Filter -->
<div class="filter-section">
    <form method="GET" action="" class="filter-form">
        <div class="form-row">
            <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?= htmlspecialchars($search) ?>" class="search-input">

            <select name="gender" class="filter-select">
                <option value="">All Genders</option>
                <option value="male" <?= $gender_filter === 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $gender_filter === 'female' ? 'selected' : '' ?>>Female</option>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="list.php" class="btn btn-secondary">Clear</a>
        </div>
    </form>
</div>

<!-- Users Table -->
<?php if (empty($users)): ?>
    <div class="no-results">
        <p>No users found.</p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Location</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($user['gender']): ?>
                                <span class="badge badge-<?= $user['gender'] === 'male' ? 'blue' : 'pink' ?>">
                                    <?= ucfirst($user['gender']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-gray">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(($user['city'] ?? '') . ($user['country'] ? ', ' . $user['country'] : '')) ?: 'N/A' ?></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td class="actions">
                            <a href="view.php?id=<?= $user['id'] ?>" class="btn-action btn-view" title="View Details">👁</a>
                            <a href="edit.php?id=<?= $user['id'] ?>" class="btn-action btn-edit" title="Edit">✏</a>
                            <a href="delete.php?id=<?= $user['id'] ?>" class="btn-action btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">🗑</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&gender=<?= urlencode($gender_filter) ?>" class="page-link">Previous</a>
            <?php endif; ?>

            <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&gender=<?= urlencode($gender_filter) ?>" class="page-link">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
