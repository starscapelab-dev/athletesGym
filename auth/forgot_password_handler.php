<?php
require_once "../includes/csrf.php";
require_once "../includes/session.php";
require_once "../admin/includes/db.php";
require_once "../includes/simple_email_service.php";

// SECURITY: Validate CSRF token
requireCsrfToken();

$email = trim($_POST['email'] ?? '');

if (!$email) {
    $_SESSION['forgot_error'] = "Please enter your email address.";
    header("Location: forgot_password.php");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['forgot_error'] = "No account found with that email.";
    header("Location: forgot_password.php");
    exit;
}

// Generate 6-digit OTP
$otp = rand(100000, 999999);
$expiresAt = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Store OTP in DB
$stmt = $pdo->prepare("UPDATE users SET reset_otp=?, reset_expires=? WHERE email=?");
$stmt->execute([$otp, $expiresAt, $email]);

// Send OTP email using SimpleEmailService
try {
    $emailService = new SimpleEmailService();
    $emailService->sendOTP($email, $otp, $user['name'] ?? 'User');
} catch (Exception $e) {
    error_log("Failed to send OTP email: " . $e->getMessage());
    $_SESSION['forgot_error'] = "Failed to send OTP email. Please try again.";
    header("Location: forgot_password.php");
    exit;
}

$_SESSION['reset_email'] = $email;
$_SESSION['forgot_success'] = "OTP sent successfully to your email!";
header("Location: verify_otp.php");
exit;
