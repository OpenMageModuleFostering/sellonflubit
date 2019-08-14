<?php



/**

 * Class Flubit Model Cron

 * 

 * @package Flubit

 * @category Flubit_Model

 * @author Flubit team

 */

class Flubit_Flubit_Model_Config extends Flubit_Flubit_Client {



    const CONSUMER_API = 'flubit_section/flubit_configuration/flubit_consumer_key';

    const SECRET_KEY = 'flubit_section/flubit_configuration/flubit_secret';

    const CONSUMER_URL = 'flubit_section/flubit_configuration/flubit_url';

    const LOG_TYPE = 'flubit_section/flubit_setup/log_type_list';



    /**

     * Construct and autoload initModule

     */

    public function __construct() {

        

    }



    /**

     * Method for initModule

     */

    public function initModule() {

        return $this->_initModule();

    }



    /**

     * Method for initModules

     */

    protected function _initModule() {

        try {

            $api = Mage::getStoreConfig(self::CONSUMER_API);

            $key = Mage::getStoreConfig(self::SECRET_KEY);

            $url = Mage::getStoreConfig(self::CONSUMER_URL);

            if (!empty($api) && !empty($key)) {

                $obj = new Flubit_Flubit_Client($api, $key, $url);

                return $obj;

            } else {

                Mage::log('Missing Keys in configuration', null, Flubit_Flubit_Helper_Data::FLUBIT_MISSING_CONFIG);

            }

        } catch (Exception $e) {

            Mage::log(__LINE__ . ' _initModule ', null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**

     * Method for Create Flubit Products 

     * 

     * @param Xml String $xml

     * @return Xml String

     */

    public function createFlubitProducts($xml) {

        try {

            $flubit = $this->_initModule();

            $result = $flubit->createProducts($xml);

            $result = new SimpleXMLElement($result);

            return $result;

        } catch (Exception $e) {

            Mage::log(__LINE__ . ' createFlubitProducts ', null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**

     * Method for Update Flubit Products 
     * 
     * @param Xml String $xml
     * @return Xml String
     */

    public function updateFlubitProducts($xml) {

        try {

            $flubit = $this->_initModule();

            $result = $flubit->updateProducts($xml);

            $result = new SimpleXMLElement($result);


            return $result;

        } catch (Exception $e) {

            Mage::log(__LINE__ . ' updateFlubitProducts ', null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**
     * Method for Get Flubit Orders 
     * 
     */

    public function getFlubitOrders() {

        try {

            $flubit = $this->_initModule();

            //get flubit last order fetched time

            $from = new DateTime(date('m/d/Y', strtotime('-1 year')));

            $timeLastRun = Mage::getModel('flubit/ordertime')->getCollection();

            $timeLastRun->getSelect()->limit(1);

            foreach ($timeLastRun as $lastRun) {

                $from = new DateTime($lastRun->date_time);

            }


            $validStatuses = array('awaiting_dispatch');


            foreach ($validStatuses as $status) {

                $orderArray = $flubit->getOrders($from, $status);

                $num_orders = count($orderArray);


                if ($num_orders > 0) {

                    try {

                        $order_last = end($orderArray);

                        $lasrOrderTimeRaw = $order_last['created_at'];



                        $lasrOrderTimeArr = explode('+', $lasrOrderTimeRaw);

                        $lasrOrderTimePre = $lasrOrderTimeArr[0];

                        $addOneSec = '';



                        if ($num_orders != 100)

                            $addOneSec = '+1 seconds ';



                        $lasrOrderTimeTot = strtotime($addOneSec . $lasrOrderTimePre);

                        $updateLastOrderTime = date('Y-m-d H:i:s', $lasrOrderTimeTot);

                        if (is_object($lastRun)) {

                            // insert the last order created time ignoring the timezone

                            $lastRun->setDateTime($updateLastOrderTime)

                                    ->setFetchedOrders($num_orders)

                                    ->save();

                        } else {

                            $first_run = Mage::getModel('flubit/ordertime');

                            $first_run->setDateTime($updateLastOrderTime)

                                    ->setFetchedOrders($num_orders)

                                    ->save();

                        }

                    } catch (Exception $e) {

                        Mage::log('Time Update Exception Log ' . $e, NULL, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

                    }

                }

				if (count($orderArray) > 0) {

                    $request = 'Requested Date Time ' . print_r($from, true) . '. Flubit Orders with Status :' . $status;

                    $this->logFlubitOrdersRequestResponse($request, $orderArray, 'fetchOrder', 0, 0);

                }

                if (!$this->createOrderInMagento($orderArray)) {

                    break;

                }

            }

        } catch (Exception $e) {

            Mage::log(__LINE__ . ' getFlubitOrders ', null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }

		

    /**

     * Method for Inactive Products

     * 

     * @param string $sku

     * @param integer $status

     * @return array

     */

    public function inactiveProduct($sku, $status = 0) {

        try {

		$flubit_products = Mage::getModel('flubit/flubit')->getCollection()

							->addfieldtofilter('sku', $sku);

			foreach ($flubit_products as $flubitprd) {

				$price  = $flubitprd->getPrice();

				$qty  = $flubitprd->getQty();

			}				

			

            $flubit = $this->_initModule();

$productXml = <<<EOH

<?xml version="1.0" encoding="UTF-8"?>

<products>

<product sku="$sku">

	<is_active>false</is_active>

	<base_price>$price</base_price>

	<stock>$qty</stock>

</product>

</products>

EOH;


            $res = $flubit->updateProducts($productXml);

            $result = simplexml_load_string($res);


            if ($result->getName() == 'id') {

                $this->logFlubitOrdersRequestResponse($productXml, $result->asXML(), 'deleteProduct', 0, 0, $result[0]);

            }

            $json = json_encode($result);

            $res_array = json_decode($json, TRUE);

            return $res_array[0];

        } catch (Exception $e) {

            Mage::log(__LINE__ . ' inactiveProduct ', null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**

     * Method for get Products Feed Status

     * 

     * @param string $feed

     * @param string $result

     * @return array

     */

    public function getproductFeedStatus($feed, $result = '') {

        try {

            $flubit = $this->_initModule();

            //Mage::log('Request Feed ID :' . $feed, null, Flubit_Flubit_Helper_Data::FLUBIT_CHECK_FEED);

            $res = $flubit->getProductsFeed($feed);

            Mage::log('Response  :' . $res, null, Flubit_Flubit_Helper_Data::FLUBIT_CHECK_FEED);

            Mage::log('Response Feed ID :' . $feed, null, Flubit_Flubit_Helper_Data::FLUBIT_CHECK_FEED);

            if ($result == 'xml') {

                return $res;

            } else {

                $result = simplexml_load_string($res);

                $json = json_encode($result);

                $res_array = json_decode($json, TRUE);

				return $res_array['results']['updated'];

            }

        } catch (Exception $e) {

            Mage::log('Exception Get Product Feed Status' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**

     * Method for Create order in Magento

     * 

     * @param array $orderArray

     * @return boolean

     */

    public function createOrderInMagento($orderArray) {

	        foreach ($orderArray as $key => $value) {

            try {

                $outOfStock = false;

                $OrderFailing = false;

                if (trim(strtolower($value['status'])) != 'awaiting_dispatch') {

                    continue;

				}

                $ordersCollection = Mage::getModel('sales/order')->getCollection();

                $ordersCollection->addFieldToFilter('flubit_order_id', $key);

				if (count($ordersCollection) != 0) {

                    if (count($orderArray["$key"]) > 0) {

                      	// Mage::log(print_r($orderArray["$key"], true), null, 'flubit_magento_create_order.log');

                        //$request = 'Flubit order id <b>: ' . print_r($orderArray["$key"]) . '</b> Already Exist in Magento. ', true);

                        //$this->logFlubitOrdersRequestResponse($request, 'Order Exist in Magento Flubit order id is Order ID : ' . $key, 'insertOrder', 0, 1, $key);

                    }

                    continue;

                }


                $flubitOrderId = $key;

                $quote = Mage::getModel('sales/quote');

                $quote->setIsMultiShipping(false)

                        ->setStore(Mage::app()->getDefaultStoreView())

                        ->setIsSuperMode(true);

                $subtotal = 0;

                $shipping_amount = ($value['shipping_cost']) ? $value['shipping_cost'] : 0;

                $tax_percentage = 0;

                $data = array();

                foreach ($value['products'] as $pValue) {

                    $sku = $pValue['@attributes']['sku'];

                    $taxRate = (($pValue['tax_rate'] / 100) + 1);



                    //Flubit customization at item row level

                    $price = $pValue['unit_price_sold_at'];

                    $tax_percentage = $pValue['tax_rate'];

                    $tax_amount = $price - ($price / $taxRate);

                    $original_price = $pValue['unit_base_price'];

                    $rowsubtotal = $price * $pValue['quantity'];

                    $discount = 0;

                    $row_total = $rowsubtotal + $tax_amount;



                    //Prepare totals

                    $subtotal+= $rowsubtotal;



                    $productid = Mage::getModel('catalog/product')->getIdBySku(trim($sku));

                    $product = Mage::getModel('catalog/product')->load($productid);



                    if (!$product->getId()) {

                        //$request = 'Flubit order id <b>: ' . print_r($orderArray["$key"]) . '</b> Already Exist in Magento. ', true);

                        $this->logFlubitOrdersRequestResponse(print_r($orderArray["$key"], true), 'Product SKU: <b>"' . trim($sku) . '</b>" does not exists in Magento for Flubit Order ID: ' . $key, 'insertOrder', 0, 1, 'Flubit order id :' . $key);

                        Mage::log('Product SKU: ' . trim($sku) . ' does not exists in Magento ', null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

                        $OrderFailing = true;

                        continue;

                    }



                    $stock = $product->getStockItem();

                    if (!$stock->getIsInStock()) {

                        Mage::log('Product SKU: ' . $product->getSku() . ' is not in stock for Flubit Order ID: ' . $key, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

                        $this->logFlubitOrdersRequestResponse(print_r($orderArray["$key"], true), 'Product SKU: <b>"' . $product->getSku() . '"</b> is not in stock for Flubit Order ID: ' . $key, 'insertOrder', 0, 1, 'Flubit order id :' . $key);

                        $outOfStock = true;

                        $OrderFailing = true;

                        continue;

                    }



                    //prepare items data

                    $data[trim($sku)]['unit_base_price'] = $original_price;

                    $data[trim($sku)]['unit_price_sold_at'] = $price;

                    $data[trim($sku)]['row_subtotal'] = $rowsubtotal;



                    $quote_item = Mage::getModel('flubit/quote_item');

                    $quote_item

                            ->setProduct($product)

                            ->setPrice($price)

                            ->setOriginalPrice($original_price)

                            ->setCustomPrice($price)

                            ->setOriginalCustomPrice($price)

                            ->setSubtotal($rowsubtotal)

                            ->setDiscountAmount($discount)

                            ->setRowTotal($rowsubtotal)

                            ->setQuote($quote)

                            ->setQty((integer) $pValue['quantity']);

                    $quote->addItem($quote_item);
                    

                }



                if ($outOfStock || $OrderFailing) {

                 Mage::log('Order failed for Flubit Order ID: ' . $key, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);
                    continue;
                    
                   // lets log this properly smb
                   
                   $this->logFlubitOrdersRequestResponse(print_r($orderArray["$key"], true), 'Order failed for Flubit Order ID: ' . $key, 'failedOrder', 0, 1, 'Flubit order id :' . $key);

                }



                $shippingArray = $value['shipping_address'];

                $name = explode(" ", (string) $shippingArray['name']);



                $firstname = $name[0];

                $lastname = isset($name[1]) ? $name[1] : '';

                if (!isset($shippingArray['address1'])) {

                    Mage::log('address 1 ' . trim($shippingArray['address1']) . ' Missing for Flubit Order ID: ' . $key, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

                    $this->logFlubitOrdersRequestResponse(print_r($orderArray["$key"], true), 'address 1 <b>"' . trim($shippingArray['address1']) . '"</b> Missing for Flubit Order ID: ' . $key, 'insertOrder', 0, 1, 'Flubit order id :' . $key);

                    continue;

                }

                /* set address1,address2,state as blank if user doesnot provide address1,address2 and state  */

                $address1 = ((count($shippingArray['address1']) > 0) ? (string) $shippingArray['address1'] : '');

                $address2 = ((count($shippingArray['address2']) > 0) ? (string) $shippingArray['address2'] : '');



                $state = ((count($shippingArray['state']) > 0) ? (string) $shippingArray['state'] : '');



                $billingAddress = array(

                    'firstname' => $firstname,

                    'lastname' => $lastname,

                    //'company' => '',

                    'email' => $shippingArray['email'],

                    'street' => array(

                        $address1,

                        $address2

                    ),

                    'city' => (string) $shippingArray['city'],

                    'region_id' => '',

                    'region' => $state,

                    'postcode' => (string) $shippingArray['postal_code'],

                    'country_id' => (string) $shippingArray['country_code'],

                    'telephone' => $shippingArray['phone'],

                    'customer_password' => '',

                    'confirm_password' => '',

                    'save_in_address_book' => '0',

                    'use_for_shipping' => '1',

                );



                //set order is flubit

                Mage::getSingleton('checkout/session')->setData('flubit_order', true);



                $quote->getBillingAddress()

                        ->setShouldIgnoreValidation(true)

                        ->addData($billingAddress);



                $quote->getShippingAddress()

                        ->setShouldIgnoreValidation(true)

                        ->addData($billingAddress);



                // Create Shipment

                $shipping_cost = $shipping_amount;



                Mage::getSingleton('checkout/session')

                        ->setData('shipping_price', $shipping_cost);



                $quote->getShippingAddress()

                        ->setShippingMethod('flatrate_flatrate')

                        ->setCollectShippingRates(true)

                        ->collectShippingRates();



                $quote->setCheckoutMethod('guest')

                        ->setCustomerId(null)

                        ->setCustomerFirstname($firstname)

                        ->setCustomerLastname($lastname)

                        ->setCustomerEmail($quote->getBillingAddress()->getEmail())

                        ->setCustomerIsGuest(true)

                        ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);



                // Create Payment

                $quote->getShippingAddress()

                        ->setPaymentMethod('flubitterms');

                $payment = $quote->getPayment();

                $payment->importData(array('method' => 'flubitterms'));



                $quote->collectTotals();

                $quote->save();


                //create order

                if ($quote && ($quote instanceof Mage_Sales_Model_Quote)) {

                    $service = Mage::getModel('sales/service_quote', $quote);



                    $quote->setGrandTotal($subtotal + 0 + $shipping_cost);

                    $quote->setBaseGrandTotal($subtotal + 0 + $shipping_cost);

                    if (method_exists($service, 'submitAll')) {

                        $service->submitAll();

                        $order = $service->getOrder();

                    } else {

                        $order = $service->submit();

                    }



                    //check if order object present

                    if (!$order || !($order instanceof Mage_Sales_Model_Order)) {

                        Mage::log('Order Object is undefined for flubit order ' . $flubitOrderId, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

                        continue;

                    }



                    $order->setGrandTotal(($subtotal + 0 + $shipping_cost));

                    $order->setBaseGrandTotal(($subtotal + 0 + $shipping_cost));

                    $order->setTaxAmount(0);

                    $order->setShippingAmount($shipping_cost);

                    $order->setBaseShippingAmount($shipping_cost);

                    $order->setShippingInclTax($shipping_cost);

                    $order->setBaseShippingInclTax($shipping_cost);

                    $order->setFlubitOrderId($flubitOrderId);

                    $order->setFlubitOrderTaxRate($tax_percentage)

                            ->save();



                    // FIX fields amount & taxes

                    $products = Mage::getResourceModel('sales/order_item_collection')

                            ->setOrderFilter($order->getId());

                    foreach ($products as $product) {

                        $product->setBasePrice($data[$product->getSku()]['unit_price_sold_at']);

                        $product->setPrice($data[$product->getSku()]['unit_price_sold_at']);

                        $product->setSubtotal($data[$product->getSku()]['row_subtotal']);

                        $product->setBaseSubtotal($data[$product->getSku()]['row_subtotal']);

                        $product->setRowTotal($data[$product->getSku()]['row_subtotal']);

                        $product->setBaseRowTotal($data[$product->getSku()]['row_subtotal']);

                        $product->setPriceInclTax($data[$product->getSku()]['row_subtotal']);

                        $product->setBasePriceInclTax($data[$product->getSku()]['row_subtotal']);

                        $product->setRowTotalInclTax($data[$product->getSku()]['row_subtotal']);

                        $product->setBaseRowTotalInclTax($data[$product->getSku()]['row_subtotal']);

                        $product->setBaseTaxAmount(0);

                        $product->setTaxAmount(0);

                        $product->save();

                    }

                    $order->setBaseTaxAmount(0);

                    $order->setTaxAmount(0);

                    $order->setBaseSubtotal($subtotal);

                    $order->setSubtotal($subtotal);

                    $order->setBaseSubtotalInclTax($subtotal);

                    $order->setSubtotalInclTax($subtotal);



                    $order->setBaseShippingTaxAmount(0);

                    $order->setShippingTaxAmount(0);

					$order->save();



                    if ($order->canInvoice()) {

                        $invoiceId = Mage::getModel('sales/order_invoice_api')

                                ->create($order->getIncrementId(), array());

                        Mage::log('Invoice created for order ' . $order->getIncrementId() . '. Invoice Id is: ' . $invoiceId, null, 'flubitinvoice.log');

                    }

					} else {

						Mage::log('Quote object is undefined for order ' . $flubitOrderId, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

						continue;

					}





                try {

                    $flubitCollection = Mage::getModel('flubit/order')->getCollection();

                    $flubitCollection->addFieldToFilter('order_no', $order->getIncrementId());



                    if (count($flubitCollection) == 0) {

                        $flubitOrder = Mage::getModel('flubit/order');

                        $flubitOrder->setOrderNo($order->getIncrementId());

                        $flubitOrder->setStatus($order->getStatus());

                        $flubitOrder->setFlubitOrderId($flubitOrderId);

                        $flubitOrder->save();



                        $response = 'Flubit order ID : <b>"' . $flubitOrderId . '"</b> has been inserted successfully to Magento.';

                        //$request = 'Flubit order ID : ' . $flubitOrderId. ' has been inserted successfully to Magento.';

                        $feedid = 'Flubit order ID : "' . $flubitOrderId .'"';

                        $this->logFlubitOrdersRequestResponse(print_r($orderArray["$key"], true), $response, 'insertOrder', 0, 0, $feedid);

                    }

                } catch (Exception $e) {

                    Mage::log('Order Insert into flubit table= ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

                }



                Mage::getSingleton('checkout/session')->setData('flubit_order', false);

            } catch (Exception $e) {

                Mage::log('Order Exception = ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_ORDER);

            }

        }

        return true;

    }

		

    /**

     * 

     * Method for Dispatch Flubit Orders

     */

    public function dispatchFlubitOrders() {

        $flubit = $this->_initModule();

        $collection = Mage::getModel('sales/order')->getCollection();

        $collection->addFieldToFilter('main_table.status', 'complete');

        $collection->addFieldToSelect('flubit_order_id');

        $collection->addFieldToSelect('created_at');

        $collection->getSelect()->join(array('flubit_order' => Mage::getConfig()->getTablePrefix() . 'flubit_order'), 'flubit_order.order_no = main_table.increment_id and flubit_order.dispatch = 0 and flubit_order.refund = 0', array('flubit_order.flubit_order_id', 'flubit_order.flubit_id'));



        foreach ($collection as $order) {

            $datetime = date("Y-m-d\TH:i:sP", strtotime($order->getCreatedAt()));

            $response = $flubit->dispatchOrderByFlubitId($order->getFlubitOrderId(), $datetime, '');

            $xmlObjRes = simplexml_load_string($response);

            if ($xmlObjRes->getName() == 'success') {

                $flubitorder = Mage::getModel('flubit/order')->load($order->getFlubitId());

                if ($flubitorder->getId()) {

                    $flubitorder->setDispatch('1')

                    ->save();

                }

                Mage::log('Order Dispatched Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_DISPATCH_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for dispatch.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() .'"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'dispatchOrder', 1, 0, $feedid);

            } else {

                Mage::log('Order Dispatched Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_DISPATCH_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for dispatch.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'dispatchOrder', 0, 1, $feedid);

            }

        }

    }



    /**

     * 

     * Method for Cancel Flubit Orders

     */

	public function cancelFlubitOrders($flubitOrderId=0) {

        $flubit = $this->_initModule();

            if($flubitOrderId != 0) {

			$response = $flubit->cancelOrderByFlubitId($flubitOrderId, 'Order cancelled');

			$xmlObjRes = simplexml_load_string($response);

            if ($xmlObjRes->getName() == 'success') {

			    

				$collection = Mage::getModel('flubit/order')->getCollection();

				$collection->addFieldToFilter('flubit_order_id', $flubitOrderId);

				$collection->addFieldToSelect('flubit_id');

					foreach ($collection as $refund) {

						$refund->setData(array('flubit_id' => $refund->getFlubitId(),'refund' => '1'))

								->save();

					}

                

                Mage::log('Order Cancel success Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

				Mage::log('Order Cancel success Request' . $request, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

                $request = 'Flubit order ID : "' . $flubitOrderId . '" has been sent for cancelled.';

                $feedid = 'Flubit order ID : "' . $flubitOrderId . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'cancelOrder', 1, 0, $feedid);

            } else {

                Mage::log('Order Cancel Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

				Mage::log('Order Cancel Request' . $request, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

                $request = 'Flubit order ID : "' . $flubitOrderId . '" has been sent for cancelled.';

                $feedid = 'Flubit order ID : "' . $flubitOrderId . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'cancelOrder', 0, 1, $feedid);

            }

			} else {

			$collection = Mage::getModel('sales/order')->getCollection();

			$collection->addFieldToFilter('main_table.status', 'closed');

			$collection->addFieldToSelect('flubit_order_id');

			$collection->addFieldToSelect('created_at');

			$collection->getSelect()->join(array('flubit_order' => Mage::getConfig()->getTablePrefix() . 'flubit_order'), 'flubit_order.order_no = main_table.increment_id and flubit_order.dispatch = 0 and flubit_order.refund = 0', array('flubit_order.flubit_order_id', 'flubit_order.flubit_id'));

			

			foreach ($collection as $order) {

            $response = $flubit->cancelOrderByFlubitId($order->getFlubitOrderId(), 'Order cancelled');

            $xmlObjRes = simplexml_load_string($response);

            if ($xmlObjRes->getName() == 'success') {

			    $flubitorder = Mage::getModel('flubit/order')->load($order->getFlubitId());

                if ($flubitorder->getId()) {

				     $flubitorder->setRefund('1')

                    ->save();

                }

                Mage::log('Order Cancel Success Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

				Mage::log('Order Cancel Request' . $request, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

                $request = 'Flubit order ID : "' . $order->getFlubitOrderId() . '" has been sent for cancelled.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'cancelOrder', 1, 0, $feedid);

            } else {

                Mage::log('Order Cancel Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

				Mage::log('Order Cancel Request' . $request, null, Flubit_Flubit_Helper_Data::FLUBIT_CANCEL_ORDER);

                $request = 'Flubit order ID : "' . $order->getFlubitOrderId() . '" has been sent for cancelled.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'cancelOrder', 0, 1, $feedid);

            }

        }

		}

			

		

    }

 



    /**

     * 

     * Method for Refund Flubit Orders

     */

    public function refundFlubitOrders($flubitOrderId=0) {



        $flubit = $this->_initModule();

		if($flubitOrderId != 0) {

			$response = $flubit->refundOrderByFlubitId($flubitOrderId, 'Order Refunded');

            $xmlObjRes = simplexml_load_string($response);

            if ($xmlObjRes->getName() == 'success') {

                $flubitorder = Mage::getModel('flubit/order')->load($order->getFlubitId());

                if ($flubitorder->getId()) {

				Mage::log($flubitorder->getId(), null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                    $flubitorder->setRefund('1')

                            ->save();

                }

                //Mage::log('Order Dispatched for flubit ID:' . $order->getFlubitOrderId(), null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                Mage::log('Order Refunded Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for refund.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'refundOrder', 1, 0, $feedid);

            } else if ($xmlObjRes->getName() == 'error') {

                $flubitorder = Mage::getModel('flubit/order')->load($order->getFlubitId());

                if ($flubitorder->getId()) {

                    $flubitorder->setRefund('1')

                    ->save();

                }

                Mage::log('Order Refunded Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for refund.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'refundOrder', 0, 1, $feedid);

            } else {

                Mage::log('Order Refunded Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for refund.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'refundOrder', 0, 1, $feedid);

            }

		} else {

        $collection = Mage::getModel('sales/order')->getCollection();

        $collection->addFieldToFilter('main_table.status', 'closed');

        $collection->addFieldToSelect('flubit_order_id');

        $collection->addFieldToSelect('created_at');

        $collection->getSelect()->join(array('flubit_order' => Mage::getConfig()->getTablePrefix() . 'flubit_order'), 'flubit_order.order_no = main_table.increment_id  and flubit_order.dispatch = 1 and flubit_order.refund = 0 ', array('flubit_order.flubit_order_id', 'flubit_order.flubit_id'));



        foreach ($collection as $order) {

            $response = $flubit->refundOrderByFlubitId($order->getFlubitOrderId(), 'Order Refunded');

            $xmlObjRes = simplexml_load_string($response);

            if ($xmlObjRes->getName() == 'success') {

                $flubitorder = Mage::getModel('flubit/order')->load($order->getFlubitId());

                if ($flubitorder->getId()) {

				Mage::log($flubitorder->getId(), null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                    $flubitorder->setRefund('1')

                            ->save();

                }

                //Mage::log('Order Dispatched for flubit ID:' . $order->getFlubitOrderId(), null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                Mage::log('Order Refunded Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for refund.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'refundOrder', 1, 0, $feedid);

            } else if ($xmlObjRes->getName() == 'error') {

                $flubitorder = Mage::getModel('flubit/order')->load($order->getFlubitId());

                if ($flubitorder->getId()) {

                    $flubitorder->setRefund('1')

                    ->save();

                }

                Mage::log('Order Refunded Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for refund.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'refundOrder', 0, 1, $feedid);

            } else {

                Mage::log('Order Refunded Response' . $response, null, Flubit_Flubit_Helper_Data::FLUBIT_REFUND_ORDER);

                $request = 'Flubit order ID : <b>"' . $order->getFlubitOrderId() . '"</b> has been sent for refund.';

                $feedid = 'Flubit order ID : "' . $order->getFlubitOrderId() . '"';

                $this->logFlubitOrdersRequestResponse($request, $response, 'refundOrder', 0, 1, $feedid);

            }

        }

		}

    }



    /**

     * 

     * @param integer $country_id

     * @param integer $state

     * @return array

     */

    public function getRegionCode($country_id = null, $state = null) {



        $collection = Mage::getModel('directory/country')->getResourceCollection()->loadByStore();



        return $collection;

    }



    /**

     * Method to log fubit orders process

     * 

     * 

     */

    function logFlubitOrdersRequestResponse($request, $response, $mode, $parseResponse = 0, $error = 0, $ForderId = '') {

        

        $level = 1;



        if ($error == 1)

            $level = 2;



        if ($mode == 'fetchOrder') {

            $type = 'Fetch Order';

        } else if ($mode == 'dispatchOrder') {

            $type = 'Dispatch Order';

        } else if ($mode == 'refundOrder') {

            $type = 'Refund Order';

        } else if ($mode == 'cancelOrder') {

            $type = 'Cancel Order';

        } else if ($mode == 'insertOrder') {

            $type = 'Order in Magento';

        } else if ($mode == 'deleteProduct') {

            $type = 'Delete Product';

        } else if ($mode == 'failedOrder') {

            $type = 'Order Failed';

        }
        


        $action = array(

            'fetchOrder' => 3,

            'dispatchOrder' => 4,

            'refundOrder' => 5,

            'cancelOrder' => 6,

            'insertOrder' => 7,

            'deleteProduct' => 9,

            'failedOrder' => 11

        );



        if ((is_array($request)) || (is_object($request))) {

            $request = print_r($request, TRUE);

        }

        if ((is_array($response)) || (is_object($response))) {

            $response = print_r($response, TRUE);

        }



        if ($parseResponse == 1) {

            $xml = simplexml_load_string($response);

            //$xml = new SimpleXMLElement($response);

            Mage::log('Xml Parse :' . $xml->asXML(), null, 'xmlparse.log');

            if ($xml->getName() == 'error') {

                $level = 2;

            } else if (($xml->getName() == 'success')) {

                $level = 1;

            }

        }

        //Mage::log('Xml :' .$response , null, 'xmlparse-resp.log');

        // if ($ForderId != '')

        //   $ForderId = 'Order ID ' . $ForderId;



        if (($request != '') && ($response != '')) {

            try {

                $flubitlog = Mage::getModel('flubitlog/flubitlog');

                $flubitlog->setData(

                                array(

                                    'request_xml' => $request,

                                    'feedid' => $ForderId,

                                    'response_xml' => $response,

                                    'action' => $action[$mode],

                                    'datetime' => date('Y-m-d H:i:s'),

                                    'level' => $level

                                )

                        )

                        ->save();

            } catch (Exception $e) {

                Mage::log('Log Saving Exception' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

            }

        }

    }



}