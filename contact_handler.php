<?php
require_once "includes/session.php";
require_once "admin/includes/db.php";
require_once "includes/email_service.php";
require_once "includes/csrf.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.php");
    exit;
}

// Validate CSRF token
requireCsrfToken();

// Get form data
$name = trim($_POST['fullname'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate inputs
if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['contact_error'] = "Please fill in all required fields.";
    header("Location: contact.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = "Please enter a valid email address.";
    header("Location: contact.php");
    exit;
}

// Store in database (optional - create contact_submissions table)
try {
    $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $phone, $message]);
} catch (PDOException $e) {
    // Table might not exist - that's okay, we'll still send email
    error_log("Could not store contact submission: " . $e->getMessage());
}

// Send email notification
try {
    $emailService = new EmailService();
    $emailService->sendContactForm([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message
    ]);

    $_SESSION['contact_success'] = "Thank you for contacting us! We'll get back to you soon.";
} catch (Exception $e) {
    error_log("Failed to send contact form email: " . $e->getMessage());
    $_SESSION['contact_error'] = "Failed to send your message. Please try again or email us directly.";
}

header("Location: contact.php");
exit;
