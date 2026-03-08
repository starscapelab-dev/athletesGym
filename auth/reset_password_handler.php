<?php
require_once "../includes/csrf.php";
require_once "../includes/session.php";
require_once "../admin/includes/db.php";

// SECURITY: Validate CSRF token
requireCsrfToken();

$email = $_SESSION['reset_email'] ?? '';
$newPassword = trim($_POST['new_password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

// Verify authorization
if (!$email || !($_SESSION['otp_verified'] ?? false)) {
    $_SESSION['reset_error'] = "Unauthorized request. Please start from the beginning.";
    header("Location: forgot_password.php");
    exit;
}

// Validate passwords are provided
if (empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['reset_error'] = "Please fill in all fields.";
    header("Location: reset_password.php");
    exit;
}

// Validate passwords match
if ($newPassword !== $confirmPassword) {
    $_SESSION['reset_error'] = "Passwords do not match. Please try again.";
    header("Location: reset_password.php");
    exit;
}

// SECURITY: Enforce minimum password length (match registration requirement)
if (strlen($newPassword) < 8) {
    $_SESSION['reset_error'] = "Password must be at least 8 characters long.";
    header("Location: reset_password.php");
    exit;
}

// Optional: Check password strength
if (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
    $_SESSION['reset_error'] = "Password must contain both letters and numbers.";
    header("Location: reset_password.php");
    exit;
}

try {
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password and clear OTP
    $stmt = $pdo->prepare("UPDATE users SET password=?, reset_otp=NULL, reset_expires=NULL WHERE email=?");
    $stmt->execute([$hashedPassword, $email]);

    // Verify update was successful
    if ($stmt->rowCount() === 0) {
        throw new Exception("Failed to update password. User not found.");
    }

    // Clean up session variables
    unset($_SESSION['reset_email'], $_SESSION['otp_verified']);

    // Set success message
    $_SESSION['login_success'] = "Password updated successfully! Please log in with your new password.";

    // Redirect to login page
    header("Location: login.php");
    exit;

} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['reset_error'] = "An error occurred while updating your password. Please try again.";
    header("Location: reset_password.php");
    exit;
}
