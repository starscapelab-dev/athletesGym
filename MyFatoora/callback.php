<?php
// Error reporting is now handled in db.php via environment configuration
header('Content-Type: application/json');
ob_start();

// Use proper session handling from session.php
require_once __DIR__ . "/../includes/session.php";
require_once 'config.php';
require_once __DIR__ . "/../admin/includes/db.php";
require_once __DIR__ . "/../layouts/config.php";
require_once __DIR__ . "/../includes/simple_email_service.php";

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
        $update = $pdo->prepare("UPDATE orders SET payment_status = 'Paid', order_status = 'confirmed', transaction_id = ? WHERE id = ?");
        $update->execute([$keyId, $data['CustomerReference']]);

        $orderId = $data['CustomerReference'];
        
        // Log order verification info
        $verifyStmt = $pdo->prepare("SELECT id, customer_id, session_id FROM orders WHERE id = ?");
        $verifyStmt->execute([$orderId]);
        $verifiedOrder = $verifyStmt->fetch();
        error_log("=== 📋 ORDER VERIFIED AFTER PAYMENT ===");
        error_log("Order ID: {$orderId}");
        error_log("Customer ID: " . ($verifiedOrder['customer_id'] ?? 'NULL (Guest)'));
        error_log("Stored Session ID: " . ($verifiedOrder['session_id'] ?? 'NULL'));
        error_log("Current Session ID: " . session_id());
        error_log("User ID in Session: " . ($_SESSION['user_id'] ?? 'NULL (Guest)'));
        error_log("=== ===");

        // ✅ DEDUCT STOCK ONLY AFTER PAYMENT IS CONFIRMED
        try {
            $stockStmt = $pdo->prepare("
                SELECT oi.variant_id, oi.quantity
                FROM order_items oi
                WHERE oi.order_id = ?
            ");
            $stockStmt->execute([$orderId]);
            $orderItems = $stockStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($orderItems as $item) {
                $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE id = ?")
                    ->execute([$item['quantity'], $item['variant_id']]);
                error_log("✅ Stock deducted: Variant {$item['variant_id']}, Qty: {$item['quantity']}");
            }
        } catch (Exception $e) {
            error_log("❌ Stock deduction error: " . $e->getMessage());
        }

        // Send order confirmation email
        try {
            $orderId = $data['CustomerReference'];

            error_log("=== 📧 ORDER EMAIL PROCESS STARTED ===");
            error_log("Order ID: {$orderId}");
            error_log("APP_ENV: " . env('APP_ENV'));
            error_log("MAIL_FROM: " . env('MAIL_FROM_ADDRESS'));
            error_log("=== ===");

            // Fetch order details
            $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch();

            // Fetch order items with images
            $itemsStmt = $pdo->prepare("
                SELECT 
                    oi.product_name, 
                    oi.quantity, 
                    oi.price,
                    GROUP_CONCAT(pi.image_path) as images
                FROM order_items oi
                LEFT JOIN product_images pi ON oi.product_id = pi.product_id
                WHERE oi.order_id = ?
                GROUP BY oi.id
            ");
            $itemsStmt->execute([$orderId]);
            $itemsResult = $itemsStmt->fetchAll();
            
            // Transform items to include images
            $items = array_map(function($item) {
                return [
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'images' => $item['images'] ? explode(',', $item['images']) : []
                ];
            }, $itemsResult);

            if ($order && $order['email']) {
                error_log("Sending email to: {$order['email']}");
                
                $emailService = new SimpleEmailService();
                
                $customerEmailSent = $emailService->sendOrderConfirmation([
                    'order_id' => $orderId,
                    'customer_name' => $order['full_name'],
                    'customer_email' => $order['email'],
                    'customer_phone' => $order['phone'] ?? '',
                    'shipping_address' => $order['address'] ?? '',
                    'order_date' => $order['created_at'],
                    'payment_method' => 'MyFatoorah',
                    'order_status' => 'Processing',
                    'total' => $order['total'],
                    'items' => array_map(function($item) {
                        return [
                            'name' => $item['product_name'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'images' => $item['images'] ?? []
                        ];
                    }, $items)
                ]);
                
                if ($customerEmailSent) {
                    error_log("✅ Customer email sent successfully to: {$order['email']}");
                } else {
                    error_log("❌ Customer email FAILED to send to: {$order['email']}");
                }
                
                // Also notify admin
                $adminEmailSent = $emailService->sendOrderNotificationToAdmin([
                    'order_id' => $orderId,
                    'customer_name' => $order['full_name'],
                    'customer_email' => $order['email'],
                    'customer_phone' => $order['phone'] ?? '',
                    'shipping_address' => $order['address'] ?? '',
                    'order_date' => $order['created_at'],
                    'total' => $order['total'],
                    'subtotal' => $order['subtotal'] ?? 0,
                    'tax' => $order['tax'] ?? 0,
                    'shipping_fee' => $order['shipping_fee'] ?? 0,
                    'discount' => $order['discount'] ?? 0,
                    'payment_method' => 'MyFatoorah',
                    'order_status' => 'Processing',
                    'items' => array_map(function($item) {
                        return [
                            'name' => $item['product_name'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'images' => $item['images'] ?? []
                        ];
                    }, $items)
                ]);
                
                if ($adminEmailSent) {
                    error_log("✅ Admin email sent successfully");
                } else {
                    error_log("❌ Admin email FAILED to send");
                    // Log failed notification to database as backup
                    try {
                        $adminEmail = env('MAIL_ADMIN_ADDRESS', 'athletesgymqa@gmail.com');
                        $failureLog = $pdo->prepare("
                            INSERT INTO email_logs (order_id, recipient, subject, status, created_at)
                            VALUES (?, ?, ?, 'failed', NOW())
                        ");
                        $failureLog->execute([
                            $orderId,
                            $adminEmail,
                            "New Order #{$orderId} - Failed Admin Notification"
                        ]);
                        error_log("⚠️ Failed email logged to database for manual review");
                    } catch (Exception $logErr) {
                        error_log("❌ Could not log failed email: " . $logErr->getMessage());
                    }
                }
            } else {
                error_log("❌ Order not found or missing email - Order: " . print_r($order, true));
            }
        } catch (Exception $e) {
            error_log("❌ EXCEPTION during order email: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
