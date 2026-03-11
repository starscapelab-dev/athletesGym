<?php
// Detect protocol (HTTP or HTTPS)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Get host
$domain = $_SERVER['HTTP_HOST'];

// Detect project folder (useful for localhost setups like http://localhost:8080/athletesgym/)
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Normalize path (strip /admin, /auth, /MyFatoora folders for top-level base)
$path = preg_replace('#/(admin|auth|category|account|MyFatoora)(/.*)?$#', '', $path);

// Define final BASE_URL
define('BASE_URL', $protocol . $domain . $path . '/');
?>
