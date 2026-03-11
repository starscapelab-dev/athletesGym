<?php
// Error reporting is now handled in db.php via environment configuration
header('Content-Type: application/json');
ob_start();
session_start();

require_once 'config.php';
require_once __DIR__ . "/../admin/includes/db.php";
require_once __DIR__ . "/../layouts/config.php";
require_once __DIR__ . "/../includes/email_service.php";

// // require_once 'MyFatoorahLibrary2.php';
// /* For simplicity check our PHP SDK library here https://myfatoorah.readme.io/php-library */

// //PHP Notice:  To enable MyFatoorah auto-update, kindly give the write/read permissions to the library folder
// //use zip file
include 'MyfatoorahLoader.php';
include 'MyfatoorahLibrary2.php';
// print_r($data);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);

    // Use API key from config (already loaded via env)
    $countryCode = 'QAT';
    // $isTestMode is already set in config.php

    $keyId   = $_GET['paymentId'];
    $KeyType = 'paymentId';
    
    try {
        $mfPayment = new PaymentMyfatoorahApiV2($apiKey, $countryCode, $isTestMode);
        $data      = $mfPayment->getPaymentStatus($keyId, $KeyType);
    
        print_r($data);
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
    $data = json_decode(json_encode($data), true);
    echo $data['InvoiceStatus'];
    if ($data['InvoiceStatus'] === 'Paid') {

        $mobile = $data['CustomerMobile'];
        // $mobile = '923154526941';
        $name = $data['CustomerName'].' - Ticket ID : '.$data['CustomerReference'];

        $check = $pdo->prepare("select * from orders where id = ? ");
        $check->execute([$data['CustomerReference']]);
        $resultcheck = $check->fetch();
        // $no_of_tickets = $resultcheck->no_of_tickets;
        // $ticket_days = $resultcheck->ticket_days;
        // $ticket_day = $resultcheck->ticket_day;
        // $ticket_type = $resultcheck->ticket_type;
        // echo $ticket_days;
        // if($ticket_days=='3 days')
        // {
        //     $date = '8-9-10 Dec';
        // }   else {
        //     if($ticket_day=='Day 1'){
        //         $date = '8 Dec';
        //     }else if($ticket_day=='Day 2'){
        //         $date = '9 Dec';
        //     }else if($ticket_day=='Day 3'){
        //         $date = '10 Dec';
        //     }
        // }
        // echo $ticket_days;

        // if($resultcheck->ticket_type == 'Regular')
        // {
        // $type_ticket = 'regualarticket';
        // }  else{
        // $type_ticket = 'viptickets';
        // }
        // SECURITY FIX: Use prepared statement for transaction_id to prevent SQL injection
        $update = $pdo->prepare("UPDATE orders SET payment_status = 'Paid', transaction_id = ? WHERE id = ?");
        $update->execute([$keyId, $data['CustomerReference']]);

        // Send order confirmation email
        try {
            $orderId = $data['CustomerReference'];

            // Fetch order details
            $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch();

            // Fetch order items
            $itemsStmt = $pdo->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll();

            if ($order && $order['email']) {
                $emailService = new EmailService();
                $emailService->sendOrderConfirmation($order['email'], [
                    'order_id' => $orderId,
                    'customer_name' => $order['full_name'],
                    'total' => number_format($order['total'], 2),
                    'items' => array_map(function($item) {
                        return [
                            'name' => $item['product_name'],
                            'quantity' => $item['quantity'],
                            'price' => number_format($item['price'], 2)
                        ];
                    }, $items)
                ]);
            }
        } catch (Exception $e) {
            error_log("Failed to send order confirmation email: " . $e->getMessage());
            // Don't fail the payment process if email fails
        }

        // Clear cart after successful payment
        require_once __DIR__ . "/../includes/cart_functions.php";
        try {
            // Get the cart associated with this order
            if ($order['customer_id']) {
                // Logged-in user
                $cartStmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1");
                $cartStmt->execute([$order['customer_id']]);
            } else {
                // Guest user
                $cartStmt = $pdo->prepare("SELECT id FROM carts WHERE session_id = ? AND status = 'active' LIMIT 1");
                $cartStmt->execute([$order['session_id']]);
            }
            $cartId = $cartStmt->fetchColumn();

            if ($cartId) {
                // Mark cart as checked out
                $pdo->prepare("UPDATE carts SET status='checked_out', updated_at=NOW() WHERE id=?")->execute([$cartId]);
            }
        } catch (Exception $e) {
            error_log("Failed to clear cart after payment: " . $e->getMessage());
            // Don't fail payment process if cart clearing fails
        }

        // $curl = curl_init();

        // curl_setopt_array($curl, [
        // CURLOPT_URL => "https://live-mt-server.wati.io/348071/api/v1/sendTemplateMessage?whatsappNumber=$mobile",
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => "",
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 30,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => "POST",
        // CURLOPT_POSTFIELDS => "{\"template_name\":\"$type_ticket\",\"broadcast_name\":\"$type_ticket\",\"parameters\":[{\"name\":\"name\",\"value\":\"$name\"},{\"name\":\"phone\",\"value\":\"$ticket_type\"},{\"name\":\"tenant_id\",\"value\":\"$date\"},{\"name\":\"dashboard_url\",\"value\":\"$no_of_tickets\"}]}",
        // CURLOPT_HTTPHEADER => [
        //     "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiI0Mzg4NzE1ZC1iMTY3LTQ0MDktYjI1Zi03MDljZjMyMDM3NGIiLCJ1bmlxdWVfbmFtZSI6InNlaWZAemFqZWwucWEiLCJuYW1laWQiOiJzZWlmQHphamVsLnFhIiwiZW1haWwiOiJzZWlmQHphamVsLnFhIiwiYXV0aF90aW1lIjoiMTEvMjQvMjAyNCAwNzozMjo0MCIsInRlbmFudF9pZCI6IjM0ODA3MSIsImRiX25hbWUiOiJtdC1wcm9kLVRlbmFudHMiLCJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvY2xhaW1zL3JvbGUiOiJBRE1JTklTVFJBVE9SIiwiZXhwIjoyNTM0MDIzMDA4MDAsImlzcyI6IkNsYXJlX0FJIiwiYXVkIjoiQ2xhcmVfQUkifQ.AhEpF4e_SMuSlmqN3c7wpBoP22hUpl-14_Dl9mrWd4k",
        //     "content-type: application/json-patch+json"
        // ],
        // ]);

        // $response = curl_exec($curl);
        // $err = curl_error($curl);

        // curl_close($curl);

        // if ($err) {
        // echo "cURL Error #:" . $err;
        // } else {
        // echo $response;
        // }

        // header("Location: ../ticket.php");
        $_SESSION['checkout_success'] =  "Payment successful for Invoice ID: " . $data['InvoiceId'];
        ob_end_clean(); // clear any buffered output

        header("Location: " . BASE_URL . "order_success.php?id={$data['CustomerReference']}");
        exit;
    } else {

        $_SESSION['checkout_success']=  "Payment failed or pending. Status: " . $data['InvoiceStatus'];
       ob_end_clean(); // clear any buffered output

        header("Location: " . BASE_URL . "order_success.php?id={$data['CustomerReference']}");
    }
} else {
    $_SESSION['checkout_success']=  "Payment failed or pending. Status: " . $data['InvoiceStatus'];
       ob_end_clean(); // clear any buffered output

        header("Location: " . BASE_URL . "order_success.php?id={$data['CustomerReference']}");
}
?>
