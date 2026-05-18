<?php
// Diagnostic script to identify the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "=== Diagnostic Test for add.php ===\n\n";

// Test 1: Check if required files exist
echo "1. Checking required files:\n";
$files = [
    'db.php' => __DIR__ . "/../includes/db.php",
    'functions.php' => __DIR__ . "/../includes/functions.php",
    'config.php' => __DIR__ . '/../../layouts/config.php',
    'csrf.php' => __DIR__ . '/../../includes/csrf.php'
];

foreach ($files as $name => $path) {
    echo "   - $name: " . (file_exists($path) ? "✓ EXISTS" : "✗ MISSING") . " ($path)\n";
}

echo "\n2. Testing file includes:\n";
try {
    require_once __DIR__ . "/../includes/db.php";
    echo "   - db.php: ✓ Loaded successfully\n";
} catch (Exception $e) {
    echo "   - db.php: ✗ ERROR - " . $e->getMessage() . "\n";
    exit;
}

try {
    require_once __DIR__ . "/../includes/functions.php";
    echo "   - functions.php: ✓ Loaded successfully\n";
} catch (Exception $e) {
    echo "   - functions.php: ✗ ERROR - " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . '/../../layouts/config.php';
    echo "   - config.php: ✓ Loaded successfully\n";
    echo "   - BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "\n";
} catch (Exception $e) {
    echo "   - config.php: ✗ ERROR - " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . '/../../includes/csrf.php';
    echo "   - csrf.php: ✓ Loaded successfully\n";
} catch (Exception $e) {
    echo "   - csrf.php: ✗ ERROR - " . $e->getMessage() . "\n";
}

echo "\n3. Testing database connection:\n";
try {
    if (isset($pdo)) {
        echo "   - PDO connection: ✓ Connected\n";

        // Test queries
        $cats = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
        echo "   - Categories query: ✓ Success (" . count($cats) . " categories)\n";

        $sizes = $pdo->query("SELECT id, name FROM sizes ORDER BY sort_order, name")->fetchAll();
        echo "   - Sizes query: ✓ Success (" . count($sizes) . " sizes)\n";

        $colors = $pdo->query("SELECT id, name, hex_code FROM colors ORDER BY name")->fetchAll();
        echo "   - Colors query: ✓ Success (" . count($colors) . " colors)\n";
    } else {
        echo "   - PDO connection: ✗ Not initialized\n";
    }
} catch (Exception $e) {
    echo "   - Database error: ✗ " . $e->getMessage() . "\n";
}

echo "\n4. Testing CSRF functions:\n";
try {
    if (function_exists('getCsrfToken')) {
        $token = getCsrfToken();
        echo "   - getCsrfToken(): ✓ Works (token length: " . strlen($token) . ")\n";
    } else {
        echo "   - getCsrfToken(): ✗ Function not defined\n";
    }

    if (function_exists('csrfField')) {
        echo "   - csrfField(): ✓ Function exists\n";
    } else {
        echo "   - csrfField(): ✗ Function not defined\n";
    }
} catch (Exception $e) {
    echo "   - CSRF error: ✗ " . $e->getMessage() . "\n";
}

echo "\n5. Testing sanitize function:\n";
try {
    if (function_exists('sanitize')) {
        $test = sanitize("<script>test</script>");
        echo "   - sanitize(): ✓ Works\n";
    } else {
        echo "   - sanitize(): ✗ Function not defined\n";
    }
} catch (Exception $e) {
    echo "   - sanitize error: ✗ " . $e->getMessage() . "\n";
}

echo "\n6. Testing session:\n";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "   - Session: ✓ Active\n";
    echo "   - Session ID: " . session_id() . "\n";
} else {
    echo "   - Session: ✗ Not started\n";
}

echo "\n7. Testing header inclusion:\n";
try {
    $pageTitle = "Test Page";
    ob_start();
    require_once __DIR__ . "/../includes/header.php";
    $header_output = ob_get_clean();
    echo "   - header.php: ✓ Loaded successfully (" . strlen($header_output) . " bytes)\n";
} catch (Exception $e) {
    echo "   - header.php: ✗ ERROR - " . $e->getMessage() . "\n";
}

echo "\n=== All diagnostics complete ===\n";
echo "\nIf all tests pass, the add.php page should work.\n";
echo "Access this file at: " . (defined('BASE_URL') ? BASE_URL . 'admin/products/debug.php' : '/admin/products/debug.php') . "\n";
