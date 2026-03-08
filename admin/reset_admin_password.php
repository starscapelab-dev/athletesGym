<?php
/**
 * Admin Password Reset Script
 * SECURITY WARNING: Delete this file after use!
 *
 * Access: http://localhost/athletesGym/admin/reset_admin_password.php
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/../layouts/config.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? 'admin');
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($newPassword)) {
        $message = 'Please enter a new password';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Password must be at least 6 characters';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update admin password
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $result = $stmt->execute([$hashedPassword, $username]);

        if ($result) {
            $success = true;
            $message = "✅ Password updated successfully!<br><br>
                       <strong>Username:</strong> {$username}<br>
                       <strong>New Password:</strong> {$newPassword}<br><br>
                       <a href='login.php' style='color: #21335b; font-weight: bold;'>Go to Login Page</a><br><br>
                       <strong style='color: red;'>⚠️ DELETE THIS FILE NOW: admin/reset_admin_password.php</strong>";
        } else {
            $message = 'Failed to update password. Check if username exists.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Admin Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 100px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #21335b;
            margin-top: 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #856404;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #21335b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #1a2847;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Admin Password</h1>

        <div class="warning">
            <strong>⚠️ Security Warning:</strong><br>
            This is a password reset tool. Delete this file immediately after use!
        </div>

        <?php if ($message): ?>
            <div class="message <?= $success ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Admin Username:</label>
                <input type="text" name="username" value="admin" required>
            </div>

            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" required minlength="6"
                       placeholder="Enter new password (min 6 characters)">
            </div>

            <button type="submit">Reset Password</button>
        </form>

        <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 5px; font-size: 13px;">
            <strong>Tips:</strong><br>
            • Use a strong password with letters, numbers, and symbols<br>
            • Write down the new password somewhere safe<br>
            • Delete this file after resetting the password
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
