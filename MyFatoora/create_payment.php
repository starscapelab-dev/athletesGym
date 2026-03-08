<?php
require_once 'config.php';
// // require_once 'MyFatoorahLibrary2.php';
// /* For simplicity check our PHP SDK library here https://myfatoorah.readme.io/php-library */

// //PHP Notice:  To enable MyFatoorah auto-update, kindly give the write/read permissions to the library folder
// //use zip file
include 'MyfatoorahLoader.php';
include 'MyfatoorahLibrary2.php';

// // $myFatoorah = new MyFatoorahApiV2($apiKey, $baseUrl);

// //use composer
// //require 'vendor/autoload.php';
// //use MyFatoorah\Library\MyFatoorah;
// //use MyFatoorah\Library\API\Payment\MyFatoorahPayment;

// /* --------------------------- Configurations ------------------------------- */
// //Test
// $mfConfig = [
//     /**
//      * API Token Key (string)
//      * Accepted value:
//      * Live Token: https://myfatoorah.readme.io/docs/live-token
//      * Test Token: https://myfatoorah.readme.io/docs/test-token
//      */
//     'apiKey'      => 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL',
//     /*
//      * Vendor Country ISO Code (string)
//      * Accepted value: QAR, SAU, ARE, QAT, BHR, OMN, JOD, or EGY. Check https://docs.myfatoorah.com/docs/iso-lookups
//      */
//     'vcCode' => 'QAR',
//     /**
//      * Test Mode (boolean)
//      * Accepted value: true for the test mode or false for the live mode
//      */
//     'isTest'      => true,
// ];

// /* --------------------------- SendPayment Endpoint ------------------------- */
// $invoiceValue       = 50;
// $displayCurrencyIso = 'KWD';

// //Fill customer address array
// /* $customerAddress = array(
//   'Block'               => 'Blk #', //optional
//   'Street'              => 'Str', //optional
//   'HouseBuildingNo'     => 'Bldng #', //optional
//   'Address'             => 'Addr', //optional
//   'AddressInstructions' => 'More Address Instructions', //optional
//   ); */

// //Fill invoice item array
// /* $invoiceItems[] = [
//   'ItemName'  => 'Item Name', //ISBAN, or SKU
//   'Quantity'  => '2', //Item's quantity
//   'UnitPrice' => '25', //Price per item
//   ]; */

// //Fill suppliers array
// /* $suppliers = [
//   [
//   'SupplierCode'  => 1,
//   'InvoiceShare'  => '2',
//   'ProposedShare' => null,
//   ]
//   ]; */

// //Parse the phone string
// $phone = '923154526941';

// //------------- Post Fields -------------------------
// //Check https://docs.myfatoorah.com/docs/send-payment#request-model
// $postFields = [
//     //Fill required data
//     'InvoiceValue'       => $invoiceValue,
//     'CustomerName'       => 'fname lname',
//     'NotificationOption' => 'LNK', //'SMS', 'EML', or 'ALL'
//         //Fill optional data
//         //'DisplayCurrencyIso' => $displayCurrencyIso,
//         //'MobileCountryCode'  => $phone[0],
//         //'CustomerMobile'     => $phone[1],
//         //'CustomerEmail'      => 'email@example.com',
//         //'CallBackUrl'        => 'https://example.com/callback.php',
//         //'ErrorUrl'           => 'https://example.com/callback.php', //or 'https://example.com/error.php'
//         //'Language'           => 'en', //or 'ar'
//         //'CustomerReference'  => 'orderId',
//         //'CustomerCivilId'    => 'CivilId',
//         //'UserDefinedField'   => 'This could be string, number, or array',
//         //'ExpiryDate'         => '', //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
//         //'CustomerAddress'    => $customerAddress,
//         //'InvoiceItems'       => $invoiceItems,
//         //'Suppliers'          => $suppliers,
// ];

// //------------- Call the Endpoint -------------------------
// try {
//     $mfObj = new PaymentMyfatoorahApiV2($mfConfig);
//     $data  = $mfObj->sendPayment($postFields);

//     //You can save payment data in database as per your needs
//     $invoiceId   = $data->InvoiceId;
//     $paymentLink = $data->InvoiceURL;

//     //Display the result to your customer
//     //Redirect your customer to complete the payment process
//     echo '<h3><u>Summary:</u></h3>';
//     echo "To pay the invoice ID <b>$invoiceId</b>, click on:<br>";
//     echo "<a href='$paymentLink' target='_blank'>$paymentLink</a><br><br>";

//     echo '<h3><u>SendPayment Response Data:</u></h3><pre>';
//     print_r($data);
//     echo '</pre>';
// } catch (Exception $ex) {
//     echo $ex->getMessage();
//     die;


try {
    $paymentMethodId = 0; //to be redirect to MyFatoorah invoice page    
    $postFields      = [
        'InvoiceValue' => '1',
        "CurrencyIso"  => "QAR",
        "CustomECI"    => "02",
        "CustomCAVV"   => "AABBCCDDEEFFGG123456",
        'CustomerName' => 'fname lname',
        'CallBackUrl'  => 'http://localhost:8080/Zajel-HadySafa-Website/MyFatoora//callback.php',
        'ErrorUrl'     => 'http://localhost:8080/Zajel-HadySafa-Website/MyFatoora/callback.php', //or 'https://example.com/error.php'
    ];

    // $apiKey = 'OhZFb6M9ZU3OmJuh7fJrwwsOfdhu33CMLKVln5sx0Ni4tT72nY2IU_p1lHN-inCIhN5R08NOKmmjb7LOfaWjwQS2RDwCPjWHvMuT2S_bo9_Mb68Np0k4LtlzJwig0WahfyLQk7EFNrqv7_FrbvNN1MbYMWY7hwNOwbKs6qisoZaflVtRCzTX-mnY753lYl2gzbSUKo9sp9H4-MrXrpNQtGRny4Sp3fiGcrVe3irj5cmO8UV_pttiYv9TWW6YzNrLu9zb2aLVJsIFyOS1mQvoRT7hilU83w3S0Rox5SSzozdl7XjhHd3kDkk9oFGdWXa2E2dnOa4SYcWwjGDQxqGowe19t-DVfgOjRgfc31nBoDQFTF7Vq-jxB0OhmtYryzxR9TuhfWYL7CNtkEQ0ReXzsoGv8b_6sVl6V3uMP3gby1F_mewt6SmcPHedEvxCtk0-2_lhrFViogyGXVMZv_vrszUfO48boWrRVg8MZoUdyjf29sKceRVPVlCXjUm9eEIz9lSY9r9zHXUe7Cjwi8DTTS3GeGm1UD2GwWqj31ul4PRGAUwPf322K_hKguzv4dLqCCZLSy-QU_iI4TUZ3CFVHMksL6fDR3XUJ6tilevsX0zNoprJwduPgkdBLDp93G7RskR6UYm--r4GngURXYS2zGMO_qLPjT7bPl-YKtZE7FXqg2M-';
    $apiKey = 'gfOTCr56XFqO6g1ENMzvC4t_6mbjhJ_M0sKvowTkawPRq5qAWDj0E9pkcCm_M2p9AvmmyNDYOEvSFMgSS5bwVgtpQiEIzCJqAfK5Yz8jn2EkeVbXKdrfU-nEHsDtss3ZnrvSAerGPt_FXq6WggzdGsuo7zHr25pfRPbPwlJYPhNT940hS13NK4PiDLhEjQHYpWUqKMWlZuX8N461XqABRbdPv8tZbksRJPJjFQjISgJ53741eWj9njI2AKd5vDwAC8j3LuXLqvpy7c5hVk6yhKoXU2BsB3j-wQMIVQV78083LctQDfXtmBZs_kyge-SWwf7eWaklEvuY6w-xk2JAwEhEb0xdC8BuTT0uv4srR-0lTolAGwrg0LNoADSJai-DNYt0Opm_sqogBz-Olh5-Pt6_Q94K0I6odJc4av80wNzXFvdmL8MYKVqZJdfPTz27AEjWqrUbfbfagUberVPdPSfKvSpq_MAIy1450wcUezi56dq7lDg9_0HprlTA8hKQtG2miUU6UUdgVKic_AWfAEvWZ7b8Jq-NWsUI57Yq9k1ieJRtifY3ZsZUQQW_wkG6AKX3YY3lkWTR0TfB4sarvzl3I9XLy1gZ8-GGuXzGg_kNLLTFZs4aX9nIsDoxPtpbv_vNuShu5lL1HJiQ-cQordl0AyMAnXIbNosz3knzPDtIiopa';

    $countryCode = 'QAT';

    $isTestMode = true;

    try {
        $mfPayment = new PaymentMyfatoorahApiV2($apiKey, $countryCode, $isTestMode);
        $data      = $mfPayment->getInvoiceURL($postFields, $paymentMethodId);
        // print_r($data);
        $invoiceId   = $data['invoiceId'];
        $paymentLink = $data['invoiceURL'];
        
        header("Location: $paymentLink ");
        exit;
        // echo "Click on <a href='$paymentLink' target='_blank'>$paymentLink</a> to pay with invoiceID $invoiceId.";
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}

