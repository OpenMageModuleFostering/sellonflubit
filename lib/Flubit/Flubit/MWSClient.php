<?php
//namespace flubitApi;

class Flubit_Flubit_MWSClient
{
    
    /* CONSTANTS */
    const BASE_URI         = 'api.sandbox.weflubit.com';
    const METHOD_TYPE_GET  = 'GET';
    const METHOD_TYPE_POST = 'POST';

    /* PRIVATE VARS */
    private $apiKey;
    private $apiSecret;
    //private $timestampFormat = "Y-m-d\TH:i:sO";

    /**
    * Initial API call, establishes connection with seletced API call
    * 
    * @param mixed $apiKey
    * @param mixed $apiSecret
    * @return flubitClient
    */
    public function __construct($apiKey, $apiSecret) {
        $this->apiKey    = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
    * Status is used to varify the account and will return either an staus message or product count.
    * 
    * @param mixed $returnType
    */
    public function status($returnType) {
        $flbStatus = $this->queryApi('1/account/status.xml', self::METHOD_TYPE_GET);
        if($returnType == "message"){
            $result = new SimpleXMLElement($flbStatus);
            if(!$result['code']){
                $flubitStatus = "Account Verified and Active!";
            } else {
                if($result['code'] == 144){
                    $flubitStatus = "Account Not Verified!";    
                } else{
                    $flubitStatus = "Error: ".$result['code'] ." - ". $result['message'];
                }
            }
            
            return $flubitStatus;
            
        } elseif($returnType == "count"){
            $result = new SimpleXMLElement($flbStatus);
            echo $result;
            if(!$result['code']){
                if($result['active_products'] == 0){$flubitStatus = "No Products";}
            } else {
                if($result['code'] == 144){
                    $flubitStatus = "Account Not Verified!";    
                } else{
                    $flubitStatus = "Error: ".$result['code'] ." - ". $result['message'];
                }
            }
            return $flubitStatus;
        } else {
            return $flbStatus; 
        }
    }

    /**
     * Query API feed method in create mode
     *
     * @param  string $xmlString
     * @return string
     */
    public function createProduct($xmlString)
    {
        Mage::log($xmlString ,null, 'hum.txt');
        $response = $this->queryApi(
                '1/products/feed.xml?type=create', 
                self::METHOD_TYPE_POST, 
                $xmlString
                );
        
        Mage::log('Response: '.$response ,null, 'hum.txt');
        return $response;
    }

    /**
     * Query API feed method in update mode
     *
     * @param  string $xmlString
     * @return string
     */
    public function updateProduct($xmlString)
    {
        return $this->queryApi(
                '1/products/feed.xml', 
                self::METHOD_TYPE_POST, 
                $xmlString
                );
    }

    /**
     * Query API feed method with feed ID
     *
     * @param  integer $feedID
     * @return string
     */
    public function getProductFeedStatus($feedID)
    {
        return $this->queryApi(
                sprintf("1/products/feed/%s.xml", $feedID), 
                self::METHOD_TYPE_GET
                );
    }

    /**
     * Query API filter method
     *
     * @param  string $from
     * @param  string $status
     * @return string
     */
    public function filterOrders($from, $status=null)
    {
        $params = array('from' => $from, 'status' => $status);

        return $this->queryApi(
                '1/orders/filter.xml', 
                self::METHOD_TYPE_GET, 
                null, 
                $params
                );
    }

    /**
     * Query API dispatch method
     *
     * @param  string $xmlString
     * @param  string $orderId
     * @param  string $type
     * @return string
     */
    public function dispatchOrder($xmlString, $orderId, $type = 'flubit')
    {
        $orderTypeKey = sprintf('%s_order_id', $type);
        $params       = array($orderTypeKey => $orderId);

        return $this->queryApi(
                '1/orders/dispatch.xml', 
                self::METHOD_TYPE_POST, 
                $xmlString, 
                $params
                );
    }

    /**
     * Query API cancel method
     *
     * @param  string $xmlString
     * @param  string $orderId
     * @param  string $type
     * @return string
     */
    public function cancelOrder($xmlString, $orderId, $type = 'flubit')
    {
        $orderTypeKey = sprintf('%s_order_id', $type);
        $params       = array($orderTypeKey => $orderId);

        return $this->queryApi(
                '1/orders/cancel.xml', 
                self::METHOD_TYPE_POST, 
                $xmlString, 
                $params
                );
    }

    /**
     * Query API refund order method
     *
     * @param  string $orderId
     * @param  string $type
     * @return string
     */
    public function refundOrder($orderId, $type = 'flubit')
    {
        $orderTypeKey = sprintf('%s_order_id', $type);
        $params       = array($orderTypeKey => $orderId);

        return $this->queryApi(
                '1/orders/refund.xml', 
                self::METHOD_TYPE_POST, 
                null, 
                $params
                );
    }

    /**
     * Create HTTP auth header
     *
     * Uses new nonce and api signature each time it's called
     *
     * @return string
     */
    private function getDefaultHeaders() {
        $timeOfCreation = date('Y-m-d\TH:i:s\Z');
        $nonce          = $this->generateNonce();
        $signature      = $this->generateApiSigniture($nonce, $timeOfCreation, $this->apiSecret);
        $rawAuthString  = "\tauth-token\n\tkey=\"%s\",\n\tsignature=\"%s\",\n\tnonce=\"%s\",\n\tcreated=\"%s\"";
        $authString     = sprintf($rawAuthString, $this->apiKey, $signature, $nonce, $timeOfCreation);

        return $authString;
    }

    /**
     * Create new API signature using given params
     *
     * @param  string $nonce
     * @param  string $timeOfCreation
     * @param  string $apiSecret
     * @return string
     */
    private function generateApiSigniture($nonce, $timeOfCreation, $apiSecret) {
        return base64_encode(sha1(base64_decode($nonce) . $timeOfCreation . $apiSecret, true));
    }

    /**
     * Create new nonce
     *
     * Uses the given string if supplied, or current timestamp if not
     *
     * @param  string $randomString
     * @return string
     */
    private function generateNonce($randomString = null) {
        if (!$randomString) {$randomString = microtime();}
        $nonce = md5($randomString);

        return $nonce;
    }

    /**
     * Query the API
     *
     * @param  string $methodUri
     * @param  string $methodType
     * @param  string  $postString
     * @param  array $params
     * @return string
     */
    private function queryApi($methodUri, $methodType, $postString = null, array $params = null)
    {
        $queryUri = self::BASE_URI . $methodUri;

        if ($params) {
            $queryUri .= '?' . http_build_query($params);
        }

        $ch = curl_init($queryUri);
        
        if ($methodType === self::METHOD_TYPE_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$postString");
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml', 
            'auth-token: ' . $this->getDefaultHeaders()
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
