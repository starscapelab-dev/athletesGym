<?php
session_start();

// If user is logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// If user is not logged in, redirect to login page
header('Location: login.php');
exit;
