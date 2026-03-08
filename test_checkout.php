<?php
session_start();
require_once "admin/includes/db.php";
require_once "includes/cart_functions.php";
require_once "layouts/config.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        pre { background: #333; color: #0f0; padding: 10px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #21335b; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Checkout Page Diagnostic</h1>

    <div class="info">
        <strong>Base URL:</strong> <?= BASE_URL ?><br>
        <strong>Current Page:</strong> <?= $_SERVER['REQUEST_URI'] ?><br>
        <strong>Time:</strong> <?= date('Y-m-d H:i:s') ?>
    </div>

    <hr>

    <h2>1. Session Information</h2>
    <table>
        <tr>
            <th>Key</th>
            <th>Value</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>Session ID</td>
            <td><?= session_id() ?></td>
            <td class="success">✓ Active</td>
        </tr>
        <tr>
            <td>User ID</td>
            <td><?= $_SESSION['user_id'] ?? 'Not set' ?></td>
            <td class="<?= isset($_SESSION['user_id']) ? 'success' : 'error' ?>">
                <?= isset($_SESSION['user_id']) ? '✓ Logged In' : '✗ Not Logged In' ?>
            </td>
        </tr>
        <tr>
            <td>User Name</td>
            <td><?= $_SESSION['user_name'] ?? 'N/A' ?></td>
            <td><?= isset($_SESSION['user_name']) ? '✓' : '-' ?></td>
        </tr>
        <tr>
            <td>User Email</td>
            <td><?= $_SESSION['user_email'] ?? 'N/A' ?></td>
            <td><?= isset($_SESSION['user_email']) ? '✓' : '-' ?></td>
        </tr>
        <tr>
            <td>Guest Session</td>
            <td><?= isset($_SESSION['guest']) ? 'Yes' : 'No' ?></td>
            <td class="<?= isset($_SESSION['guest']) ? 'success' : 'info' ?>">
                <?= isset($_SESSION['guest']) ? '✓ Guest Mode' : '-' ?>
            </td>
        </tr>
    </table>

    <hr>

    <h2>2. Cart Items Check</h2>
    <?php
    $items = getCartItems($pdo);

    if ($items && count($items) > 0) {
        echo "<p class='success'>✓ Cart has " . count($items) . " item(s)</p>";
        echo "<table>";
        echo "<tr><th>Product</th><th>Size</th><th>Color</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";

        $total = 0;
        foreach ($items as $item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $total += $lineTotal;
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['name']) . "</td>";
            echo "<td>" . htmlspecialchars($item['size'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($item['color'] ?? '-') . "</td>";
            echo "<td>" . $item['quantity'] . "</td>";
            echo "<td>" . number_format($item['price'], 2) . " QR</td>";
            echo "<td>" . number_format($lineTotal, 2) . " QR</td>";
            echo "</tr>";
        }
        echo "<tr><th colspan='5'>TOTAL</th><th>" . number_format($total, 2) . " QR</th></tr>";
        echo "</table>";

        echo "<p class='success'>✓ Ready for checkout!</p>";
    } else {
        echo "<p class='error'>✗ Cart is empty</p>";

        // Check database directly
        echo "<h3>Direct Database Check:</h3>";
        $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE session_id = ?");
        $stmt->execute([session_id()]);
        $dbItems = $stmt->fetchAll();

        if ($dbItems) {
            echo "<p class='info'>Found items in database but getCartItems() returned empty:</p>";
            echo "<pre>";
            print_r($dbItems);
            echo "</pre>";
        } else {
            echo "<p class='error'>No items in database for this session</p>";
        }
    }
    ?>

    <hr>

    <h2>3. Database Connection Test</h2>
    <?php
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $productCount = $stmt->fetch()['count'];
        echo "<p class='success'>✓ Database connected - $productCount products in database</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
    }
    ?>

    <hr>

    <h2>4. Recommendations</h2>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <p class='error'>⚠ You're not logged in!</p>
        <p><a href="<?= BASE_URL ?>auth/login.php" style="padding: 10px 20px; background: #21335b; color: white; text-decoration: none; border-radius: 4px;">Login Now</a></p>
        <p>Or <a href="<?= BASE_URL ?>auth/register.php">Register a new account</a></p>
    <?php endif; ?>

    <?php if (!$items || count($items) === 0): ?>
        <p class='error'>⚠ Your cart is empty!</p>
        <p><a href="<?= BASE_URL ?>shop.php" style="padding: 10px 20px; background: #21335b; color: white; text-decoration: none; border-radius: 4px;">Go to Shop</a></p>
    <?php else: ?>
        <p class='success'>✓ Everything looks good!</p>
        <p><a href="<?= BASE_URL ?>checkout.php" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">Proceed to Checkout</a></p>
    <?php endif; ?>

    <hr>

    <h2>5. Quick Links</h2>
    <ul>
        <li><a href="<?= BASE_URL ?>">Home</a></li>
        <li><a href="<?= BASE_URL ?>shop.php">Shop</a></li>
        <li><a href="<?= BASE_URL ?>cart.php">Cart</a></li>
        <li><a href="<?= BASE_URL ?>checkout.php">Checkout</a></li>
        <li><a href="<?= BASE_URL ?>auth/login.php">Login</a></li>
        <li><a href="<?= BASE_URL ?>auth/register.php">Register</a></li>
    </ul>

    <hr>

    <h2>6. Raw Session Data</h2>
    <pre><?php print_r($_SESSION); ?></pre>

</body>
</html>
