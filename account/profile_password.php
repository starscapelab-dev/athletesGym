<?php
require_once "../includes/session.php";
require_auth();
require_once "../admin/includes/db.php";

$id = $_SESSION['user_id'];
$old = $_POST['old_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_new_password'] ?? '';

if (!$old || !$new || !$confirm) {
    header("Location: profile.php?page=password&msg=" . urlencode("All fields required."));
    exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user || !password_verify($old, $user['password'])) {
    header("Location: profile.php?page=password&msg=" . urlencode("Current password incorrect."));
    exit;
}
if ($new !== $confirm) {
    header("Location: profile.php?page=password&msg=" . urlencode("New passwords do not match."));
    exit;
}
if (strlen($new) < 6) {
    header("Location: profile.php?page=password&msg=" . urlencode("Password too short."));
    exit;
}
$new_hash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->execute([$new_hash, $id]);

header("Location: profile.php?page=password&msg=" . urlencode("Password changed successfully!"));
exit;
?>
