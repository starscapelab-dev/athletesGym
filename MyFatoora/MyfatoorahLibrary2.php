<?php

/**
 * Class MyfatoorahApiV2 is responsible for handling calling MyFatoorah API endpoints.
 * Also, It has necessary library functions that help in providing the correct parameters used endpoints.
 *
 * MyFatoorah offers a seamless business experience by offering a technology put together by our tech team. This enables smooth business operations involving sales activity, product invoicing, shipping, and payment processing. MyFatoorah invoicing and payment gateway solution trigger your business to greater success at all levels in the new age world of commerce. Leverage your sales and payments at all e-commerce platforms (ERPs, CRMs, CMSs) with transparent and slick applications that are well-integrated into social media and telecom services. For every closing sale click, you make a business function gets done for you, along with generating factual reports and statistics to fine-tune your business plan with no-barrier low-cost.
 * Our technology experts have designed the best GCC E-commerce solutions for the native financial instruments (Debit Cards, Credit Cards, etc.) supporting online sales and payments, for events, shopping, mall, and associated services.
 *
 * Created by MyFatoorah http://www.myfatoorah.com/
 * Developed By tech@myfatoorah.com
 * Date: 02/05/2023
 * Time: 12:00
 *
 * API Documentation on https://myfatoorah.readme.io/docs
 * Library Documentation and Download link on https://myfatoorah.readme.io/docs/php-library
 *
 * @author    MyFatoorah <tech@myfatoorah.com>
 * @copyright 2021 MyFatoorah, All rights reserved
 * @license   GNU General Public License v3.0
 */
class MyfatoorahApiV2 {
    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * The URL used to connect to MyFatoorah test/live API server
     *
     * @var string
     */
    protected $apiURL = 'https://api-qa.myfatoorah.com/';

    /**
     * The API Token Key is the authentication which identify a user that is using the app
     * To generate one follow instruction here https://myfatoorah.readme.io/docs/live-token
     *
     * @var string
     */
    protected $apiKey = 'gfOTCr56XFqO6g1ENMzvC4t_6mbjhJ_M0sKvowTkawPRq5qAWDj0E9pkcCm_M2p9AvmmyNDYOEvSFMgSS5bwVgtpQiEIzCJqAfK5Yz8jn2EkeVbXKdrfU-nEHsDtss3ZnrvSAerGPt_FXq6WggzdGsuo7zHr25pfRPbPwlJYPhNT940hS13NK4PiDLhEjQHYpWUqKMWlZuX8N461XqABRbdPv8tZbksRJPJjFQjISgJ53741eWj9njI2AKd5vDwAC8j3LuXLqvpy7c5hVk6yhKoXU2BsB3j-wQMIVQV78083LctQDfXtmBZs_kyge-SWwf7eWaklEvuY6w-xk2JAwEhEb0xdC8BuTT0uv4srR-0lTolAGwrg0LNoADSJai-DNYt0Opm_sqogBz-Olh5-Pt6_Q94K0I6odJc4av80wNzXFvdmL8MYKVqZJdfPTz27AEjWqrUbfbfagUberVPdPSfKvSpq_MAIy1450wcUezi56dq7lDg9_0HprlTA8hKQtG2miUU6UUdgVKic_AWfAEvWZ7b8Jq-NWsUI57Yq9k1ieJRtifY3ZsZUQQW_wkG6AKX3YY3lkWTR0TfB4sarvzl3I9XLy1gZ8-GGuXzGg_kNLLTFZs4aX9nIsDoxPtpbv_vNuShu5lL1HJiQ-cQordl0AyMAnXIbNosz3knzPDtIiopa';

    /**
     * This is the file name or the logger object
     * It is used in logging the payment/shipping events to help in debugging and monitor the process and connections.
     *
     * @var string|object
     */
    protected $loggerObj;

    /**
     * If $loggerObj is set as a logger object, you should set this var with the function name that will be used in the debugging.
     *
     * @var string
     */
    protected $loggerFunc;

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Constructor
     * Initiate new MyFatoorah API process
     *
     * @param string        $apiKey      The API Token Key is the authentication which identify a user that is using the app. To generate one follow instruction here https://myfatoorah.readme.io/docs/live-token.
     * @param string        $countryMode Select the country mode.
     * @param boolean       $isTest      Set it to false for live mode.
     * @param string|object $loggerObj   This is the file name or the logger object. It is used in logging the payment/shipping events to help in debugging and monitor the process and connections. Leave it null, if you don't want to log the events.
     * @param string        $loggerFunc  If $loggerObj is set as a logger object, you should set this var with the function name that will be used in the debugging.
     */
    public function __construct($apiKey, $countryMode = 'QAR', $isTest = false, $loggerObj = null, $loggerFunc = null) {

        $mfCountries = $this->getMyFatoorahCountries();

        $code = strtoupper($countryMode);
        if (isset($mfCountries[$code])) {
            $this->apiURL = ($isTest) ? $mfCountries[$code]['testv2'] : $mfCountries[$code]['v2'];
        } else {
            $this->apiURL = ($isTest) ? 'https://apitest.myfatoorah.com' : 'https://api-qa.myfatoorah.com/';
            // $this->apiURL = 'https://apitest.myfatoorah.com';
        }

        $this->apiKey     = $apiKey ? trim($apiKey) : '';
        $this->loggerObj  = $loggerObj;
        $this->loggerFunc = $loggerFunc;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param string         $url        MyFatoorah API endpoint URL
     * @param array          $postFields POST request parameters array. It should be set to null if the request is GET.
     * @param integer|string $orderId    The order id or the payment id of the process, used for the events logging.
     * @param string         $function   The requester function name, used for the events logging.
     *
     * @return object       The response object as the result of a successful calling to the API.
     *
     * @throws Exception    Throw exception if there is any curl/validation error in the MyFatoorah API endpoint URL
     */
    public function callAPI($url, $postFields = null, $orderId = null, $function = null) {

        //to prevent json_encode adding lots of decimal digits
        ini_set('precision', 14);
        ini_set('serialize_precision', -1);

        $request = isset($postFields) ? 'POST' : 'GET';
        $fields  = json_encode($postFields);

        $msgLog = "Order #$orderId ----- $function";

        if ($function != 'Direct Payment') {
            $this->log("$msgLog - Request: $fields");
        }

        //***************************************
        //call url
        //***************************************
        $curl = curl_init($url);

        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST  => $request,
            CURLOPT_POSTFIELDS     => $fields,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer $this->apiKey", 'Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true
        ));

        $res = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        //example set a local ip to host apitest.myfatoorah.com
        if ($err) {
            $this->log("$msgLog - cURL Error: $err");
            throw new Exception($err);
        }

        $this->log("$msgLog - Response: $res");

        $json = json_decode((string) $res);

        //***************************************
        //check for errors
        //***************************************

        $error = $this->getAPIError($json, (string) $res);
        if ($error) {
            $this->log("$msgLog - Error: $error");
            throw new Exception($error);
        }

        //***************************************
        //Success
        //***************************************
        return $json;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Handles Endpoint Errors Function
     *
     * @param object|string $json
     * @param string        $res
     *
     * @return string
     */
    protected function getAPIError($json, $res) {

        if (isset($json->IsSuccess) && $json->IsSuccess == true) {
            return '';
        }

        //to avoid blocked IP like:
        //<html>
        //<head><title>403 Forbidden</title></head>
        //<body>
        //<center><h1>403 Forbidden</h1></center><hr><center>Microsoft-Azure-Application-Gateway/v2</center>
        //</body>
        //</html>
        //and, skip apple register <YourDomainName> tag error
        $stripHtmlStr = strip_tags($res);
        if ($res != $stripHtmlStr && stripos($stripHtmlStr, 'apple-developer-merchantid-domain-association') !== false) {
            return trim(preg_replace('/\s+/', ' ', $stripHtmlStr));
        }

        //Check for the errors
        $err = $this->getJsonErrors($json);
        if ($err) {
            return $err;
        }

        if (!$json) {
            return (!empty($res) ? $res : 'Kindly review your MyFatoorah admin configuration due to a wrong entry.');
        }

        if (is_string($json)) {
            return $json;
        }

        return '';
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Check for the json (response model) errors
     *
     * @param object|string $json
     *
     * @return string
     */
    protected function getJsonErrors($json) {

        if (isset($json->ValidationErrors) || isset($json->FieldsErrors)) {
            //$err = implode(', ', array_column($json->ValidationErrors, 'Error'));

            $errorsObj = isset($json->ValidationErrors) ? $json->ValidationErrors : $json->FieldsErrors;
            $blogDatas = array_column($errorsObj, 'Error', 'Name');

            return implode(', ', array_map(function ($k, $v) {
                        return "$k: $v";
                    }, array_keys($blogDatas), array_values($blogDatas)));
        }

        if (isset($json->Data->ErrorMessage)) {
            return $json->Data->ErrorMessage;
        }

        //if not, get the message.
        //sometimes Error value of ValidationErrors is null, so either get the "Name" key or get the "Message"
        //example {
        //"IsSuccess":false,
        //"Message":"Invalid data",
        //"ValidationErrors":[{"Name":"invoiceCreate.InvoiceItems","Error":""}],
        //"Data":null
        //}
        //example {
        //"Message":
        //"No HTTP resource was found that matches the request URI 'https://apitest.myfatoorah.com/v2/SendPayment222'.",
        //"MessageDetail":
        //"No route providing a controller name was found to match request URI 'https://apitest.myfatoorah.com/v2/SendPayment222'"
        //}
        if (isset($json->Message)) {
            return $json->Message;
        }

        return '';
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns the country code and the phone after applying MyFatoorah restriction
     *
     * Matching regular expression pattern: ^(?:(\+)|(00)|(\\*)|())[0-9]{3,14}((\\#)|())$
     * if (!preg_match('/^(?:(\+)|(00)|(\\*)|())[0-9]{3,14}((\\#)|())$/iD', $inputString))
     * String length: inclusive between 0 and 11
     *
     * @param string $inputString It is the input phone number provide by the end user.
     *
     * @return array        That contains the phone code in the 1st element the the phone number the the 2nd element.
     *
     * @throws Exception    Throw exception if the input length is less than 3 chars or long than 14 chars.
     */
    public static function getPhone($inputString) {

        //remove any arabic digit
        $newNumbers = range(0, 9);

        $persianDecimal = ['&#1776;', '&#1777;', '&#1778;', '&#1779;', '&#1780;', '&#1781;', '&#1782;', '&#1783;', '&#1784;', '&#1785;']; // 1. Persian HTML decimal
        $arabicDecimal  = ['&#1632;', '&#1633;', '&#1634;', '&#1635;', '&#1636;', '&#1637;', '&#1638;', '&#1639;', '&#1640;', '&#1641;']; // 2. Arabic HTML decimal
        $arabic         = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩']; // 3. Arabic Numeric
        $persian        = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹']; // 4. Persian Numeric

        $string0 = str_replace($persianDecimal, $newNumbers, $inputString);
        $string1 = str_replace($arabicDecimal, $newNumbers, $string0);
        $string2 = str_replace($arabic, $newNumbers, $string1);
        $string3 = str_replace($persian, $newNumbers, $string2);

        //Keep Only digits
        $string4 = preg_replace('/[^0-9]/', '', $string3);

        //remove 00 at start
        if (strpos($string4, '00') === 0) {
            $string4 = substr($string4, 2);
        }

        if (!$string4) {
            return ['', ''];
        }

        //check for the allowed length
        $len = strlen($string4);
        if ($len < 3 || $len > 14) {
            throw new Exception('Phone Number lenght must be between 3 to 14 digits');
        }

        //get the phone arr
        if (strlen(substr($string4, 3)) > 3) {
            return [
                substr($string4, 0, 3),
                substr($string4, 3)
            ];
        } else {
            return [
                '',
                $string4
            ];
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * It will log the payment/shipping process events
     *
     * @param string $msg It is the string message that will be written in the log file
     */
    public function log($msg) {

        if (!$this->loggerObj) {
            return;
        }
        if (is_string($this->loggerObj)) {
            error_log(PHP_EOL . date('d.m.Y h:i:s') . ' - ' . $msg, 3, $this->loggerObj);
        } elseif (method_exists($this->loggerObj, $this->loggerFunc)) {
            $this->loggerObj->{$this->loggerFunc}($msg);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get the rate that will convert the given weight unit to MyFatoorah default weight unit.
     * Weight must be in kg, g, lbs, or oz. Default is kg.
     *
     * @param string $unit It is the weight unit used.
     *
     * @return double|integer The conversion rate that will convert the given unit into the kg.
     *
     * @throws Exception Throw exception if the input unit is not support.
     */
    public static function getWeightRate($unit) {

        $lUnit = strtolower($unit);

        //kg is the default
        $rateUnits = [
            '1'         => ['kg', 'kgs', 'كج', 'كلغ', 'كيلو جرام', 'كيلو غرام'],
            '0.001'     => ['g', 'جرام', 'غرام', 'جم'],
            '0.453592'  => ['lbs', 'lb', 'رطل', 'باوند'],
            '0.0283495' => ['oz', 'اوقية', 'أوقية'],
        ];

        foreach ($rateUnits as $rate => $unitArr) {
            if (array_search($lUnit, $unitArr) !== false) {
                return (float) $rate;
            }
        }
        throw new Exception('Weight units must be in kg, g, lbs, or oz. Default is kg');
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get the rate that will convert the given dimension unit to MyFatoorah default dimension unit.
     * Dimension must be in cm, m, mm, in, or yd. Default is cm.
     *
     * @param string $unit It is the dimension unit used in width, hight, or depth.
     *
     * @return double|integer   The conversion rate that will convert the given unit into the cm.
     *
     * @throws Exception        Throw exception if the input unit is not support.
     */
    public static function getDimensionRate($unit) {

        $lUnit = strtolower($unit);

        //cm is the default
        $rateUnits = [
            '1'     => ['cm', 'سم'],
            '100'   => ['m', 'متر', 'م'],
            '0.1'   => ['mm', 'مم'],
            '2.54'  => ['in', 'انش', 'إنش', 'بوصه', 'بوصة'],
            '91.44' => ['yd', 'يارده', 'ياردة'],
        ];

        foreach ($rateUnits as $rate => $unitArr) {
            if (array_search($lUnit, $unitArr) !== false) {
                return (float) $rate;
            }
        }
        throw new Exception('Dimension units must be in cm, m, mm, in, or yd. Default is cm');
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Gets the rate of a given currency according to the default currency of the MyFatoorah portal account.
     *
     * @param string $currency The currency that will be converted into the currency of MyFatoorah portal account.
     *
     * @return number       The conversion rate converts a given currency to the MyFatoorah account default currency.
     *
     * @throws Exception    Throw exception if the input currency is not support by MyFatoorah portal account.
     */
    public function getCurrencyRate($currency) {

        $json = $this->getCurrencyRates();
        foreach ($json as $value) {
            if ($value->Text == $currency) {
                return $value->Value;
            }
        }
        throw new Exception('The selected currency is not supported by MyFatoorah');
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get list of MyFatoorah currency rates
     *
     * @return object
     */
    public function getCurrencyRates() {

        $url = "$this->apiURL/v2/GetCurrenciesExchangeList";
        return $this->callAPI($url, null, null, 'Get Currencies Exchange List');
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Calculate the amount value that will be paid in each gateway
     *
     * @param double|integer $totalAmount
     * @param string         $currency
     * @param string         $paymentCurrencyIso
     * @param object         $allRatesData
     *
     * @return array
     */
    protected function calcGatewayData($totalAmount, $currency, $paymentCurrencyIso, $allRatesData) {

        //if ($currency != $paymentCurrencyIso) {
        foreach ($allRatesData as $data) {
            if ($data->Text == $currency) {
                $baseCurrencyRate = $data->Value;
            }
            if ($data->Text == $paymentCurrencyIso) {
                $gatewayCurrencyRate = $data->Value;
            }
        }

        if (isset($baseCurrencyRate) && isset($gatewayCurrencyRate)) {
            $baseAmount = ceil(((int) ($totalAmount * 1000)) / $baseCurrencyRate / 10) / 100;

            $number = ceil(($baseAmount * $gatewayCurrencyRate * 100)) / 100;
            return [
                'GatewayTotalAmount' => number_format($number, 2, '.', ''),
                'GatewayCurrency'    => $paymentCurrencyIso
            ];
        } else {
            return [
                'GatewayTotalAmount' => $totalAmount,
                'GatewayCurrency'    => $currency
            ];
        }

        //        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Validate webhook signature function
     *
     * @param array  $dataArray webhook request array
     * @param string $secret    webhook secret key
     * @param string $signature MyFatoorah signature
     * @param int    $eventType MyFatoorah Event type Number (1, 2, 3 , 4)
     *
     * @return boolean
     */
    public static function isSignatureValid($dataArray, $secret, $signature, $eventType = 0) {

        if ($eventType == 2) {
            unset($dataArray['GatewayReference']);
        }

        uksort($dataArray, 'strcasecmp');

        // uksort($data, function ($a, $b) {
        //   $a = mb_strtolower($a);
        //   $b = mb_strtolower($b);
        //   return strcmp($a, $b);
        // });

        $output = implode(',', array_map(
                        function ($v, $k) {
                            return sprintf("%s=%s", $k, $v);
                        },
                        $dataArray,
                        array_keys($dataArray)
        ));

        //        $data      = utf8_encode($output);
        //        $keySecret = utf8_encode($secret);
        // generate hash of $field string
        $hash = base64_encode(hash_hmac('sha256', $output, $secret, true));

        if ($signature === $hash) {
            return true;
        } else {
            return false;
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get a list of MyFatoorah countries and their API URLs and names
     *
     * @return array of MyFatoorah data
     */
    public static function getMyFatoorahCountries() {

        $mfConfigFile = __DIR__ . '/mf-config.json';
        
        if (file_exists($mfConfigFile)) {
            if ((time() - filemtime($mfConfigFile) > 3600)) {
                self::updateMFConfigFile($mfConfigFile);
            }

            $content = file_get_contents($mfConfigFile);
            return ($content) ? json_decode($content, true) : [];
        }
        return [];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Update the mf-config.json file 
     * 
     * @param string $mfConfigFile
     *
     * @return void
     */
    protected static function updateMFConfigFile($mfConfigFile) {

        if (!is_writable($mfConfigFile)) {
            $mfError = 'To enable MyFatoorah auto-update, kindly give the write/read permissions to the library folder ' . __DIR__ . ' on your server and its files.';
            trigger_error($mfError, E_USER_WARNING);
            return;
        }

        touch($mfConfigFile);

        $mfCurl = curl_init('https://portal.myfatoorah.com/Files/API/mf-config.json');
        curl_setopt_array($mfCurl, array(
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true
        ));

        $mfResponse = curl_exec($mfCurl);
        $mfHttpCode = curl_getinfo($mfCurl, CURLINFO_HTTP_CODE);

        curl_close($mfCurl);

        if ($mfHttpCode == 200 && is_string($mfResponse)) {
            file_put_contents($mfConfigFile, $mfResponse);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
}


/**
 *  PaymentMyfatoorahApiV2 handle the payment process of MyFatoorah API endpoints
 *
 * @author    MyFatoorah <tech@myfatoorah.com>
 * @copyright 2021 MyFatoorah, All rights reserved
 * @license   GNU General Public License v3.0
 */
class PaymentMyfatoorahApiV2 extends MyfatoorahApiV2 {

    /**
     * To specify either the payment will be onsite or offsite
     * (default value: false)
     *
     * @var boolean
     */
    protected $isDirectPayment = false;

    /**
     *
     * @var string
     */
    public static $pmCachedFile = __DIR__ . '/mf-methods.json';

    /**
     *
     * @var array
     */
    protected static $paymentMethods;

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * List available Payment Gateways. (POST API)
     *
     * @param double|integer $invoiceValue
     * @param string         $displayCurrencyIso
     * @param boolean        $isCached
     *
     * @return array
     */
    public function getVendorGateways($invoiceValue = 0, $displayCurrencyIso = '', $isCached = false) {

        $postFields = [
            'InvoiceAmount' => $invoiceValue,
            'CurrencyIso'   => $displayCurrencyIso,
        ];

        $json = $this->callAPI("$this->apiURL/v2/InitiatePayment", $postFields, null, 'Initiate Payment');

        $paymentMethods = isset($json->Data->PaymentMethods) ? $json->Data->PaymentMethods : [];

        if (!empty($paymentMethods) && $isCached) {
            file_put_contents(self::$pmCachedFile, json_encode($paymentMethods));
        }
        return $paymentMethods;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * List available Cached Payment Gateways.
     *
     * @return array of Cached payment methods
     */
    public function getCachedVendorGateways() {

        if (file_exists(self::$pmCachedFile)) {
            $cache = file_get_contents(self::$pmCachedFile);
            return ($cache) ? json_decode($cache) : [];
        } else {
            return $this->getVendorGateways(0, '', true);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * List available Payment Gateways by type (direct, cards)
     *
     * @param boolean $isDirect
     *
     * @return array
     */
    public function getVendorGatewaysByType($isDirect = false) {

        $gateways = $this->getCachedVendorGateways();

        //        try {
        //            $gateways = $this->getVendorGateways();
        //        } catch (Exception $ex) {
        //            return [];
        //        }

        $paymentMethods = [
            'cards'  => [],
            'direct' => [],
        ];

        foreach ($gateways as $g) {
            if ($g->IsDirectPayment) {
                $paymentMethods['direct'][] = $g;
            } elseif ($g->PaymentMethodCode != 'ap') {
                $paymentMethods['cards'][] = $g;
            } elseif ($this->isAppleSystem()) {
                //add apple payment for IOS systems
                $paymentMethods['cards'][] = $g;
            }
        }

        return ($isDirect) ? $paymentMethods['direct'] : $paymentMethods['cards'];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * List available cached  Payment Methods
     *
     * @param  bool $isAppleRegistered
     * @return array
     */
    public function getCachedPaymentMethods($isAppleRegistered = false) {

        $gateways       = $this->getCachedVendorGateways();
        $paymentMethods = ['all' => [], 'cards' => [], 'form' => [], 'ap' => []];
        foreach ($gateways as $g) {
            $paymentMethods = $this->fillPaymentMethodsArray($g, $paymentMethods, $isAppleRegistered);
        }

        //add only one ap gateway
        $paymentMethods['ap'] = (isset($paymentMethods['ap'][0])) ? $paymentMethods['ap'][0] : [];

        return $paymentMethods;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * List available Payment Methods
     *
     * @param double|integer $invoiceValue
     * @param string         $displayCurrencyIso
     * @param bool           $isAppleRegistered
     *
     * @return array
     */
    public function getPaymentMethodsForDisplay($invoiceValue, $displayCurrencyIso, $isAppleRegistered = false) {

        if (!empty(self::$paymentMethods)) {
            return self::$paymentMethods;
        }

        $gateways = $this->getVendorGateways($invoiceValue, $displayCurrencyIso);
        $allRates = $this->getCurrencyRates();

        self::$paymentMethods = ['all' => [], 'cards' => [], 'form' => [], 'ap' => []];

        foreach ($gateways as $g) {
            $g->GatewayData = $this->calcGatewayData($g->TotalAmount, $g->CurrencyIso, $g->PaymentCurrencyIso, $allRates);

            self::$paymentMethods = $this->fillPaymentMethodsArray($g, self::$paymentMethods, $isAppleRegistered);
        }

        //add only one ap gateway
        self::$paymentMethods['ap'] = $this->getOneApplePayGateway(self::$paymentMethods['ap'], $displayCurrencyIso, $allRates);

        return self::$paymentMethods;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    protected function getOneApplePayGateway($apGateways, $displayCurrency, $allRates) {

        $displayCurrencyIndex = array_search($displayCurrency, array_column($apGateways, 'PaymentCurrencyIso'));
        if ($displayCurrencyIndex) {
            return $apGateways[$displayCurrencyIndex];
        }

        //get defult mf account currency
        $defCurKey       = array_search('1', array_column($allRates, 'Value'));
        $defaultCurrency = $allRates[$defCurKey]->Text;

        $defaultCurrencyIndex = array_search($defaultCurrency, array_column($apGateways, 'PaymentCurrencyIso'));
        if ($defaultCurrencyIndex) {
            return $apGateways[$defaultCurrencyIndex];
        }

        if (isset($apGateways[0])) {
            return $apGateways[0];
        }

        return [];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param object  $g
     * @param array   $paymentMethods
     * @param boolean $isAppleRegistered
     *
     * @return array
     */
    protected function fillPaymentMethodsArray($g, $paymentMethods, $isAppleRegistered = false) {

        if ($g->PaymentMethodCode != 'ap') {
            if ($g->IsEmbeddedSupported) {
                $paymentMethods['form'][] = $g;
                $paymentMethods['all'][]  = $g;
            } elseif (!$g->IsDirectPayment) {
                $paymentMethods['cards'][] = $g;
                $paymentMethods['all'][]   = $g;
            }
        } elseif ($this->isAppleSystem()) {
            if ($isAppleRegistered) {
                //add apple payment for IOS systems
                $paymentMethods['ap'][] = $g;
            } else {
                $paymentMethods['cards'][] = $g;
            }
            $paymentMethods['all'][] = $g;
        }
        return $paymentMethods;
    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Check if the system supports ApplePay or not
     *
     * @return boolean
     */
    protected static function isAppleSystem() {

        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if ((stripos($userAgent, 'iPod') || stripos($userAgent, 'iPhone') || stripos($userAgent, 'iPad') || stripos($userAgent, 'Mac')) && (self::getBrowserName($userAgent) == 'Safari')) {
            return true;
        }

        return false;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param string $userAgent
     *
     * @return string
     */
    public static function getBrowserName($userAgent) {

        $browsers = [
            'Opera'             => ['Opera', 'OPR/'],
            'Edge'              => ['Edge'],
            'Chrome'            => ['Chrome', 'CriOS'],
            'Firefox'           => ['Firefox', 'FxiOS'],
            'Safari'            => ['Safari'],
            'Internet Explorer' => ['MSIE', 'Trident/7'],
        ];

        foreach ($browsers as $browser => $bArr) {
            foreach ($bArr as $needle) {
                if (strpos($userAgent, $needle)) {
                    return $browser;
                }
            }
        }

        return 'Other';
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get Payment Method Object
     *
     * @param string         $gateway
     * @param string         $gatewayType        ['PaymentMethodId', 'PaymentMethodCode']
     * @param double|integer $invoiceValue
     * @param string         $displayCurrencyIso
     *
     * @return object
     *
     * @throws Exception
     */
    public function getPaymentMethod($gateway, $gatewayType = 'PaymentMethodId', $invoiceValue = 0, $displayCurrencyIso = '') {

        $paymentMethods = $this->getVendorGateways($invoiceValue, $displayCurrencyIso);

        $pm = null;
        foreach ($paymentMethods as $method) {
            if ($method->$gatewayType == $gateway) {
                $pm = $method;
                break;
            }
        }

        if (!isset($pm)) {
            throw new Exception('Please contact Account Manager to enable the used payment method in your account');
        }

        if ($this->isDirectPayment && !$pm->IsDirectPayment) {
            throw new Exception($pm->PaymentMethodEn . ' Direct Payment Method is not activated. Kindly contact your MyFatoorah account manager or sales representative to activate it.');
        }

        return $pm;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get the invoice/payment URL and the invoice id
     *
     * @param array          $curlData
     * @param int|string     $gatewayId (default value: 'myfatoorah')
     * @param integer|string $orderId   (default value: null) used in log file
     * @param string         $sessionId The payment session used in embedded payment.
     * @param string         $notificationOption could be EML, SMS, LNK, or ALL.
     *
     * @return array
     */
    public function getInvoiceURL($curlData, $gatewayId = 0, $orderId = null, $sessionId = null, $notificationOption = 'Lnk') {

        $this->log('------------------------------------------------------------');

        $this->isDirectPayment = false;

        if (!empty($sessionId)) {
            return $this->embeddedPayment($curlData, $sessionId, $orderId);
        } elseif ($gatewayId == 'myfatoorah' || empty($gatewayId)) {
            return $this->sendPayment($curlData, $orderId, $notificationOption);
        } else {
            return $this->excutePayment($curlData, $gatewayId, $orderId);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * (POST API)
     *
     * @param array          $curlData
     * @param integer|string $gatewayId
     * @param integer|string $orderId   (default value: null) used in log file
     *
     * @return array
     */
    public function executePayment($curlData, $gatewayId, $orderId = null) {

        $curlData['PaymentMethodId'] = $gatewayId;

        $json = $this->callAPI("$this->apiURL/v2/ExecutePayment", $curlData, $orderId, 'Excute Payment'); //__FUNCTION__

        return ['invoiceURL' => $json->Data->PaymentURL, 'invoiceId' => $json->Data->InvoiceId];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * (POST API)
     *
     * @param array          $curlData
     * @param integer|string $orderId            (default value: null) used in log file
     * @param string         $notificationOption could be EML, SMS, LNK, or ALL.
     *
     * @return array
     */
    public function sendPayment($curlData, $orderId = null, $notificationOption = 'Lnk') {

        $curlData['NotificationOption'] = $notificationOption;

        $json = $this->callAPI("$this->apiURL/v2/SendPayment", $curlData, $orderId, 'Send Payment');

        return ['invoiceURL' => $json->Data->InvoiceURL, 'invoiceId' => $json->Data->InvoiceId];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get the direct payment URL and the invoice id (POST API)
     *
     * @param array          $curlData
     * @param integer|string $gateway
     * @param array          $cardInfo
     * @param integer|string $orderId  (default value: null) used in log file
     *
     * @return array
     */
    public function directPayment($curlData, $gateway, $cardInfo, $orderId = null) {

        $this->log('------------------------------------------------------------');

        $this->isDirectPayment = true;

        $data = $this->excutePayment($curlData, $gateway, $orderId);

        $json = $this->callAPI($data['invoiceURL'], $cardInfo, $orderId, 'Direct Payment'); //__FUNCTION__
        return ['invoiceURL' => $json->Data->PaymentURL, 'invoiceId' => $data['invoiceId']];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get the Payment Transaction Status (POST API)
     *
     * @param string         $keyId
     * @param string         $KeyType
     * @param integer|string $orderId (default value: null)
     * @param string         $price
     * @param string         $currncy
     *
     * @return object
     *
     * @throws Exception
     */
    public function getPaymentStatus($keyId, $KeyType, $orderId = null, $price = null, $currncy = null) {

        //payment inquiry
        $curlData = ['Key' => $keyId, 'KeyType' => $KeyType];
        $json     = $this->callAPI("$this->apiURL/v2/GetPaymentStatus", $curlData, $orderId, 'Get Payment Status');

        $msgLog = 'Order #' . $json->Data->CustomerReference . ' ----- Get Payment Status';

        //check for the order information
        if (!$this->checkOrderInformation($json, $orderId, $price, $currncy)) {
            $err = 'Trying to call data of another order';
            $this->log("$msgLog - Exception is $err");
            throw new Exception($err);
        }


        //check invoice status (Paid and Not Paid Cases)
        if ($json->Data->InvoiceStatus == 'Paid' || $json->Data->InvoiceStatus == 'DuplicatePayment') {
            $json->Data = $this->getSuccessData($json);
            $this->log("$msgLog - Status is Paid");
        } elseif ($json->Data->InvoiceStatus != 'Paid') {
            $json->Data = $this->getErrorData($json, $keyId, $KeyType);
            $this->log("$msgLog - Status is " . $json->Data->InvoiceStatus . '. Error is ' . $json->Data->InvoiceError);
        }

        return $json->Data;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param object $json
     * @param string $orderId
     * @param string $price
     * @param string $currncy
     *
     * @return boolean
     */
    protected function checkOrderInformation($json, $orderId = null, $price = null, $currncy = null) {

        //check for the order ID
        if ($orderId && $json->Data->CustomerReference != $orderId) {
            return false;
        }

        //check for the order price and currency
        list($valStr, $mfCurrncy) = explode(' ', $json->Data->InvoiceDisplayValue);
        $mfPrice = floatval(preg_replace('/[^\d.]/', '', $valStr));

        if ($price && $price != $mfPrice) {
            return false;
        }
        if ($currncy && $currncy != $mfCurrncy) {
            return false;
        }

        return true;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param object $json
     *
     * @return object
     */
    protected function getSuccessData($json) {

        foreach ($json->Data->InvoiceTransactions as $transaction) {
            if ($transaction->TransactionStatus == 'Succss') {
                $json->Data->InvoiceStatus = 'Paid';
                $json->Data->InvoiceError  = '';

                $json->Data->focusTransaction = $transaction;
                return $json->Data;
            }
        }
        return $json->Data;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param object $json
     * @param string $keyId
     * @param string $KeyType
     *
     * @return object
     */
    protected function getErrorData($json, $keyId, $KeyType) {

        //------------------
        //case 1: payment is Failed
        $focusTransaction = $this->{"getLastTransactionOf$KeyType"}($json, $keyId);
        if ($focusTransaction && $focusTransaction->TransactionStatus == 'Failed') {
            $json->Data->InvoiceStatus = 'Failed';
            $json->Data->InvoiceError  = $focusTransaction->Error . '.';

            $json->Data->focusTransaction = $focusTransaction;

            return $json->Data;
        }

        //------------------
        //case 2: payment is Expired
        //all myfatoorah gateway is set to Asia/Kuwait
        $ExpiryDateTime = $json->Data->ExpiryDate . ' ' . $json->Data->ExpiryTime;
        $ExpiryDate     = new \DateTime($ExpiryDateTime, new \DateTimeZone('Asia/Kuwait'));
        $currentDate    = new \DateTime('now', new \DateTimeZone('Asia/Kuwait'));

        if ($ExpiryDate < $currentDate) {
            $json->Data->InvoiceStatus = 'Expired';
            $json->Data->InvoiceError  = 'Invoice is expired since ' . $json->Data->ExpiryDate . '.';

            return $json->Data;
        }

        //------------------
        //case 3: payment is Pending
        //payment is pending .. user has not paid yet and the invoice is not expired
        $json->Data->InvoiceStatus = 'Pending';
        $json->Data->InvoiceError  = 'Pending Payment.';

        return $json->Data;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param object         $json
     * @param integer|string $keyId
     *
     * @return object|null
     */
    protected function getLastTransactionOfPaymentId($json, $keyId) {

        foreach ($json->Data->InvoiceTransactions as $transaction) {
            if ($transaction->PaymentId == $keyId && $transaction->Error) {
                return $transaction;
            }
        }
        return null;
    }

    /**
     *
     * @param object $json
     *
     * @return object
     */
    protected function getLastTransactionOfInvoiceId($json) {

        usort($json->Data->InvoiceTransactions, function ($a, $b) {
            return strtotime($a->TransactionDate) - strtotime($b->TransactionDate);
        });

        return end($json->Data->InvoiceTransactions);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Refund a given PaymentId or InvoiceId
     *
     * @param int|string        $keyId        payment id that will be refunded
     * @param double|int|string $amount       the refund amount
     * @param string            $currencyCode the amount currency
     * @param string            $comment      reason of the refund
     * @param int|string        $orderId      used in log file (default value: null)
     * @param string            $keyType      supported keys are (InvoiceId, PaymentId)
     *
     * @return object
     */
    public function refund($keyId, $amount, $currencyCode = null, $comment = null, $orderId = null, $keyType = 'PaymentId')
    {

        $url  = "$this->apiURL/v2/MakeRefund";

        $postFields = [
            'Key'                     => $keyId,
            'KeyType'                 => $keyType,
            'RefundChargeOnCustomer'  => false,
            'ServiceChargeOnCustomer' => false,
            'Amount'                  => $amount,
            'CurrencyIso'             => $currencyCode,
            'Comment'                 => $comment,
        ];

        return $this->callAPI($url, $postFields, $orderId, 'Make Refund');
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Create an invoice using Embedded session (POST API)
     *
     * @param array          $curlData  invoice information
     * @param integer|string $sessionId session id used in payment process
     * @param integer|string $orderId   used in log file (default value: null)
     *
     * @return array
     */
    public function embeddedPayment($curlData, $sessionId, $orderId = null) {

        $curlData['SessionId'] = $sessionId;

        $json = $this->callAPI("$this->apiURL/v2/ExecutePayment", $curlData, $orderId, 'Embedded Payment');
        return ['invoiceURL' => $json->Data->PaymentURL, 'invoiceId' => $json->Data->InvoiceId];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get session Data (POST API)
     *
     * @param string         $userDefinedField Customer Identifier to dispaly its saved data
     * @param integer|string $orderId          used in log file (default value: null)
     *
     * @return object
     */
    public function getEmbeddedSession($userDefinedField = '', $orderId = null) {

        $customerIdentifier = ['CustomerIdentifier' => $userDefinedField];

        $json = $this->callAPI("$this->apiURL/v2/InitiateSession", $customerIdentifier, $orderId, 'Initiate Session');
        return $json->Data;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Register Apple Pay Domain (POST API)
     *
     * @param string $url Site URL
     *
     * @return object
     */
    public function registerApplePayDomain($url) {

        $domainName = ['DomainName' => parse_url($url, PHP_URL_HOST)];
        return $this->callAPI("$this->apiURL/v2/RegisterApplePayDomain", $domainName, '', 'Register Apple Pay Domain');
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
}


/**
 * This class handles the shipping process of MyFatoorah API endpoints
 *
 * @author    MyFatoorah <tech@myfatoorah.com>
 * @copyright 2021 MyFatoorah, All rights reserved
 * @license   GNU General Public License v3.0
 */
class ShippingMyfatoorahApiV2 extends MyfatoorahApiV2 {
    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get MyFatoorah Shipping Countries (GET API)
     *
     * @return object
     */
    public function getShippingCountries() {

        $url  = "$this->apiURL/v2/GetCountries";
        $json = $this->callAPI($url, null, null, 'Get Countries');
        return $json;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get Shipping Cities (GET API)
     *
     * @param integer $method      [1 for DHL, 2 for Aramex]
     * @param string  $countryCode It can be obtained from getShippingCountries
     * @param string  $searchValue The key word that will be used in searching
     *
     * @return object
     */
    public function getShippingCities($method, $countryCode, $searchValue = '') {

        $url = $this->apiURL . '/v2/GetCities'
                . '?shippingMethod=' . $method
                . '&countryCode=' . $countryCode
                . '&searchValue=' . urlencode(substr($searchValue, 0, 30));

        $json = $this->callAPI($url, null, null, "Get Cities: $countryCode");
        //        return array_map('strtolower', $json->Data->CityNames);
        return $json;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Calculate Shipping Charge (POST API)
     *
     * @param array $curlData the curl data contains the shipping information
     *
     * @return object
     */
    public function calculateShippingCharge($curlData) {

        $url  = "$this->apiURL/v2/CalculateShippingCharge";
        $json = $this->callAPI($url, $curlData, null, 'Calculate Shipping Charge');
        return $json;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
}
