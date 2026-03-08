<?php
// Load environment variables FIRST (needed by security.php)
require_once __DIR__ . '/env_loader.php';

if (session_status() === PHP_SESSION_NONE) {
    // Secure session configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');

    // Enable secure cookies if on HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }

    session_start();
}

// Ensure config (for BASE_URL)
require_once __DIR__ . '/../layouts/config.php';

// Include CSRF protection
require_once __DIR__ . '/csrf.php';

// Include security functions (HTTPS enforcement, security headers)
require_once __DIR__ . '/security.php';

/**
 * Require authentication OR guest session
 */
function require_auth() {
    // Pages that do NOT require authentication
    $publicPages = ['login.php', 'register.php', 'forgot_password.php'];

    $current = basename($_SERVER['SCRIPT_NAME']);
    if (in_array($current, $publicPages)) {
        return; // public, no redirect
    }

    // ✅ If neither user nor guest exists → redirect to login
    if (empty($_SESSION['user_id']) && empty($_SESSION['guest'])) {
        // Create guest automatically
        $_SESSION['guest'] = [
            'id' => session_id(),
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
}
