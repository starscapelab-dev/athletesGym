<?php
session_start();

// Increment counter
if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 1;
} else {
    $_SESSION['counter']++;
}

// Store first session ID
if (!isset($_SESSION['first_session_id'])) {
    $_SESSION['first_session_id'] = session_id();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #21335b; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #21335b; color: white; }
        .test { margin: 20px 0; padding: 15px; background: #fff; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>🔍 Session Persistence Test</h1>

    <div class="info">
        <strong>Instructions:</strong> Refresh this page (F5) multiple times.
        The counter should increase and session ID should stay the same.
    </div>

    <h2>Session Status</h2>
    <table>
        <tr>
            <th>Property</th>
            <th>Value</th>
            <th>Status</th>
        </tr>
        <tr>
            <td><strong>Current Session ID</strong></td>
            <td><code><?= session_id() ?></code></td>
            <td class="<?= session_id() === $_SESSION['first_session_id'] ? 'success' : 'error' ?>">
                <?= session_id() === $_SESSION['first_session_id'] ? '✓ SAME (Good)' : '✗ CHANGED (Problem!)' ?>
            </td>
        </tr>
        <tr>
            <td><strong>First Session ID</strong></td>
            <td><code><?= $_SESSION['first_session_id'] ?></code></td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>Page View Counter</strong></td>
            <td><strong style="font-size: 24px;"><?= $_SESSION['counter'] ?></strong></td>
            <td class="<?= $_SESSION['counter'] > 1 ? 'success' : 'info' ?>">
                <?= $_SESSION['counter'] > 1 ? '✓ Persisting' : 'First visit' ?>
            </td>
        </tr>
        <tr>
            <td><strong>Session Save Path</strong></td>
            <td><code><?= session_save_path() ?: 'Default (C:\xampp\tmp)' ?></code></td>
            <td><?= is_writable(session_save_path() ?: 'C:\xampp\tmp') ? '✓ Writable' : '✗ Not writable' ?></td>
        </tr>
        <tr>
            <td><strong>Session Cookie Name</strong></td>
            <td><code><?= session_name() ?></code></td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>Session Cookie Set</strong></td>
            <td><?= isset($_COOKIE[session_name()]) ? '✓ Yes' : '✗ No' ?></td>
            <td class="<?= isset($_COOKIE[session_name()]) ? 'success' : 'error' ?>">
                <?= isset($_COOKIE[session_name()]) ? '✓ Cookie exists' : '✗ Cookie missing!' ?>
            </td>
        </tr>
    </table>

    <h2>Diagnosis</h2>
    <div class="test">
        <?php if (session_id() !== $_SESSION['first_session_id']): ?>
            <p class="error">⚠ <strong>SESSION ID IS CHANGING!</strong></p>
            <p>This means sessions are not persisting between page loads.</p>

            <h3>Possible Causes:</h3>
            <ol>
                <li><strong>Session save path not writable</strong> - Check C:\xampp\tmp permissions</li>
                <li><strong>Cookies disabled</strong> - Enable cookies in your browser</li>
                <li><strong>Multiple session_start() calls</strong> - Check your code</li>
                <li><strong>session.save_path not configured</strong> - Check php.ini</li>
            </ol>

            <h3>Quick Fixes:</h3>
            <ol>
                <li>Check if <code>C:\xampp\tmp</code> folder exists</li>
                <li>Ensure it's writable (not read-only)</li>
                <li>Clear browser cookies and try again</li>
                <li>Check php.ini: <code>session.save_path = "C:/xampp/tmp"</code></li>
            </ol>

        <?php else: ?>
            <p class="success">✓ <strong>SESSION IS PERSISTING CORRECTLY!</strong></p>
            <p>Your sessions are working properly. The cart issue is something else.</p>

            <h3>Since sessions work, check:</h3>
            <ol>
                <li>Are you logging in successfully?</li>
                <li>Are items being added to cart?</li>
                <li>Check database: <code>SELECT * FROM cart_items</code></li>
                <li>Check cart status: <code>SELECT * FROM carts WHERE status='active'</code></li>
            </ol>
        <?php endif; ?>
    </div>

    <h2>Full Session Data</h2>
    <pre style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd;"><?php print_r($_SESSION); ?></pre>

    <h2>Actions</h2>
    <p>
        <a href="test_session.php" style="padding: 10px 20px; background: #21335b; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">↻ Refresh Page</a>
        <a href="test_session.php?clear=1" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px;">Clear Session</a>
    </p>

    <?php if (isset($_GET['clear'])): ?>
        <?php session_destroy(); ?>
        <script>window.location.href = 'test_session.php';</script>
    <?php endif; ?>

    <hr>

    <h2>PHP Session Configuration</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>session.save_handler</td>
            <td><?= ini_get('session.save_handler') ?></td>
        </tr>
        <tr>
            <td>session.save_path</td>
            <td><?= ini_get('session.save_path') ?: 'Not set' ?></td>
        </tr>
        <tr>
            <td>session.gc_maxlifetime</td>
            <td><?= ini_get('session.gc_maxlifetime') ?> seconds (<?= round(ini_get('session.gc_maxlifetime') / 60) ?> minutes)</td>
        </tr>
        <tr>
            <td>session.cookie_lifetime</td>
            <td><?= ini_get('session.cookie_lifetime') ?> seconds (<?= ini_get('session.cookie_lifetime') == 0 ? 'Until browser closes' : round(ini_get('session.cookie_lifetime') / 60) . ' minutes' ?>)</td>
        </tr>
        <tr>
            <td>session.use_cookies</td>
            <td><?= ini_get('session.use_cookies') ? '✓ Enabled' : '✗ Disabled' ?></td>
        </tr>
    </table>

</body>
</html>
