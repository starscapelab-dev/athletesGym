<?php
require_once __DIR__ . "/includes/session.php";
require_once __DIR__ . "/includes/csrf.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Form Submission Test</h2>";

    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    echo "<h3>Session Token:</h3>";
    echo "<pre>";
    echo $_SESSION['csrf_token'] ?? 'NOT SET';
    echo "</pre>";

    echo "<h3>Validation Result:</h3>";
    if (validateCsrfToken()) {
        echo "<p style='color: green; font-weight: bold;'>✓ CSRF TOKEN VALID</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>✗ CSRF TOKEN INVALID</p>";
    }

    echo "<hr>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CSRF Token Test</title>
    <style>
        body { font-family: Arial; padding: 20px; max-width: 800px; margin: 0 auto; }
        .info { background: #f0f0f0; padding: 15px; margin: 10px 0; border-left: 4px solid #21335b; }
        form { background: #fff; padding: 20px; border: 1px solid #ddd; margin: 20px 0; }
        input[type="text"] { width: 100%; padding: 8px; margin: 5px 0; }
        button { background: #21335b; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        button:hover { background: #1a2847; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔒 CSRF Token Test</h1>

    <div class="info">
        <strong>Purpose:</strong> Test if CSRF tokens are being generated and validated correctly.
    </div>

    <h2>Session Information</h2>
    <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        <tr>
            <td><strong>Session ID</strong></td>
            <td><code><?= session_id() ?></code></td>
        </tr>
        <tr>
            <td><strong>CSRF Token in Session</strong></td>
            <td><code><?= isset($_SESSION['csrf_token']) ? substr($_SESSION['csrf_token'], 0, 20) . '...' : 'NOT SET' ?></code></td>
        </tr>
        <tr>
            <td><strong>Session Status</strong></td>
            <td><?= session_status() === PHP_SESSION_ACTIVE ? '✓ Active' : '✗ Not Active' ?></td>
        </tr>
    </table>

    <h2>Test Form</h2>
    <form method="POST" action="test_csrf.php">
        <?php csrfField(); ?>

        <label for="test_field">Test Field:</label>
        <input type="text" id="test_field" name="test_field" value="Test Value" required>

        <p><button type="submit">Submit Test Form</button></p>
    </form>

    <h2>Generated CSRF Field (view source):</h2>
    <div style="background: #f5f5f5; padding: 10px; overflow-x: auto;">
        <code>
        <?php
        ob_start();
        csrfField();
        $field = ob_get_clean();
        echo htmlspecialchars($field);
        ?>
        </code>
    </div>

    <h2>Instructions:</h2>
    <ol>
        <li>Submit the form above</li>
        <li>Check if CSRF token is valid</li>
        <li>If valid: ✓ CSRF protection is working</li>
        <li>If invalid: There's a problem with token generation or validation</li>
    </ol>

    <p><a href="checkout.php">→ Go to Checkout Page</a></p>

</body>
</html>
