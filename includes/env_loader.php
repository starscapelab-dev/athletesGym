<?php
/**
 * Simple .env file loader
 * Loads environment variables from .env file into $_ENV and getenv()
 */

function loadEnv($path = __DIR__ . '/../.env') {
    if (!file_exists($path)) {
        die("Error: .env file not found at: $path");
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
