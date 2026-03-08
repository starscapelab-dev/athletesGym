<?php
ob_start();

// Start session for admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php'; // Connection file
require_once __DIR__ . '/../layouts/config.php'; // ensure BASE_URL is loaded
require_once __DIR__ . '/../includes/csrf.php'; // CSRF protection

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    ob_end_clean(); // clear any previous output
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SECURITY: Validate CSRF token
    requireCsrfToken();

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // SECURITY: Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $noSessionCheck = true;
        header("Location: " . BASE_URL . "admin/dashboard.php");
        exit();
    } else {
        $error = "Invalid login!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Athletes Gym</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>auth/auth.css">
    <style>
        @font-face {
            font-family: "Orbitron-VariableFont_wght";
            src: url("<?= BASE_URL ?>assets/fonts/Optician/optician-sans.regular.ttf");
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Nunito-VariableFont_wght";
            src: url("<?= BASE_URL ?>assets/fonts/Optician/optician-sans.regular.ttf");
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: "Nunito-VariableFont_wght", -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: "Orbitron-VariableFont_wght", -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
        }

        .admin-badge {
            background: linear-gradient(135deg, #21335b 0%, #1a2847 100%);
            color: #fff;
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
            color: #8a93ae;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div style="text-align: center;">
                <span class="admin-badge">🔐 Admin Access</span>
            </div>

            <h2>Athletes Gym</h2>

            <?php if (!empty($error)): ?>
                <div class="auth-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <?php csrfField(); ?>
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required autofocus>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit" class="btn btn-primary">
                    Login to Dashboard
                </button>
            </form>

            <div class="auth-footer">
                Athletes Gym Qatar &copy; <?= date('Y') ?>
            </div>
        </div>
    </div>
</body>
</html>
