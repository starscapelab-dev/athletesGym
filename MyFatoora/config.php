<?php
// Load environment variables
require_once __DIR__ . '/../includes/env_loader.php';

$apiKey = env('MYFATOORAH_API_KEY');
$baseUrl = env('MYFATOORAH_BASE_URL');
$isTestMode = env('MYFATOORAH_TEST_MODE', true);
?>