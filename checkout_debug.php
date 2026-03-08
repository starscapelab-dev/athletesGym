<?php
// Temporary debug version of checkout_process.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Checkout Debug Mode</h1>";
echo "<pre>";

echo "=== POST Data ===\n";
print_r($_POST);

echo "\n=== SESSION Data ===\n";
session_start();
print_r($_SESSION);

echo "\n=== SERVER Info ===\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "\n";

echo "\n=== Files Included? ===\n";

try {
    require_once __DIR__ . "/includes/session.php";
    echo "✓ session.php loaded\n";
} catch (Exception $e) {
    echo "✗ session.php ERROR: " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . "/admin/includes/db.php";
    echo "✓ db.php loaded\n";
    echo "PDO: " . ($pdo ? "Connected" : "Not connected") . "\n";
} catch (Exception $e) {
    echo "✗ db.php ERROR: " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . "/includes/cart_functions.php";
    echo "✓ cart_functions.php loaded\n";
} catch (Exception $e) {
    echo "✗ cart_functions.php ERROR: " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . "/layouts/config.php";
    echo "✓ config.php loaded\n";
    echo "BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . "\n";
} catch (Exception $e) {
    echo "✗ config.php ERROR: " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . "/includes/csrf.php";
    echo "✓ csrf.php loaded\n";
} catch (Exception $e) {
    echo "✗ csrf.php ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== CSRF Check ===\n";
if (isset($_POST['csrf_token'])) {
    echo "CSRF Token in POST: " . substr($_POST['csrf_token'], 0, 20) . "...\n";
    echo "CSRF Token in SESSION: " . (isset($_SESSION['csrf_token']) ? substr($_SESSION['csrf_token'], 0, 20) . "..." : "NOT SET") . "\n";

    if (isset($_SESSION['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        echo "✓ CSRF tokens MATCH\n";
    } else {
        echo "✗ CSRF tokens DO NOT MATCH!\n";
    }
} else {
    echo "✗ No CSRF token in POST data\n";
}

echo "\n=== Auth Check ===\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not logged in') . "\n";
echo "Guest: " . (isset($_SESSION['guest']) ? 'Yes' : 'No') . "\n";

echo "\n=== Cart Check ===\n";
try {
    if (isset($pdo)) {
        $items = getCartItems($pdo);
        if ($items) {
            echo "✓ Cart has " . count($items) . " item(s)\n";
            foreach ($items as $item) {
                echo "  - " . $item['name'] . " x" . $item['quantity'] . " = " . $item['price'] . " QR\n";
            }
        } else {
            echo "✗ Cart is empty\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Cart ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Form Fields ===\n";
$requiredFields = ['name', 'email', 'phone', 'address'];
foreach ($requiredFields as $field) {
    $value = $_POST[$field] ?? '';
    echo "$field: " . ($value ? "✓ Present" : "✗ Missing") . "\n";
}

echo "\n=== Next Steps ===\n";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "This is a POST request - checkout_process.php should run\n";

    if (!isset($_POST['csrf_token'])) {
        echo "⚠ ISSUE: No CSRF token - form submission will fail\n";
    } elseif (!isset($_SESSION['csrf_token'])) {
        echo "⚠ ISSUE: No CSRF token in session - validation will fail\n";
    } elseif ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "⚠ ISSUE: CSRF tokens don't match - validation will fail\n";
    } else {
        echo "✓ Should proceed to payment processing\n";
    }
} else {
    echo "This is a GET request - form not submitted yet\n";
}

echo "</pre>";

echo "<hr>";
echo "<h2>Test Form Submission</h2>";
echo "<form method='POST' action='checkout_debug.php'>";
echo "<input type='hidden' name='csrf_token' value='" . ($_SESSION['csrf_token'] ?? generateCsrfToken()) . "'>";
echo "<input type='text' name='name' placeholder='Name' required><br>";
echo "<input type='email' name='email' placeholder='Email' required><br>";
echo "<input type='text' name='phone' placeholder='Phone' required><br>";
echo "<textarea name='address' placeholder='Address' required></textarea><br>";
echo "<button type='submit'>Test Submit</button>";
echo "</form>";

echo "<hr>";
echo "<a href='checkout.php'>← Back to Checkout</a> | ";
echo "<a href='cart.php'>View Cart</a>";
?>
