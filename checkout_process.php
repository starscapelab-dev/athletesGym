<?php
ob_start(); // Start buffering early

// Error reporting is now handled in db.php via environment configuration

require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/admin/includes/db.php";
require_once __DIR__ . "/layouts/config.php";
require_once __DIR__ . "/includes/csrf.php";

// SECURITY: Validate CSRF token with detailed error handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token'])) {
        error_log("CSRF token missing in POST data");
        $_SESSION['checkout_error'] = "Security token missing. Please refresh the page and try again.";
        header("Location: checkout.php");
        exit;
    }

    if (!isset($_SESSION['csrf_token'])) {
        error_log("CSRF token missing in SESSION");
        $_SESSION['checkout_error'] = "Session expired. Please refresh the page and try again.";
        header("Location: checkout.php");
        exit;
    }

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token mismatch - POST: " . substr($_POST['csrf_token'], 0, 10) . "... SESSION: " . substr($_SESSION['csrf_token'], 0, 10) . "...");
        $_SESSION['checkout_error'] = "Security validation failed. Please try again.";
        header("Location: checkout.php");
        exit;
    }
}

// Guest checkout enabled - no login required

// Guest cart functions
function getGuestCartId($pdo) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id=? and status = 'active' LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $cart = $stmt->fetchColumn();
        if (!$cart) {
            $pdo->prepare("INSERT INTO carts (user_id, status) VALUES (?, 'active')")->execute([$_SESSION['user_id']]);
            return $pdo->lastInsertId();
        }
        return $cart;
    } else {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
            $pdo->prepare("INSERT INTO carts (session_id, status) VALUES (?, 'active')")->execute([$_SESSION['cart_session_id']]);
        }
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id=? and status = 'active' LIMIT 1");
        $stmt->execute([$_SESSION['cart_session_id']]);
        return $stmt->fetchColumn();
    }
}

function getGuestCartItems($pdo) {
    $cartId = getGuestCartId($pdo);
    if (!$cartId) return [];
    $stmt = $pdo->prepare("SELECT ci.id as cart_item_id, ci.quantity, p.name, p.price, siz.name as size, col.name as color, (
    SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_featured DESC, id ASC LIMIT 1
  ) AS image_path, ci.variant_id
                           FROM cart_items ci
                           JOIN products p ON ci.product_id=p.id
                           LEFT JOIN product_variants v ON ci.variant_id=v.id
                           LEFT JOIN sizes siz ON siz.id=v.size_id
                           LEFT JOIN colors col ON col.id=v.color_id
                           WHERE ci.cart_id=? and ci.is_removed = 0");
    $stmt->execute([$cartId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Session already started in session.php - no need to start again
// Fetch cart items
$items = getGuestCartItems($pdo);
if (!$items) {
    $_SESSION['checkout_error'] = "Your cart is empty.";
    header("Location: cart.php");
    exit;
}
// Get customer info from form (or session if logged in)
$name   = trim($_POST['name'] ?? ($_SESSION['user_name'] ?? ''));
$email  = trim($_POST['email'] ?? ($_SESSION['user_email'] ?? ''));
$country_code = trim($_POST['country_code'] ?? '+971');
$phone  = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city    = trim($_POST['city'] ?? 'doha');
$country = trim($_POST['country'] ?? 'Qatar');

// Format phone with country code
$full_phone = $country_code . $phone;

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 0.00;
$tax = 0.00;
$total = $subtotal + $shipping + $tax;

// Start transaction
$pdo->beginTransaction();

try {
    // 1️⃣ Validate stock for each variant
    foreach ($items as $item) {
        $variantId = $item['variant_id'];
        $qty = $item['quantity'];
        $item_name = htmlspecialchars($item['name']);

        $stmt = $pdo->prepare("SELECT stock FROM product_variants WHERE id=? FOR UPDATE");
        $stmt->execute([$variantId]);
        $variant = $stmt->fetch();

        if (!$variant) {
            throw new Exception("Variant not found for '{$item_name}'.");
        }

        $available = (int)$variant['stock'];
        if ($available < $qty) {
            throw new Exception("Sorry, only {$available} left for '{$item_name}'. Please adjust your cart.");
        }
    }

    // 2️⃣ Do NOT deduct stock yet - wait for payment confirmation

    // 3️⃣ Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            customer_id, session_id, full_name, email, phone, 
            shipping_address, city, country, 
            subtotal, tax, shipping_fee, total, 
            payment_method, payment_status, order_status, 
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
        )
    ");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        session_id(),
        $name,
        $email,
        $full_phone,
        $address,
        $city,
        $country,
        $subtotal,
        $tax,
        $shipping,
        $total,
        'MyFatoorah',
        'pending',
        'pending'
    ]);

    $orderId = $pdo->lastInsertId();
    // $orderId = '1234321';

    // Log order creation
    error_log("=== 📦 ORDER CREATED ===");
    error_log("Order ID: {$orderId}");
    error_log("Customer ID: " . ($_SESSION['user_id'] ?? 'NULL (Guest)'));
    error_log("Session ID stored: " . session_id());
    error_log("=== ===");

    // 4️⃣ Insert order items
    $invoiceItems = []; // Initialize array for MyFatoorah invoice items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, variant_id, product_name, color, size, quantity, price, total)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($items as $item) {
        $stmt = $pdo->prepare("
            SELECT c.name AS color, s.name AS size, v.product_id
            FROM product_variants v
            LEFT JOIN colors c ON v.color_id = c.id
            LEFT JOIN sizes s ON v.size_id = s.id
            WHERE v.id = ?
        ");
        $stmt->execute([$item['variant_id']]);
        $variant = $stmt->fetch();

        $color = $variant['color'] ?? '-';
        $size = $variant['size'] ?? '-';
        $product_id = $variant['product_id'] ?? '-';
        
        $invoiceItems[] = [
        'ItemName'   => $item['name'],
        'Quantity'   => $item['quantity'],
        'UnitPrice'  => $item['price']
        ];
        $stmtItem->execute([
            $orderId,
            $product_id,
            $item['variant_id'],
            $item['name'],
            $color,
            $size,
            $item['quantity'],
            $item['price'],
            $item['price'] * $item['quantity']
        ]);
    }

    // 5️⃣ DON'T clear cart yet - only clear after successful payment
    // clearCart($pdo); // Moved to callback.php after payment confirmation

    // 6️⃣ Commit transaction
    $pdo->commit();

    // exit;


require_once __DIR__ . '/MyFatoora/config.php';
// // require_once 'MyFatoorahLibrary2.php';
// /* For simplicity check our PHP SDK library here https://myfatoorah.readme.io/php-library */

// //PHP Notice:  To enable MyFatoorah auto-update, kindly give the write/read permissions to the library folder
// //use zip file
require_once __DIR__ . '/MyFatoora/MyfatoorahLoader.php';
require_once __DIR__ . '/MyFatoora/MyfatoorahLibrary2.php';

try {
    $paymentMethodId = 0; //to be redirect to MyFatoorah invoice page

    // Determine callback URL based on current domain (dynamic for any environment)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
        || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];
    $baseCallbackUrl = $protocol . $domain;

    // Format phone for MyFatoorah - extract numeric country code and phone
    $countryCodeNumeric = preg_replace('/[^0-9]/', '', $country_code); // Extract digits from +971
    $phoneNumeric = preg_replace('/[^0-9]/', '', $phone); // Extract digits from phone

    // MyFatoorah requires CustomerMobile to be max 11 characters (phone number only, without country code)
    // The country code is passed separately in MobileCountryCode field
    $phoneNumeric = substr($phoneNumeric, 0, 11); // Limit to 11 characters

    $postFields      = [
        'InvoiceValue' => $total,
        // 'InvoiceValue' => 1,
        "CurrencyIso"  => "QAR",
        "DisplayCurrencyIso"    => "QAR",
        "MobileCountryCode" => $countryCodeNumeric,
        "NotificationOption" =>  "ALL",
        "InvoiceItems" => $invoiceItems,
        "CustomerReference"  => $orderId,
        'CustomerName' => $name,
        'CustomerMobile' => $phoneNumeric,
        'CustomerEmail' => $email,
        'CallBackUrl'  => $baseCallbackUrl . '/MyFatoora/callback.php',
        'ErrorUrl'     => $baseCallbackUrl . '/MyFatoora/callback.php',
    ];

    // Use API key from config (already loaded via env)
    $countryCode = 'QAT';
    // $isTestMode is already set in config.php

    // Debug: Log payment request details
    error_log("=== MyFatoorah Payment Request ===");
    error_log("Order ID: " . $orderId);
    error_log("Total Amount: " . $total);
    error_log("Customer Name: " . $name);
    error_log("Customer Email: " . $email);
    error_log("Customer Phone (original): " . $full_phone);
    error_log("Mobile Country Code: " . $countryCodeNumeric);
    error_log("Customer Mobile (phone only): " . $phoneNumeric);
    error_log("Invoice Items Count: " . count($invoiceItems));
    error_log("Callback URL: " . $baseCallbackUrl . '/MyFatoora/callback.php');
    error_log("Test Mode: " . ($isTestMode ? 'true' : 'false'));

    try {
        $mfPayment = new PaymentMyfatoorahApiV2($apiKey, $countryCode, $isTestMode);
        $data      = $mfPayment->sendPayment($postFields, $paymentMethodId);
        // print_r($data);
        $invoiceId   = $data['invoiceId'];
        $paymentLink = $data['invoiceURL'];

        header("Location: $paymentLink ");
        exit;
        // echo "Click on <a href='$paymentLink' target='_blank'>$paymentLink</a> to pay with invoiceID $invoiceId.";
    } catch (Exception $ex) {
        // Transaction already committed - can't rollback
        // Just log the error and redirect
        error_log("MyFatoorah payment error: " . $ex->getMessage());
        $_SESSION['checkout_error'] = "Payment gateway error: " . $ex->getMessage();
        header("Location: " . BASE_URL . "checkout.php");
        exit;
    }
} catch (Exception $ex) {
    // General error in payment setup
    error_log("Checkout setup error: " . $ex->getMessage());
    $_SESSION['checkout_error'] = "Checkout error: " . $ex->getMessage();
    header("Location: " . BASE_URL . "checkout.php");
    exit;
}

//     $_SESSION['checkout_success'] = "✅ Your order #{$orderId} has been placed successfully!";
//     ob_end_clean(); // clear any buffered output

//   header("Location: " . BASE_URL . "order_success.php?id={$orderId}");
//     exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // echo "<pre style='color:red'>";
    // echo "Checkout Error:\n" . $e->getMessage() . "\n\n";
    // echo "File: " . $e->getFile() . "\n";
    // echo "Line: " . $e->getLine() . "\n";
    // echo "</pre>";
    // exit;
    //$pdo->rollBack();
    error_log("Checkout error: " . $e->getMessage());
    $_SESSION['checkout_error'] = $e->getMessage();
    header("Location: cart.php");
    exit;
}
?>
