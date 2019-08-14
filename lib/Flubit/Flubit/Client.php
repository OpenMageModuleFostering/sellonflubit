<?php

class Flubit_Flubit_Client {
    /* CONSTANTS */

    const BASE_URI = 'http://api.sandbox.weflubit.com';
    const METHOD_TYPE_GET = 'GET';
    const METHOD_TYPE_POST = 'POST';

    /* PRIVATE VARS */

    private $client;
    private $timestampFormat = "Y-m-d\TH:i:sO";
    private $orderTimestampFormat = "Y-m-d\TH:i:sP";
    private $apiKey;
    private $apiSecret;
    private $_domain;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $domain
     */
    public function __construct($apiKey, $apiSecret, $domain = 'http://api.sandbox.weflubit.com') {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->_domain = $domain;
    }

    /**
     * Create HTTP auth header
     *
     * Uses new nonce and api signature each time it's called
     *
     * @return string
     */
    private function generateAuthToken() {
        try{
        $dateTime = new DateTime('UTC');
        $time = $dateTime->format($this->timestampFormat);

        $nonce = $this->generateNonce();

        $signature = base64_encode(
                sha1(
                        base64_decode($nonce) . $time . $this->apiSecret, true
                )
        );
        return sprintf(
                "key=\"%s\", signature=\"%s\", nonce=\"%s\", created=\"%s\"", $this->apiKey, $signature, $nonce, $time
        );
        } catch (Exception $e) {
            Mage::log('Generate Authentication token Exception '. $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    private function generateNonce() {
        try{
        $nonce = md5(uniqid(mt_rand(), true));
        return $nonce;
        } catch (Exception $e) {
            Mage::log('Post Request Exception ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * 
     *  Create 
     * 
     */

    /**
     * Return a response from the API based on the uri provided
     * 
     * @param mixed $uri
     * @param mixed $payload
     * @param mixed $queryParams
     */
    private function getPostRequest($uri, $payload = null, array $queryParams = array()) {
        try{
        $fields_string = '';
        
        $ch = curl_init($this->_domain . '/1/' . $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/atom+xml', 'auth-token: ' . $this->generateAuthToken()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $response = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
        $http_code = $curl_info['http_code'];
        curl_close($ch);
        
        if(($http_code == 200) || ($http_code == 202) || ($http_code == 304)) {
		   Mage::log('Post Request HTTP CODE Error ' . $http_code . print_r($curl_info, true) . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_COMMUNICATION);
		} else {
				$helper = Mage::helper('flubit');
				$response = print_r($response, true);
				$feedid = $http_code . ' error';
				$helper->logCommunicationErrors (print_r($curl_info,true),$response,$feedid,"Communication Error");
				Mage::log('Post Request HTTP CODE Success ' . $http_code . print_r($curl_info, true) . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_COMMUNICATION);
        }   
        
        } catch (Exception $e) {
            Mage::log('Post Request Exception ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
        return $response;
    }

    /**
     * Create a new product when given valid xml. 
     * 
     * @param mixed $productXml
     */
    public function createProducts($productXml) {
        try{
        return $this->getPostRequest('products/feed.xml?type=create', $productXml);
        } catch (Exception $e) {
            Mage::log('Post Request Exception ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * 
     *  Read 
     *  
     */

    /**
     * Return a response from the API based on the uri provided
     * 
     * @param mixed $uri
     */
    private function getGetRequest($uri) {
        try{
        $ch = curl_init($this->_domain . '/1/' . $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/atom+xml', 'auth-token: ' . $this->generateAuthToken()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        //curl_setopt($ch, CURLINFO_HEADER_OUT , 1);
        
        $response = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
        $http_code = $curl_info['http_code'];
        curl_close($ch);
        
        if(($http_code == 200) || ($http_code == 202) || ($http_code == 304)) {
			Mage::log('Post Request HTTP CODE Success ' . $http_code .  print_r($curl_info, true) . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_COMMUNICATION);
		} else {
				$helper = Mage::helper('flubit');
				$response = print_r($response, true);
				$feedid = $http_code . ' error';
				$helper->logCommunicationErrors (print_r($curl_info,true),$response,$feedid,"Communication Error");
				Mage::log('Post Request HTTP CODE Success ' . $http_code . print_r($curl_info, true) . $response , null, Flubit_Flubit_Helper_Data::FLUBIT_COMMUNICATION);
		} 
        
        } catch(Exception $e) {
            Mage::log('Get Requsest ', null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
		}
        return $response;
    }

    /**
     * Return the current user's account status, including number of active products
     *                            
     */
    public function getAccountStatus() {
        try{
        return $this->getGetRequest('account/status.xml');
        } catch (Exception $e) {
            Mage::log('Post Request Exception ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Get orders based on the provided time and status.
     * 
     * @param DateTime $from date the order is from
     * @param array $status 'refunded', 'cancelled','dispatched','awaiting_dispatch'
     */
    public function getOrders(DateTime $from, $status) {
        try{
        $orderArray = array();
		$order = array();
        $from = $from->format($this->orderTimestampFormat);
        $data = $this->getGetRequest('orders/filter.xml?from=' .urlencode($from). '&status=' . $status);
        
        $data = json_decode(json_encode((array) simplexml_load_string($data)), 1);
        
        if (isset($data['order']['@attributes'])) {
            foreach ($data as $order) {
                $orderArray[$order['@attributes']['id']] = $order;
            }
        } else {
            if(count($data['order']) > 0 )
            foreach ($data['order'] as $order) {
                $orderArray[$order['@attributes']['id']] = $order;
            }
        }
        return $orderArray;
        } catch (Exception $e) {
            Mage::log('Get Orders ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Get a list of products based on the provided values. If SKU is provided ignore other values.
     * 
     * @param mixed $isActive
     * @param mixed $limit
     * @param mixed $page
     * @param mixed $sku
     */
    public function getProducts($isActive, $limit, $page, $sku = null) {
        try {
        if ($sku) {
            return $this->getGetRequest('products/filter.xml?is_active=' . $isActive . '&sku=' . $sku);
        } else {
            return $this->getGetRequest('products/filter.xml?is_active=' . $isActive . '&limit=' . $limit . '&page=' . $page);
        }
        } catch (Exception $e) {
            Mage::log('Get Products ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Get product feed by feed ID
     *                   
     * @param mixed $feedID
     */
    public function getProductsFeed($feedID) {
        try {
            if(($feedID != '') || ($feedID != null)) {
            return $this->getGetRequest('products/feed/' . $feedID . '.xml');
            }
        } catch (Exception $e) {
            Mage::log('Get Product Feed ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * 
     * Update
     * 
     */

    /**
     * Update a product that is already in the system. The only values that can be updated here are is_active, base_price and stock
     * 
     * @param mixed $productXml
     */
    public function updateProducts($productXml) {
        try {
        $xml = $this->getPostRequest('products/feed.xml', $productXml);
        return $xml;
        } catch (Exception $e) {
            Mage::log('Update Products ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Generate a dispatch order and post it to the api using the flubit ID
     * 
     * @param mixed $id
     * @param DateTime $dateTime
     * @param mixed $params
     */
    public function dispatchOrderByFlubitId($id, $dateTime,$params) {
        try {
            if (($id != '') && ($dateTime != '')) {
                if(!is_array($params))
                    $params = array();
                    $orderXML = $this->generateDispatchOrderPayload($dateTime, $params);
                    return $this->getPostRequest('orders/dispatch.xml?flubit_order_id=' . $id, $orderXML);
                } else {
                    Mage::log('Id or datetime found blank. Id =  ' . $id . '  Datetime' . $dateTime  , null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                }
                
        } catch (Exception $e) {
            Mage::log('Dispatch Order By Flubit ID ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Generate a dispatch order and post it to the api using the merchant order ID
     * Params = 
     * 
     * @param mixed $id
     * @param DateTime $dateTime
     * @param mixed $params 
     */
    public function dispatchOrderByMerchantOrderId($id, DateTime $dateTime, array $params) {
        try{
        $orderXML = $this->generateDispatchOrderPayload($dateTime, $params);
        return $this->getPostRequest('orders/dispatch.xml?merchant_order_id=' . $id, $orderXML);
        } catch (Exception $e) {
            Mage::log('Dispatch Order By Merchant Order ID ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }
  
    /**
     * {@inheritdoc}
     */
    public function cancelOrderByFlubitId($id, $reason) {
        try{
            if($id) {
                $orderXML = $this->generateCancelOrderPayload($reason);
                return $this->getPostRequest('orders/cancel.xml?flubit_order_id=' . $id, $orderXML);
            } else {
                Mage::log('Cancel order by flubit ID. Id found null. flubit ID ' . $id , null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
            }
            
        } catch (Exception $e) {
            Mage::log('Cancel order by flubit ID ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cancelOrderByMerchantOrderId($id, $reason) {
        try {
        $payload = $this->generateCancelOrderPayload($reason);

        $request = $this->getPostRequest(
                'orders/cancel.xml', $payload, array(
            'merchant_order_id' => $id
                )
        );

        return $this->call($request);
         } catch (Exception $e) {
            Mage::log('cancelOrderByMerchantOrderId ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refundOrderByFlubitId($id, $reason) {
        try {
        $orderXML = $this->generateRefundOrderPayload($reason);
        return $this->getPostRequest('orders/refund.xml?flubit_order_id=' . $id, $orderXML);
         } catch (Exception $e) {
            Mage::log('refundOrderByFlubitId ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refundOrderByMerchantOrderId($id) {
        try {
        $request = $this->getPostRequest(
                'orders/refund.xml', null, array(
            'merchant_order_id' => $id
                )
        );

        return $this->call($request);
         } catch (Exception $e) {
            Mage::log('refundOrderByMerchantOrderId ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Generate the xml for a dispatch order
     *      
     * @param DateTime $dateTime
     * @param mixed $params
     */
    private function generateDispatchOrderPayload($dateTime, array $params) {
        $courier = isset($params['courier']) ? $params['courier'] : '';
        $consignmentNumber = isset($params['consignment_number']) ? $params['consignment_number'] : '';
        $trackingUrl = isset($params['tracking_url']) ? $params['tracking_url'] : '';

        return <<<EOH
<?xml version="1.0" encoding="UTF-8"?>
<dispatch>
    <dispatched_at>{$dateTime}</dispatched_at>
    <courier>{$courier}</courier>
    <consignment_number>{$consignmentNumber}</consignment_number>
    <tracking_url>{$trackingUrl}</tracking_url>
</dispatch>
EOH;
//$dateTime->format($this->timestampFormat)
    }

    private function generateCancelOrderPayload($reason) {
        return <<<EOH
<?xml version="1.0" encoding="UTF-8"?>
<cancel>
    <reason>{$reason}</reason>
</cancel>
EOH;
    }

    private function generateRefundOrderPayload($reason) {
        return <<<EOH
<?xml version="1.0" encoding="UTF-8"?>
<refund>
    <reason>{$reason}</reason>
</refund>
EOH;
    }

}