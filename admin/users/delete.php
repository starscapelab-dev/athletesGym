<?php
session_start();
require_once __DIR__ . "/../includes/db.php";

$userId = $_GET['id'] ?? 0;

if (!$userId) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: list.php");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: list.php");
    exit;
}

// Check if user has any orders
$orderCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ?");
$orderCheckStmt->execute([$userId]);
$orderCount = $orderCheckStmt->fetchColumn();

if ($orderCount > 0) {
    $_SESSION['error'] = "Cannot delete user '{$user['name']}' because they have {$orderCount} order(s). Please contact support if you need to remove this user.";
    header("Location: list.php");
    exit;
}

// Delete user
try {
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$userId]);

    $_SESSION['success'] = "User '{$user['name']}' ({$user['email']}) has been deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
}

header("Location: list.php");
exit;
