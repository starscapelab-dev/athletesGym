<?php
/**
 * Smart .env file loader
 * Auto-detects environment and loads appropriate configuration
 * - athletesgym.haziex.com → .env.test
 * - athletesgym.qa → .env.production
 * - localhost → .env (default)
 */

function getEnvironmentFile() {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Map domains to .env files
    if (strpos($host, 'athletesgym.haziex.com') !== false) {
        return __DIR__ . '/../.env.test';
    } elseif (strpos($host, 'athletesgym.qa') !== false) {
        return __DIR__ . '/../.env.production';
    }
    
    // Default to local development
    return __DIR__ . '/../.env';
}

function loadEnv($path = null) {
    // If no path provided, auto-detect based on domain
    if ($path === null) {
        $path = getEnvironmentFile();
    }
    
    if (!file_exists($path)) {
        // Log the error but don't die on production
        $domain = $_SERVER['HTTP_HOST'] ?? 'unknown';
        $env = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        
        error_log("⚠️  WARNING: .env file not found at: {$path}");
        error_log("Domain: {$domain}");
        error_log("Expected environment file: " . basename($path));
        
        // On production, log but continue with defaults
        // This prevents 500 errors if .env is missing
        if (strpos($domain, 'localhost') === false && strpos($domain, '127.0.0.1') === false) {
            error_log("⚠️ On production domain but .env file missing - using fallback email configuration");
            // Set some basic fallback values
            $_ENV['MAIL_FROM_ADDRESS'] = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@' . $domain;
            $_ENV['MAIL_FROM_NAME'] = $_ENV['MAIL_FROM_NAME'] ?? 'Athletes Gym';
            return; // Don't die, continue with what we have
        }
        
        // On localhost, require the file
        die("Error: .env file not found at: $path (Domain: {$domain})");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove inline comments (anything after # that's not in quotes)
            if (strpos($value, '#') !== false && !preg_match('/^["\']/', $value)) {
                $value = trim(explode('#', $value)[0]);
            }

            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            // Set environment variable
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Get environment variable with optional default
 */
function env($key, $default = null) {
    // Prefer values loaded from the local .env file so
    // project-specific settings override any system/global env vars.
    if (array_key_exists($key, $_ENV)) {
        $value = $_ENV[$key];
    } else {
        $value = getenv($key);
        if ($value === false) {
            $value = $default;
        }
    }

    // Convert string booleans to actual booleans
    if (is_string($value)) {
        $lower = strtolower($value);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
    }

    return $value;
}

// Load the .env file automatically when this file is included
loadEnv();
