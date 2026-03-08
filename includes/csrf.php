<?php
/**
 * CSRF Protection Functions
 * Generates and validates CSRF tokens for form submissions
 */

/**
 * Generate CSRF token and store in session
 * @return string The generated token
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Get the current CSRF token (generates if doesn't exist)
 * @return string The CSRF token
 */
function getCsrfToken() {
    return generateCsrfToken();
}

/**
 * Output a hidden CSRF token input field for forms
 */
function csrfField() {
    $token = getCsrfToken();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Validate CSRF token from POST request
 * @return bool True if valid, false otherwise
 */
function validateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Only validate on POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }

    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (empty($token) || empty($sessionToken)) {
        error_log("CSRF validation failed: Token is empty - POST token: '" . $token . "', SESSION token: '" . substr($sessionToken, 0, 10) . "...'");
        return false;
    }

    $isValid = hash_equals($sessionToken, $token);
    if (!$isValid) {
        error_log("CSRF validation failed: Tokens don't match - POST: " . substr($token, 0, 10) . "... SESSION: " . substr($sessionToken, 0, 10) . "...");
    }

    return $isValid;
}

/**
 * Validate CSRF token or die with error
 * Call this at the start of any form processing script
 */
function requireCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!validateCsrfToken()) {
        // Log detailed diagnostic information
        error_log("CSRF validation failed");
        error_log("Expected token: " . ($_SESSION['csrf_token'] ?? 'none'));
        error_log("Received token: " . ($_POST['csrf_token'] ?? 'none'));
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Request URI: " . $_SERVER['REQUEST_URI']);

        http_response_code(403);
        die('CSRF token validation failed. Please refresh the page and try again.');
    }
}

/**
 * Regenerate CSRF token (call after successful form submission)
 */
function regenerateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
