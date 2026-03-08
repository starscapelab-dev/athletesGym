<?php
$pageTitle = "Change Password";
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/includes/header.php"; // Header includes _session.php for admin auth

$success = '';
$error = '';

// Process password change form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    require_once __DIR__ . '/../includes/csrf.php';
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All fields are required.';
        } elseif (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } else {
            // Get current admin from database
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if (!$admin) {
                $error = 'Admin account not found.';
            } elseif (!password_verify($current_password, $admin['password'])) {
                $error = 'Current password is incorrect.';
            } else {
                // Update password
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");

                if ($stmt->execute([$new_password_hash, $_SESSION['admin_id']])) {
                    $success = 'Password changed successfully!';
                    // Clear form fields on success
                    $_POST = [];
                } else {
                    $error = 'Failed to update password. Please try again.';
                }
            }
        }
    }
}

// Generate CSRF token for form
require_once __DIR__ . '/../includes/csrf.php';
$csrf_token = generateCsrfToken();
?>

<div class="change-password-container">
    <div class="page-header">
        <h1><i class="fas fa-lock"></i> Change Password</h1>
        <p class="subtitle">Update your admin account password</p>
    </div>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="password-form">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="form-group">
                <label for="current_password">
                    <i class="fas fa-key"></i> Current Password
                </label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="form-control"
                    placeholder="Enter your current password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="form-group">
                <label for="new_password">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    class="form-control"
                    placeholder="Enter your new password (min 6 characters)"
                    required
                    minlength="6"
                    autocomplete="new-password"
                >
                <small class="form-hint">Password must be at least 6 characters long</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-check-double"></i> Confirm New Password
                </label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Re-enter your new password"
                    required
                    minlength="6"
                    autocomplete="new-password"
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Change Password
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="security-tips">
        <h3><i class="fas fa-shield-alt"></i> Password Security Tips</h3>
        <ul>
            <li>Use a strong password with a mix of letters, numbers, and symbols</li>
            <li>Don't reuse passwords from other websites</li>
            <li>Change your password regularly</li>
            <li>Never share your password with anyone</li>
        </ul>
    </div>
</div>

<style>
.change-password-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 30px 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 2rem;
    color: #21335b;
    margin-bottom: 10px;
}

.page-header h1 i {
    margin-right: 10px;
    color: #21335b;
}

.subtitle {
    color: #6c757d;
    font-size: 1rem;
}

.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 35px;
    margin-bottom: 30px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.95rem;
}

.alert i {
    font-size: 1.2rem;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.password-form .form-group {
    margin-bottom: 25px;
}

.password-form label {
    display: block;
    font-weight: 600;
    color: #21335b;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.password-form label i {
    margin-right: 6px;
    color: #6c757d;
}

.password-form .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.password-form .form-control:focus {
    outline: none;
    border-color: #21335b;
    box-shadow: 0 0 0 3px rgba(33, 51, 91, 0.1);
}

.form-hint {
    display: block;
    margin-top: 6px;
    font-size: 0.85rem;
    color: #6c757d;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.btn-primary {
    background: #21335b;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background: #1a2847;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(33, 51, 91, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.security-tips {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    border-left: 4px solid #21335b;
}

.security-tips h3 {
    font-size: 1.1rem;
    color: #21335b;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.security-tips ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.security-tips li {
    padding: 8px 0;
    color: #495057;
    font-size: 0.9rem;
    position: relative;
    padding-left: 25px;
}

.security-tips li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #28a745;
    font-weight: bold;
}

@media (max-width: 768px) {
    .change-password-container {
        padding: 20px 15px;
    }

    .form-card {
        padding: 25px 20px;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>

<?php require_once "includes/footer.php"; ?>
