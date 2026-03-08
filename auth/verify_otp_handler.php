<?php
require_once "../includes/csrf.php";
require_once "../includes/session.php";
require_once "../admin/includes/db.php";

// SECURITY: Validate CSRF token
requireCsrfToken();

$email = $_SESSION['reset_email'] ?? '';
$otp = trim($_POST['otp'] ?? '');

if (!$email || !$otp) {
    $_SESSION['otp_error'] = "Invalid request. Please try again.";
    header("Location: forgot_password.php");
    exit;
}

// Validate OTP format (must be 6 digits)
if (!preg_match('/^[0-9]{6}$/', $otp)) {
    $_SESSION['otp_error'] = "Invalid OTP format. Please enter 6 digits.";
    header("Location: verify_otp.php");
    exit;
}

$stmt = $pdo->prepare("SELECT reset_otp, reset_expires FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// SECURITY: Use strict comparison for OTP validation
if (!$user || $user['reset_otp'] !== $otp) {
    $_SESSION['otp_error'] = "Invalid OTP. Please check and try again.";
    header("Location: verify_otp.php");
    exit;
}

// Check if OTP has expired
if (strtotime($user['reset_expires']) < time()) {
    $_SESSION['otp_error'] = "OTP has expired. Please request a new one.";
    // Clean up expired OTP
    $stmt = $pdo->prepare("UPDATE users SET reset_otp=NULL, reset_expires=NULL WHERE email=?");
    $stmt->execute([$email]);
    header("Location: forgot_password.php");
    exit;
}

// OTP verified successfully - don't clear it yet, keep until password is actually reset
$_SESSION['otp_verified'] = true;
$_SESSION['otp_success'] = "OTP verified! Please set your new password.";
header("Location: reset_password.php");
exit;
