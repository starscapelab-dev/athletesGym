<?php
// Get the raw POST data
$webhookData = file_get_contents("php://input");

// Decode JSON data
$data = json_decode($webhookData, true);

// Log the webhook data (optional for debugging)
file_put_contents("webhook.log", print_r($data, true), FILE_APPEND);

// Process the webhook
if ($data['Event'] == 'PaymentSuccess') {
    // Example: Update order status in your database
    $invoiceId = $data['InvoiceId'];
    $paymentId = $data['PaymentId'];
    // Perform necessary actions
    echo "Payment was successful for Invoice ID: $invoiceId";
} elseif ($data['Event'] == 'PaymentFailed') {
    // Handle failed payment
    echo "Payment failed for Invoice ID: {$data['InvoiceId']}";
}
?>
