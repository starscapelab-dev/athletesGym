<?php
require_once "../admin/includes/db.php";
require_once "../includes/session.php";
require_once "../includes/csrf.php";

// SECURITY: Validate CSRF token
requireCsrfToken();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$phone = $_POST['phone'] ?? '';
$gender = $_POST['gender'] ?? '';
$dob = $_POST['dob'] ?? '';
$country = $_POST['country'] ?? '';
$city = $_POST['city'] ?? '';
$address = $_POST['address'] ?? '';

if (!$name || !$email || !$password) exit('All fields required.');

if ($password !== $confirm_password) exit('Passwords do not match.');

// SECURITY: Enforce minimum password length (server-side validation)
if (strlen($password) < 8) exit('Password must be at least 8 characters long.');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) exit('Invalid email.');

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, gender, dob, country, city, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
try {
    $stmt->execute([$name, $email, $hash, $phone, $gender, $dob, $country, $city, $address]);
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;
    
    // Check if redirect parameter is set
    $redirect = $_POST['redirect'] ?? '';
    $redirectUrl = match($redirect) {
        'checkout' => '../checkout.php',
        default => '../shop.php'
    };
    
    header("Location: " . $redirectUrl);
    exit;
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) exit('Email already registered.');
    exit('Registration error.');
}
?>
