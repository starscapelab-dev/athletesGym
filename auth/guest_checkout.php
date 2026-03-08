<?php
require_once "../includes/session.php";
require_once "../includes/csrf.php";

// SECURITY: Validate CSRF token
requireCsrfToken();

session_regenerate_id(true);
$_SESSION['guest'] = true;
header("Location: ../checkout.php");
exit;
?>
