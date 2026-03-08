<?php
require_once 'config.php';

/* For simplicity check our PHP SDK library here https://myfatoorah.readme.io/php-library */

//PHP Notice:  To enable MyFatoorah auto-update, kindly give the write/read permissions to the library folder
//use zip file
include 'MyfatoorahLoader.php';
include 'MyfatoorahLibrary2.php';

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
//     'isTest' => true,
// ];

// /* --------------------------- InitiatePayment Endpoint --------------------- */
// $invoiceValue       = 50;
// $displayCurrencyIso = 'KWD';

// //------------- Post Fields -------------------------
// //Check https://docs.myfatoorah.com/docs/initiate-payment#request-model
// //------------- Call the Endpoint -------------------------
// try {
//     $mfObj          = new PaymentMyfatoorahApiV2($mfConfig);
//     $paymentMethods = $mfObj->getVendorGateways($invoiceValue, $displayCurrencyIso);
// } catch (Exception $ex) {
//     echo $ex->getMessage();
//     die;
// }


// //You can save $paymentMethods information in database to be used later
// $paymentMethodId = 2;
// //foreach ($paymentMethods as $pm) {
// //    if ($pm->PaymentMethodEn == 'VISA/MASTER') {
// //        $paymentMethodId = $pm->PaymentMethodId;
// //        break;
// //    }
// //}

// /* --------------------------- ExecutePayment Endpoint ---------------------- */

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
// $phone = '12312311';

// //------------- Post Fields -------------------------
// //Check https://docs.myfatoorah.com/docs/execute-payment#request-model
// $postFields = [
//     //Fill required data
//     'InvoiceValue'    => $invoiceValue,
//     'PaymentMethodId' => $paymentMethodId,
//         //Fill optional data
//         //'CustomerName'       => 'fname lname',
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

//------------- Call the Endpoint -------------------------
    $apiKey = 'gfOTCr56XFqO6g1ENMzvC4t_6mbjhJ_M0sKvowTkawPRq5qAWDj0E9pkcCm_M2p9AvmmyNDYOEvSFMgSS5bwVgtpQiEIzCJqAfK5Yz8jn2EkeVbXKdrfU-nEHsDtss3ZnrvSAerGPt_FXq6WggzdGsuo7zHr25pfRPbPwlJYPhNT940hS13NK4PiDLhEjQHYpWUqKMWlZuX8N461XqABRbdPv8tZbksRJPJjFQjISgJ53741eWj9njI2AKd5vDwAC8j3LuXLqvpy7c5hVk6yhKoXU2BsB3j-wQMIVQV78083LctQDfXtmBZs_kyge-SWwf7eWaklEvuY6w-xk2JAwEhEb0xdC8BuTT0uv4srR-0lTolAGwrg0LNoADSJai-DNYt0Opm_sqogBz-Olh5-Pt6_Q94K0I6odJc4av80wNzXFvdmL8MYKVqZJdfPTz27AEjWqrUbfbfagUberVPdPSfKvSpq_MAIy1450wcUezi56dq7lDg9_0HprlTA8hKQtG2miUU6UUdgVKic_AWfAEvWZ7b8Jq-NWsUI57Yq9k1ieJRtifY3ZsZUQQW_wkG6AKX3YY3lkWTR0TfB4sarvzl3I9XLy1gZ8-GGuXzGg_kNLLTFZs4aX9nIsDoxPtpbv_vNuShu5lL1HJiQ-cQordl0AyMAnXIbNosz3knzPDtIiopa';

    $countryCode = 'QAT';

    $isTestMode = true;

try {
    $paymentMethodId = 0;
    $postFields      = [
        'InvoiceValue' => '1',
        "CurrencyIso"  => "QAR",
        "CustomECI"    => "02",
        "CustomCAVV"   => "AABBCCDDEEFFGG123456",
        'CustomerName' => 'fname lname',
        'CallBackUrl'  => 'http://localhost:8080/Zajel-HadySafa-Website/MyFatoora//callback.php',
        'ErrorUrl'     => 'http://localhost:8080/Zajel-HadySafa-Website/MyFatoora/callback.php', //or 'https://example.com/error.php'
    ];

    $mfObj = new PaymentMyfatoorahApiV2($apiKey, $countryCode, $isTestMode);
    $data  = $mfObj->getInvoiceURL($postFields, $paymentMethodId);

    //You can save payment data in database as per your needs
        $invoiceId   = $data['invoiceId'];
        $paymentLink = $data['invoiceURL'];

    //Display the result to your customer
    //Redirect your customer to complete the payment process
    echo '<h3><u>Summary:</u></h3>';
    echo "To pay the invoice ID <b>$invoiceId</b>, click on:<br>";
    echo "<a href='$paymentLink' target='_blank'>$paymentLink</a><br><br>";

    echo '<h3><u>ExecutePayment Response Data:</u></h3><pre>';
    print_r($data);
    echo '</pre>';

    echo '<h3><u>InitiatePayment Response Data:</u></h3><pre>';
    print_r($paymentMethods);
    echo '</pre>';
    echo "Click on <a href='$paymentLink' target='_blank'>$paymentLink</a> to pay with invoiceID $invoiceId.";
} catch (Exception $ex) {
    echo $ex->getMessage();
    die;
}
