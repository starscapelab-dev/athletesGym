<?php

require_once "../admin/includes/db.php";
require_once "../includes/session.php";
require_once "../includes/csrf.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// SECURITY: Validate CSRF token
requireCsrfToken();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) exit('All fields required.');

$stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // SECURITY: Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    // After successful login
    $userId = $user['id'];
    $sessionId = session_id();

    // Merge any guest cart to the user's cart
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id=? LIMIT 1");
    $stmt->execute([$sessionId]);
    $guestCartId = $stmt->fetchColumn();

    if ($guestCartId) {
        // Check if user already has a cart
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id=? LIMIT 1");
        $stmt->execute([$userId]);
        $userCartId = $stmt->fetchColumn();

        if (!$userCartId) {
            $pdo->prepare("UPDATE carts SET user_id=? WHERE id=?")->execute([$userId, $guestCartId]);
            $userCartId = $guestCartId;
        } else {
            // Merge cart items
            $merge = $pdo->prepare("UPDATE cart_items SET cart_id=?, user_id=? WHERE cart_id=?");
            $merge->execute([$userCartId, $userId, $guestCartId]);

            // Delete old guest cart
            $pdo->prepare("DELETE FROM carts WHERE id=?")->execute([$guestCartId]);
        }
    }


    header("Location: ../shop.php");
    exit;
} else {
    // ❌ Failure: store error in session & redirect back
    $_SESSION['login_error'] = "Invalid username or password.";
    $_SESSION['old_username'] = $email; // keep input for form
    header("Location: login.php");
    exit;
}
}
// exit('Invalid email or password.');
?>
