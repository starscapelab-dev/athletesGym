<?php
/**
 * Security Functions
 * HTTPS enforcement and other security utilities
 */

/**
 * Force HTTPS redirect in production
 */
function enforceHttps() {
    // Only enforce HTTPS in production
    if (env('APP_ENV') === 'production') {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                   || $_SERVER['SERVER_PORT'] == 443;

        if (!$isHttps) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect);
            exit;
        }
    }
}

/**
 * Set security headers
 */
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Content Security Policy (basic - adjust as needed)
    if (env('APP_ENV') === 'production') {
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https:; img-src 'self' data: https:;");
    }
}

// Apply security measures
enforceHttps();
setSecurityHeaders();
