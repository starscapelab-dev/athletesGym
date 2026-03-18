# Hostinger Deployment Guide - Fix Database Connection

## Problem
You're seeing: `Database connection failed: SQLSTATE[HY000] [1045] Access denied for user ''@'localhost' (using password: NO)`

This means the `.env.production` file is either missing or not readable on the Hostinger server.

## Solution

### Step 1: Upload .env.production to Hostinger

1. **Via FTP/File Manager:**
   - Connect to your Hostinger server via FTP or use Hostinger's File Manager
   - Navigate to your website root directory (usually `public_html`)
   - Upload the `.env.production` file from your local project

2. **Rename it on the server:**
   - After upload, the file should be at: `/home/u675015292/public_html/.env.production`
   - Make sure the filename is exactly `.env.production` (with the dot at the start)

### Step 2: Set Correct File Permissions

```bash
# Via SSH or Hostinger Terminal
cd /home/u675015292/public_html/
chmod 600 .env.production
```

**Important:** File permissions should be `600` (read/write for owner only) for security.

### Step 3: Verify Environment Detection

The system auto-detects the environment based on domain:
- **athletesgym.qa** → Loads `.env.production`
- **athletesgym.haziex.com** → Loads `.env.test`
- **localhost** → Loads `.env`

Check that your domain is configured correctly in `includes/env_loader.php`:

```php
if (strpos($host, 'athletesgym.qa') !== false) {
    return __DIR__ . '/../.env.production';
}
```

### Step 4: Debug (If Still Not Working)

Create a temporary debug file to check what's happening:

**Create:** `debug_env.php` in your web root:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Environment Debug</h2>";
echo "<strong>Domain:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Check which .env file should be loaded
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (strpos($host, 'athletesgym.haziex.com') !== false) {
    $envFile = '.env.test';
} elseif (strpos($host, 'athletesgym.qa') !== false) {
    $envFile = '.env.production';
} else {
    $envFile = '.env';
}

echo "<strong>Expected .env file:</strong> {$envFile}<br>";
echo "<strong>Full path:</strong> " . __DIR__ . "/{$envFile}<br>";
echo "<strong>File exists:</strong> " . (file_exists(__DIR__ . "/{$envFile}") ? 'YES' : 'NO') . "<br>";
echo "<strong>File readable:</strong> " . (is_readable(__DIR__ . "/{$envFile}") ? 'YES' : 'NO') . "<br>";

if (file_exists(__DIR__ . "/{$envFile}")) {
    echo "<strong>File permissions:</strong> " . substr(sprintf('%o', fileperms(__DIR__ . "/{$envFile}")), -4) . "<br>";
    echo "<strong>File size:</strong> " . filesize(__DIR__ . "/{$envFile}") . " bytes<br>";
}

// Try to load and check env vars
require_once __DIR__ . '/includes/env_loader.php';

echo "<h3>Environment Variables:</h3>";
echo "<strong>DB_HOST:</strong> " . (env('DB_HOST') ?: 'NOT SET') . "<br>";
echo "<strong>DB_NAME:</strong> " . (env('DB_NAME') ?: 'NOT SET') . "<br>";
echo "<strong>DB_USER:</strong> " . (env('DB_USER') ?: 'NOT SET') . "<br>";
echo "<strong>DB_PASS:</strong> " . (env('DB_PASS') ? '***SET***' : 'NOT SET') . "<br>";
echo "<strong>APP_ENV:</strong> " . (env('APP_ENV') ?: 'NOT SET') . "<br>";

echo "<h3>All Loaded Env Variables:</h3>";
echo "<pre>";
print_r($_ENV);
echo "</pre>";
?>
```

**Access:** `https://athletesgym.qa/debug_env.php`

**DELETE THIS FILE AFTER DEBUGGING** for security!

### Step 5: Alternative Quick Fix

If you need a quick fix, you can directly edit the database credentials in `/admin/includes/db.php`:

```php
// TEMPORARY HARDCODED VALUES - NOT RECOMMENDED FOR PRODUCTION
$pdo = new PDO(
    'mysql:host=localhost;dbname=u675015292_athletesgym;charset=utf8mb4',
    'u675015292_gymuser',
    '7/W2NijR',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);
```

**WARNING:** This is NOT recommended because it:
- Exposes credentials in code
- Gets overwritten on git pull
- Is less secure than using .env files

## Recommended Solution

**Upload `.env.production` to Hostinger and set correct permissions.**

## Files to Upload to Hostinger

Make sure these files are on the server:
- `.env.production` (with database credentials)
- `includes/env_loader.php`
- `admin/includes/db.php`

## Current Database Credentials (from .env.production)

```
DB_HOST=localhost
DB_NAME=u675015292_athletesgym
DB_USER=u675015292_gymuser
DB_PASS=7/W2NijR
```

## Contact

If issue persists, check:
1. Database user exists in Hostinger phpMyAdmin
2. Database name is correct
3. Password is correct (no extra spaces)
4. User has permissions to access the database
