<?php
require_once 'config.php';
require_once 'MyFatoorahLibrary.php';

// Initialize MyFatoorah API
$myFatoorah = new MyFatoorahApiV2($apiKey, $baseUrl);

// Payment ID or Invoice ID
$keyType = "InvoiceId"; // Use "PaymentId" or "InvoiceId"
$keyValue = "123456789"; // Replace with the actual ID

try {
    $response = $myFatoorah->getPaymentStatus($keyType, $keyValue);
    echo "Payment Status: " . $response['InvoiceStatus'];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
