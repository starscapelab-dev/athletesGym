<?php
require_once "../includes/session.php";
require_auth(); // block non-logged-in users
require_once "../admin/includes/db.php";

$id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$gender = $_POST['gender'] ?? '';
$dob = $_POST['dob'] ?? '';
$country = trim($_POST['country'] ?? '');
$city = trim($_POST['city'] ?? '');
$address = trim($_POST['address'] ?? '');

if (!$name || !$phone || !$gender || !$country || !$city || !$address) {
    header("Location: profile.php?msg=" . urlencode("All fields required."));
    exit;
}

$stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, gender=?, dob=?, country=?, city=?, address=? WHERE id=?");
$stmt->execute([$name, $phone, $gender, $dob, $country, $city, $address, $id]);

header("Location: profile.php?msg=" . urlencode("Profile updated successfully!"));
exit;
?>
