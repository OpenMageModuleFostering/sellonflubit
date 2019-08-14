<?php

/**
 * Class Flubit Model Flubit
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit extends Mage_Core_Model_Abstract {

    const CONSUMER_API = 'flubit_section/flubit_configuration/flubit_consumer_key';
    const SECRET_KEY = 'flubit_section/flubit_configuration/flubit_secret';
    const CONSUMER_URL = 'flubit_section/flubit_configuration/flubit_url';
    const LOG_TYPE = 'flubit_section/flubit_setup/log_type_list';
    const LOGDATE_OLDER = 'flubit_section/flubit_setup/logdate_cron_settings';

    /**
     * Constructor for load Flubit Order
     * 
     */
    protected function _construct() {
        try {
            $this->_init("flubit/flubit");
        } catch (Exception $e) {
            Mage::log('_construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }
	
	
	
    /**
     * Method to reindex products to make sure they are all there
	 * SMB adds
	 * too many of these functions are in the observer and not here so there is a fair bit of duplication till we can rationailse the observer functions
     */
	 
	public function smbDeleteProduct($sku) {
		
			$flubit = Mage::getModel('flubit/flubit')->load($sku, 'sku');
            if ($flubit->getId() == '') { } else { // only delete items that exist!
						Mage::log($sku.' was deleted' , null, 'flubit_smb.txt');
            $flubit ->setIsDeleted('1')
                    ->save();
		
			$config = Mage::getModel('flubit/config');
			$data = $config->inactiveProduct($sku, 0);
			$xml = $config->getproductFeedStatus($data,'xml');

            if (!isset($xml)) {
                Mage::log("Product is not deleted from flubit SKU :" . $sku , null, Flubit_Flubit_Helper_Data::FLUBIT_OBSERVER_DELETE);
            } else {
                $data = Mage::getModel('flubit/flubit')->load($sku, 'sku');
                $data->delete()->save();
            }
			}
	}
	 
    public function smbIndexProducts() {
		
		
		$returnString ='';
        
			
		Mage::log('started' , null, 'flubit_smb.txt');
		
		// we only really need entity_id
		$myCollection = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToSelect('entity_id');
			
		foreach ($myCollection as $_product) {
			try {
				$productId = $_product->getId();
                $product = Mage::getModel('catalog/product')->load($productId);
				
            	$status_stat = $product->getStatus(); // check if the product is active or inactive in magento 
				$stock_status = $product->getStockItem()->getIsInStock();
				$sku = $product->getSku();
				$productName = $product->getName();
            	$qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

				if (($status_stat == 1) && ($stock_status) && ($qty > 0)) {$status = 1;} else {$status = 0;}
				
				$flubit = Mage::getModel('flubit/flubit')->load($sku,'sku');
            	$flubitObj = Mage::getModel('flubit/flubit');
				
                if ($product->getFlubitProduct() != '1') { // not a flubit product
						$this->smbDeleteProduct($sku);
                } else { // we have a flubit product
					// this probably should be its own function
            		if ($flubit->getId() == '') { // new flubit product
					$new = 1;
						//Mage::log($sku.' was added' , null, 'flubit_smb.txt');
						 } else { // existing flubit product 
					$new = 0;
						//Mage::log($sku.' exists and will be ignored' , null, 'flubit_smb.txt');
						continue;// we are only going to add new items for speed at the moment
            		}
					
					if (($this->checkEanValidation($product) && $this->checkImageValidation($product))) {
						
						if ($new > 0) {
					// only add to flubit if it passes validation speeds up routine
                		$flubit->setName($productName)
                        ->setSku($sku)
                        ->setPrice(0)
                        ->setQty($qty)
                        ->setStatus('1')
						->setActiveStatus($status)
                        ->setNew($new)
                        ->save();
						} else {
						// we only want to update qty
                		$flubit->setName($productName)
                        ->setQty($qty)
                        ->save();
							
						}
					// update flubit price unsure why its not above		
					try {
            			if (is_object($flubit)) {
                			$flubitPrice = (number_format($flubitObj->getFlubitPrice($product), 2, '.', ''));
                			$flubit->setPrice($flubitPrice)
                     		   		->save();
            			}

        			} catch (Exception $e) {
            		Mage::log(__LINE__ . 'smbIndexProducts' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
					}
										} else {
					// if they failed validation lets remove them - they can be readded later
						$this->smbDeleteProduct($sku);
					}

				}
        	} catch (Exception $e) {
            Mage::log('smbIndexProducts ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        	}
        }
			
		
		Mage::log('finished' , null, 'flubit_smb.txt');
		$returnString ='Products Successfully reindexed';
        return $returnString;
	}

    /**
     * Method to get Flubit Orders
     */
    public function getFlubitOrders() {
        try {
            $config = Mage::getModel('flubit/config');
            $data = $config->getFlubitOrders();
        } catch (Exception $e) {
            Mage::log('getFlubitOrders ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Process Flubit Orders
     */
    public function processFlubitOrders() {
        try {
            $this->refundFlubitOrders();
        } catch (Exception $e) {
            Mage::log('processFlubitOrders ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Dispatch Flubit Orders
     */
    public function dispatchFlubitOrders() {
        try {
            $config = Mage::getModel('flubit/config');
            $data = $config->dispatchFlubitOrders();
        } catch (Exception $e) {
            Mage::log('dispatchFlubitOrders ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Cancel Flubit Orders
     */
    public function cancelFlubitOrders() {
        try {
            $config = Mage::getModel('flubit/config');
            $data = $config->cancelFlubitOrders();
        } catch (Exception $e) {
            Mage::log('cancelFlubitOrders ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Refund Flubit Orders
     */
    public function refundFlubitOrders() {
        try {
            $config = Mage::getModel('flubit/config');
            $data = $config->refundFlubitOrders();
        } catch (Exception $e) {
            Mage::log('refundFlubitOrders ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Send Product Feed
     */
    public function sendProductFeed() {
        try {
            $this->_updateFlubitPrices();
            $this->_updateFlubitQty();
            $this->generateProductXML();
        } catch (Exception $e) {
            Mage::log('sendProductFeed ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to delete log files and flubitlog table
     */
    public function archiveLog() {
        //Mage::log('archive',null,'hum.log');
		//$olderthen = Mage::getStoreConfig(self::LOGDATE_OLDER);
        $logbefore = date('Y-m-d h:i:s', strtotime('-' . 30 . ' day'));
        $flubitlogs = Mage::getModel('flubitlog/flubitlog')->getCollection();
        $flubitlogs->addFieldToFilter('datetime', array('lteq' => $logbefore));
        $flubitlogs->load();
        foreach ($flubitlogs as $flubitlog) {
            $flubitlog->delete();
        }
    }

    /**
     * Method to Generate Product Xml
     */
    public function generateProductXML() { // generate product xml and send to flubit
        $configure = $this->checkConfiguration();
        if ($configure->error == 'true' && $configure->right == 'false') {
            Mage::throwException('Missing Keys in configuration');
        }
        // end checking of configuration
        $this->_checkPendingFeedStatus();

        // get the configuration from the file
        $chunkSize = 10; // define if chunk size is not defined
        $errorString = ''; // define blank error string
        $returnString = '';
        $errorFeed = '';
        $productCount = 0;
        $error = FALSE;
        try {
            $chunkSize = Mage::getStoreConfig('flubit_section/flubit_setup/flubit_chunk');
        } catch (Exception $e) {
            Mage::log('Could not retrive chunk size ' . $e, NULL, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
        $allSku = array();

        // get flubit collection for the products which are needed to be pushed
        try {
            $flubit_products = Mage::getModel('flubit/flubit')->getCollection()
                    ->addFieldToFilter('status', '1')
                    ->addFieldToFilter('is_deleted', '0')
                    ->setOrder('flubit_id', 'ASC')
                    ->setPageSize($chunkSize);

            if (is_object($flubit_products)) {
                if (count($flubit_products) > 0) {

                    $create_feed_prodcut_xml = ''; // initialise the new product xml
                    $update_feed_prodcut_xml = ''; // initialise the update product xml

                    $store = Mage::app()->getStore(); // get deafult store
                    $tax_calculation_obj = Mage::getModel('tax/calculation')->getRateRequest(null, null, null, $store);

                    $newXML = '';
                    $updateXML = '';

					
                    foreach ($flubit_products as $flubit_Prodcut) {
                        // get magento product id for the given sku 
                        if ($flubit_Prodcut->getSku() == '') { // we cannot proceed further if sku is missing
                            Mage::log('Missing SKU for flubit product ', NULL, Flubit_Flubit_Helper_Data::FLUBIT_CREATE_PRODUCT);
                            // save the status to zero so that its called next time only once its updated from backend
                            $flubit_Prodcut->setStatus('0')
                                    ->save();
                            continue;
                        }
                        $product_id = Mage::getModel('catalog/product')->getIdBySku(trim($flubit_Prodcut->getSku()));
                        $product = Mage::getModel('catalog/product')->load($product_id);

                        // check if the prodcut exist in magento 
                        if (!$product->getId()) {
                            Mage::log('Sku ' . $product->getSku() . ' does not found in Magento.', null, Flubit_Flubit_Helper_Data::FLUBIT_FAILED_PRODUCT);
                            $flubit_Prodcut->setStatus('0')
                                    ->save();
                            continue;
                        }
                        // if there is no EAN, ASIN, ISBN or MPN we can't push it to Flubit
                        if (!$this->checkEanValidation($product)) {
                            if (is_object($product)) {

                                $errorString .= '<tr>
                                                <td style="padding:4px;">' . $product->getName() . '</td>
                                                <td style="padding:4px;">' . $product->getSku() . ' </td>
                                                <td style="padding:4px;"> ' . 'Missing Identifiers. ' . '</td>
                                                </tr>';

                                $response = 'Product SKU : <b>"' . $product->getSku() . '"</b> identifiers is blank. Please update the data where appropriate and send again. ';
                                $feedid = 'Product identifiers validation failed.';

                                $this->logFlubitProductsRequestResponse('', $response, 'Create Product', $feedid);

                                Mage::log($product->getSku() . ' missing identifiers', null, Flubit_Flubit_Helper_Data::FLUBIT_FAILED_PRODUCT);
                                // save the status to zero so that its called next time only once its updated from backend
                                $flubit_Prodcut->setStatus('0')
                                        ->save();
                            } else {
                                Mage::log('Product was deleted from Magento SKU :' . $product->getSku(), null, Flubit_Flubit_Helper_Data::FLUBIT_FAILED_PRODUCT);
                            }
                            continue;
                        }
                        // if there is no image we can't push it to Flubit
                        if (!$this->checkImageValidation($product)) {

                            $errorString .= '<tr>
                                        <td style="padding:4px;">' . $product->getName() . '</td>
                                        <td style="padding:4px;">' . $product->getSku() . ' </td>
                                        <td style="padding:4px;"> ' . 'No image is uploaded, so cannot be pushed to Flubit' . ' </td>
                                    </tr>';

                            $response = 'Product SKU <b>"' . $product->getSku() . '"</b> image is not available. Please update the data where appropriate and send again. ';
                            $feedid = 'Product image validation failed.';
                            $this->logFlubitProductsRequestResponse('', $response, 'Create Product', $feedid);
                            Mage::log($product->getSku() . ' Image is not available. please update and send again.', null, Flubit_Flubit_Helper_Data::FLUBIT_FAILED_PRODUCT);
                            //the product has not been pushed and so should be treated as new
                            // save the status to zero so that its called next time only once its updated from backend
                            $flubit_Prodcut->setStatus('0')
                                    ->save();
                            continue;
                        }


                        $flubit_stat = $product->getFlubitProduct(); // if product flubit stat is true
                        $status_stat = $product->getStatus(); // check if the product is active or inactive in magento 
                        $stock = $product->getStockItem();
                        //Mage::log($stock->getData(), null, 'test.log');
                        $stock_status = $stock->getIsInStock();

                        $qtyStock = 0;
                        try {
                            $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                            $taxclassid = $product->getData('tax_class_id');
                            $percent = Mage::getModel('tax/calculation')->getRate($tax_calculation_obj->setProductClassId($taxclassid));
                        } catch (Exception $e) {
                            Mage::log(__LINE__ . ' Exception getting cataloginventory/stock_item tax/calculation  Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                        }

                        if ((floor($qtyStock) != $qtyStock)) { // check if the quantity is decimal
                            $errorString .= '<tr>
                            <td style="padding:4px;">' . $product->getName() . '</td>
                            <td style="padding:4px;">' . $product->getSku() . ' </td>
                            <td style="padding:4px;"> ' . 'Quantity is in decimal. Product cannot be pushed to Flubit.' . ' </td>
                            </tr>';

                            $response = 'Product SKU <b>"' . $product->getSku() . '"</b> quantity is in decimal. Please Insert it in integer only.';
                            $feedid = 'Product quantity validation failed.';
                            $this->logFlubitProductsRequestResponse('', $response, 'Create Product', $feedid);

                            Mage::log('quantity is in decimal = ' . $qtyStock, null, 'flubit_create_product.log');
                            $flubit_Prodcut->setStatus('0')
                                    ->save();
                            continue;
                        }

                        if (($flubit_stat == 1) && ($status_stat == 1) && ($stock_status) && ($qtyStock > 0)) {
                            $status = 'true';
                        } else {
                            $status = 'false';
                        }

                        if ($flubit_Prodcut->getNew() == '1') {
                            if ($product->getSku() != '') { // check if prodcut SKU is not null
                                $newXML .= '<product sku="' . $product->getSku() . '">';
                                $newXML .= '<title><![CDATA[' . $product->getName() . ']]></title>';
                                $newXML .= "<is_active>$status</is_active>";
                                $newXML .= '<base_price>' . (number_format($flubit_Prodcut->getPrice(), 2, '.', '')) . '</base_price>';
                                $newXML .= '<stock>' . number_format((int) $qtyStock, 0, '.', '') . '</stock>';
                                $newXML .= '<description><![CDATA[' . $this->removeNonUtfCharacters($product->getDescription()) . ']]></description>';

                                if ($product->getImage() != 'no_selection') {
                                    $newXML .= '<images>';
                                    $image_path = Mage::helper('catalog/image')->init($product, 'image');
                                    $newXML .= '<image><![CDATA[' . $image_path . ']]></image>';
                                    $newXML .= '   </images>';
                                }
                                if ($product->getFlubitEan() || $product->getFlubitAsin() || $product->getFlubitIsbn() || $product->getFlubitMpn()) {
                                    $newXML .= '<identifiers>';
                                    if ($product->getFlubitEan())
                                        $newXML .= '<identifier type="EAN">' . $product->getFlubitEan() . '</identifier>';
                                    if ($product->getFlubitAsin())
                                        $newXML .= '<identifier type="ASIN">' . $product->getFlubitAsin() . '</identifier>';
                                    if ($product->getFlubitIsbn())
                                        $newXML .= '<identifier type="ISBN">' . $product->getFlubitIsbn() . '</identifier>';
                                    if ($product->getFlubitMpn())
                                        $newXML .= '<identifier type="MPN">' . $product->getFlubitMpn() . '</identifier>';
                                    $newXML .= '</identifiers>';
                                }
                                if ($product->getFlubitBrand())
                                    $newXML .= '<brand>' . $product->getFlubitBrand() . '</brand>';
                                if ($product->getFlubitStandardDelivery() || $product->getFlubitExpressDelivery()) {
                                    $newXML .= '<delivery_cost>';
                                    if ($product->getFlubitStandardDelivery())
                                        $newXML .= '<standard>' . number_format($product->getFlubitStandardDelivery(), 2, '.', '') . '</standard>';
                                    if ($product->getFlubitExpressDelivery())
                                        $newXML .= '<express>' . number_format($product->getFlubitExpressDelivery(), 2, '.', '') . '</express>';
                                    $newXML .= '</delivery_cost>';
                                }
                                if ($product->getWeight())
                                    $newXML .= '<weight>' . number_format($product->getWeight(), 1, '.', '') . '</weight>';
                                if ($percent)
                                    $newXML .= '<tax_rate>' . number_format($percent, 2, '.', '') . '</tax_rate>';
                                $newXML .= '</product>';
                            }
                        } else {
                            $updateXML .= '<product sku="' . $product->getSku() . '">';
                            $updateXML .= "<is_active>$status</is_active>";
                            $updateXML .= '<base_price>' . (number_format($flubit_Prodcut->getPrice(), 2, '.', '')) . '</base_price>';
                            $updateXML .= '<stock>' . number_format($qtyStock, 0, '.', '') . '</stock>';
                            $updateXML .= '</product>';
                        }

                        // set the flubit product status in flubit

                        try {
                            $flubitActiveStatus = '0';
                            if ($status == 'true')
                                $flubitActiveStatus = '1';
                            if ($flubit_Prodcut->getActiveStatus() != $flubitActiveStatus) {
                                //$flubit_Prodcut->setData(array('flubit_id' => $flubit_Prodcut->getId(), 'active_status' => $flubitActiveStatus))
                                $flubit_Prodcut->setActiveStatus($flubitActiveStatus)
                                        ->save();
                            }
                        } catch (Exception $e) {
                            Mage::log(__LINE__ . 'Exception Savingt flubit/flubit Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                        }
                        $allSku[] = $product->getSku();
                        //Mage::log('Prodcut ' . print_r($product->getData(), TRUE ) , NULL, 'optimise.log');
                    }
                    // end for each of product list generation

                    if ($newXML != '') {
                        $create_feed_prodcut_xml = '<?xml version="1.0" encoding="UTF-8"?><products>' . $newXML . '</products>';
                    }
                    if ($updateXML != '') {
                        $update_feed_prodcut_xml = '<?xml version="1.0" encoding="UTF-8"?><products>' . $updateXML . '</products>';
                    }
                    //Mage::log($create_feed_prodcut_xml, NULL, 'CreateProduct.xml');
                    //Mage::log($update_feed_prodcut_xml, NULL, 'UpdateProduct.xml');
                    //Mage::log(print_r($allSku, TRUE), NULL, 'allSku.xml');
                    // preparing error String to be displayed  on press of sync button
                    $productCount = count($allSku);

                    if ($errorString != '') {
                        $errorString = '<div>Following product(s) has failed.</div><br/>
                                <style type="text/css">
                                #tableOuter1{
                                        border-style: solid;
                                        border-width: 1px;
                                        border-collapse: collapse;
                                        margin: 0;
                                        padding:4;
                                        width:100%;
                                    } 

                                    #tableOuter1 th,#tableOuter1 td{
                                        border-style: solid;
                                        border-width: 0 1px 1px 0;
                                        border-collapse: collapse;
                                        margin: 0;
                                        padding:4;
                                    } 
                                </style>
                                        <table id="tableOuter1" cellspacing="0" cellpadding="0">
                                        <tr>
                                                <th width="250"><strong>Product Name</strong></th>
                                                <th width="250"><strong>Product SKU</strong></th>
                                                <th width="250"><strong>Error Message</strong></th>
                                        </tr>
                                        ' . $errorString . '
                                        </table>';
                    }
                    $failed_sku_create = array();
                    $failed_sku_update = array();
                    $createFailedCount = 0;
                    $updateFailedCount = 0;
                    $feedError = '';
                    $feedStatus = '';
                    $feedMode = '';
                    $feedResponseXML = '';
                    // get model of config
                    $config = Mage::getModel('flubit/config');
                    // send the prodcuct create feed to flubit and save the id returned to log
                    if ($create_feed_prodcut_xml != '') { // if create product feed is not null send to flubit
                        $create_result = $config->createFlubitProducts($create_feed_prodcut_xml);
                        if ($create_result != '') {
                            Mage::log($create_result, NULL, 'createfeed.log');
                            if (is_object($create_result)) {
                                if ($create_result->getName() == 'id') {
                                    $this->feedLog($create_result, 'Create');
                                    $returnString .= 'Created Feed ID : ' . (String) $create_result . '<br/>';
                                    // get the feed response if its a create feed
                                    $feedCheckResponseCreate = $this->getFeedErrors($create_result);

                                    $createFailedCount = $feedCheckResponseCreate->failedCount;
                                    $errorString .= $feedCheckResponseCreate->html;
                                    $failed_sku_create = $feedCheckResponseCreate->sku;
                                    $feedError .= $feedCheckResponseCreate->error;
                                    $feedResponseCreateXML = $feedCheckResponseCreate->responsexml;

                                    $this->logFlubitProductsRequestResponse($create_feed_prodcut_xml, $feedResponseCreateXML, 'Create Product', $create_result);
                                    // if feedresponse is fetched and is a success mark it to db as fetched
                                    $feedResponseCreateXML = simplexml_load_string($feedResponseCreateXML);
                                    if ($feedResponseCreateXML['status'] == 'processed') {
                                        if ($feedResponseCreateXML->results->errors->total == 0) {
                                            $this->feedResponseFetchedMarkToDb($create_result);
                                        }
                                    }
                                }
                            }
                        } else {
                            Mage::throwException('Response not received from flubit for create product request.');
                        }
                    }
                    // send the prodcuct uppdate feed to flubit and save the id returned to log
                    if ($update_feed_prodcut_xml != '') { // if create product feed is not null send to flubit
                        $update_result = $config->updateFlubitProducts($update_feed_prodcut_xml);
                        if ($update_result != '') {
                            Mage::log(print_r($update_result, true), NULL, 'updatefeed.log');
                            if (is_object($update_result)) {
                                if ($update_result->getName() == 'id') {
                                    $this->feedLog($update_result, 'update');
                                    $returnString .= 'Updated Feed ID : ' . (String) $update_result . '<br/>';
                                    // get the feed response if its a update  feed
                                    $feedCheckResponseUpdate = $this->getFeedErrors($update_result);

                                    $updateFailedCount = $feedCheckResponseUpdate->failedCount;
                                    $errorString .= $feedCheckResponseUpdate->html;
                                    $failed_sku_update = $feedCheckResponseUpdate->sku;
                                    $feedError .= $feedCheckResponseUpdate->error;
                                    $feedResponseupdateXML = $feedCheckResponseUpdate->responsexml;

                                    $this->logFlubitProductsRequestResponse($update_feed_prodcut_xml, $feedResponseupdateXML, 'Update Product', $update_result);
                                    // if feedresponse is fetched and is a success mark it to db as fetched
                                    $feedResponseupdateXML = simplexml_load_string($feedResponseupdateXML);
                                    if ($feedResponseupdateXML['status'] == 'processed') {
                                        if ($feedResponseupdateXML->results->errors->total == 0) {
                                            $this->feedResponseFetchedMarkToDb($update_result);
                                        }
                                    }
                                }
                            }
                        } else {
                            Mage::throwException('Response not received from flubit for update product request.');
                        }
                    }

                    // get differance of failed SKU from create and update
                    $allSku = array_diff($allSku, $failed_sku_create);
                    $allSku = array_diff($allSku, $failed_sku_update);

                    if ($errorFeed == '') {
                        try {
                            $flubits = Mage::getModel('flubit/flubit')->getCollection();
                            $flubits->addFieldToFilter('sku', array('in' => $allSku))
                            ;
                            foreach ($flubits as $flubit) {
                                $flubit
                                        ->setStatus('2')
                                        ->setNew('0')
                                        ->save();
                            }
                        } catch (Exception $e) {
                            Mage::log(__LINE__ . ' Exception Savingt flubit/flubit Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Mage::log('Try getting flubit products data ' . $e, NULL, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }

        if (!$error) {
            if ($productCount != 0) {
                $returnString .= "\n\t<p>Total Products Pushed: " . ($productCount - $createFailedCount - $updateFailedCount ) . '</p>';
                if ($errorString != '')
                    Mage::getSingleton('adminhtml/session')->addError($errorString);
            } else if (($productCount == 0 ) && ($errorFeed != '')) {
                $returnString = "<p>Products not pushed. Please Retry</p>";
            } else {
                $returnString = "<p>There are $productCount products to be pushed to Flubit.</p>";
                if ($errorString != '')
                    Mage::getSingleton('adminhtml/session')->addError($errorString);
            }
        } else {
            Mage::log('Error Occured ' . $error, null, Flubit_Flubit_Helper_Data::FLUBIT_FAILED_PRODUCT);
            Mage::throwException($error);
        }
        return $returnString;
    }

    /**
     * Method to check feed and insert into Logs table
     * @param string
     * @return None
     */
    public function feedLog($feedId, $type) {
        if (($feedId != '') && ($type != '')) {
            try {
                $feedModel = Mage::getModel('flubit/logs');
                $feedModel->setFeedType($type)
                        ->setFeedId($feedId)
                        ->setStatus('0')
                        ->setCreatedAt(date('Y-m-d H:i:s'))
                        ->save();
            } catch (Exception $e) {
                Mage::log(__LINE__ . ' Exception Flubit Feed Log ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
            }
        }
    }

    /**
     * Method to check server side validation for blank check key & secret
     * @param string
     * @return boolean
     */
    public function checkConfiguration() {
        $return = new stdClass();
        $return->right = 'false';
        $return->error = 'false';

        try {
            $api = Mage::getStoreConfig(self::CONSUMER_API);
            $key = Mage::getStoreConfig(self::SECRET_KEY);
            $url = Mage::getStoreConfig(self::CONSUMER_URL);

            if (!empty($api) && !empty($key) && !empty($url)) {
                $obj = new Flubit_Flubit_Client($api, $key, $url);

                $return->right = 'true';
            } else {
                Mage::log('Missing Keys in configuration', null, Flubit_Flubit_Helper_Data::FLUBIT_MISSING_CONFIG);
                $return->error = 'true';
            }
        } catch (Exception $e) {
            Mage::log('Missing Keys in configuration', null, Flubit_Flubit_Helper_Data::FLUBIT_MISSING_CONFIG);
            $return->error = 'true';
        }
        return $return;
    }

    /**
     * Method to update global price according to configuration
     * @param integer $product
     * @return int
     */
    public function updateFlubitPrice() {
        //Mage::log('price',null,'hum.log');
		try {
            $priceBasedOn = Mage::getStoreConfig('flubit_section/flubit_setup/price_based_on');
            $globalPrice = Mage::getStoreConfig('flubit_section/flubit_setup/global_price');
            $flubitCollection = Mage::getModel('flubit/flubit')->getCollection()
                    ->addfieldtofilter('use_global_price', '1')
                    ->addfieldtofilter('global_price_update', '0')
                    ->addFieldToSelect('sku')
                    ->addFieldToSelect('flubit_id')
                    ->addFieldToSelect('price')
                    ->setOrder('flubit_id', 'DESC')
                    ->setPageSize('200');
            //Mage::log($flubitCollection->getData(), null, 'hum.log');
            foreach ($flubitCollection as $flubit) {
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $flubit->getSku());
                if (is_object($product)) {
                    if ($priceBasedOn) {
                        $priceOfProduct = $product->getData($priceBasedOn); // Get price based on selected price
                    } else {
                        $priceOfProduct = $product->getPrice($priceBasedOn); // get default magento price
                    }

                    $flubitPrice = $priceOfProduct * $globalPrice;
                    $flubitPrice = number_format($flubitPrice, 2, '.', '');

                    if ($flubitPrice != $flubit->getPrice()) {
                        //updating the price in the main table
                        $flubit->setPrice($flubitPrice)
                                ->setGlobalPriceUpdate('1')
                                ->setStatus('1')
                                ->save();
                    }
                } // product object check
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception get flubit price Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
        return 0;
    }

    /**
     * Method to get Flubit Price
     * @param integer $product
     * @return int
     */
    public function getFlubitPrice($product) {
        try {
            $priceBasedOn = Mage::getStoreConfig('flubit_section/flubit_setup/price_based_on');
            $globalPrice = Mage::getStoreConfig('flubit_section/flubit_setup/global_price');

            if (is_object($product)) {

                $flubitProduct = Mage::getModel('flubit/flubit')->getcollection()->addfieldtofilter('sku', $product->getSku());
                $flubitPriceUseGlobal = 0; // set by default
                foreach ($flubitProduct as $flubit) {
                    $flubitPriceUseGlobal = $flubit->getUseGlobalPrice();
                }
                // if global price is zero than calculate with the given conditions
                if (($product->getFlubitBasePrice() != 0.00) && ($flubitPriceUseGlobal == 0))
                    return $product->getFlubitBasePrice(); // return default flubit price
                else if ($priceBasedOn)
                    $priceBasedOn = $product->getData($priceBasedOn); // Get price based on selected price
                else {
                    $priceBasedOn = $product->getPrice($priceBasedOn); // get default magento price
                }
                $flubitPrice = $priceBasedOn * $globalPrice;
                $flubitPrice = number_format($flubitPrice, 2, '.', '');
                //$product->setFlubitBasePrice($flubitPrice)->save();
                if ($flubitPriceUseGlobal == 0) {
                    if (is_object($flubit)) {
                        $flubit->setUseGlobalPrice(1)
                                ->save();
                    }
                }
                return $flubitPrice;
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception get flubit price Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
        return 0;
    }

    /**
     * Method to Update Flubit Price
     */
    private function _updateFlubitPrices() {
        try {
            $flubitCollection = Mage::getModel('flubit/flubit')->getCollection();

            foreach ($flubitCollection as $flubit) {
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $flubit->getSku());
                if (is_object($product)) {
                    $flubitPrice = $this->getFlubitPrice($product);

                    if (($flubitPrice != $flubit->getPrice()) || ($product->getFlubitBasePrice() != $flubitPrice)) {
                        try {
                            //updating the price in the main table
                            $flubit->setPrice($flubitPrice)
                                    ->setStatus('1')
                                    ->save();
                            //updating the product attribute flubit base price
                            $product->setFlubitBasePrice($flubitPrice)
                                    ->save();
                        } catch (Exception $e) {
                            Mage::log('Update Flubit Price Exception ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception updateFlubitPrices  Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Update Flubit Quantity
     */
    private function _updateFlubitQty() {
        try {
            $flubitCollection = Mage::getModel('flubit/flubit')->getCollection();

            foreach ($flubitCollection as $flubit) {
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $flubit->getSku());
                $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

                if ($qty != (int) $flubit->getQty()) {
                    //updating the Qty in the main table
                    $flubit->setQty($qty)
                            ->setStatus('1')
                            ->save();
                }
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception _updateFlubitQty   ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Check Ean, Asin, Isbn, Mpn
     * 
     * @param String $pr
     * @return boolean
     */
    public function checkEanValidation($pr) {
        try {
            if (is_object($pr)) {
                if ($pr->getFlubitEan() || $pr->getFlubitAsin() || $pr->getFlubitIsbn() || $pr->getFlubitMpn()) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception checkEanValidation ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Check Image not Blank
     * 
     * @param String $pr
     * @return boolean
     */
    public function checkImageValidation($pr) {
        if ($pr->getImage() != 'no_selection') {
            try {
                $image_path = Mage::helper('catalog/image')->init($pr, 'image');
                if ($image_path != '') {
                    return true;
                }
            } catch (Exception $e) {
                Mage::log(__LINE__ . ' Exception Image Validation ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
            }
        }
        return false;
    }

    /**
     * Method for Check Image not Blank
     * 
     * @param String $pr
     * @return boolean
     */
    public function checkQuantityValidation($pr) {
        //Mage::log($pr->getQty() , null, flubit_create_product.log);
        if (is_int($pr->getQty())) {
            return true;
        }
        return false;
    }

    /**
     * Method to remove non utf character
     * 
     * @param String $Str
     * @return String
     */
    public function removeNonUtfCharacters($Str) {
        try {
            $StrArr = STR_SPLIT($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ORD($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £ 
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception removeNonUtfCharacters ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
        return $NewStr;
    }

    /**
     * Method to check the status of the pushed feed
     * 
     * @param SimpleXmlObject
     * @param String mode 
     * @return Object
     */
    public function getFeedErrors($xmlObj) {
        try {
            //Mage::log('Check For feed id ' . $xmlObj, null, 'Test_Feed.log');
            //sleep(5);
            $return = new stdClass();
            $return->failedCount = 0;
            $return->html = '';
            $return->sku = array();
            $return->error = '';
            $return->status = '';
            $return->mode = '';
            $return->responsexml = '';

            try {
                $config = Mage::getModel('flubit/config');
                $xmlObjRes = $config->getproductFeedStatus($xmlObj, 'xml');
                $return->responsexml = $xmlObjRes;
            } catch (Exception $e) {
                Mage::log('Exception Getting Feed Errors ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
            }

            $xmlObjRes = simplexml_load_string($xmlObjRes);
            if (is_object($xmlObjRes)) {

                if ($xmlObjRes->getName() == 'error') {
                    $errorCode = $xmlObjRes['code'];
                    $return->error = $xmlObjRes['message'];
                    $this->feedResponseFetchedMarkToDb($xmlObj); // feed response recived mark in database
                    Mage::log('Error: Code ' . $errorCode . ' Error Message : ' . $return->error, null, Flubit_Flubit_Helper_Data::FLUBIT_FEED);
                } else if ($xmlObjRes->getName() == 'feed') {
                    if ($xmlObjRes['status'] == 'invalid') {
                        $return->error = 'Invalid Feed Sent to flubit';
                        $this->feedResponseFetchedMarkToDb($xmlObj); // feed response recived mark in database
                    }

                    if ($xmlObjRes['status'] == 'processing') {
                        Mage::log('Feed ID is Under Processing : ' . $xmlObj, null, Flubit_Flubit_Helper_Data::FLUBIT_FEED);
                    } else if ($xmlObjRes['status'] == 'processed') {
                        //Mage::log('hello afetr created check : ' . ' Feed Id = ' . $xmlObj   , null, 'temptest.log' ); 
                        if (isset($xmlObjRes->results->created)) {
                            //Mage::log('afetr created check : ' . ' Feed Id = ' . $xmlObj   , null, 'temptest.log' ); 
                            $return->mode = 'created';
                            $this->feedResponseFetchedMarkToDb($xmlObj); // feed response recived mark in database
                            //Mage::log('After Create mark db : ' . ' Feed Id = ' . $xmlObj   , null, 'temptest.log' ); 
                            if (isset($xmlObjRes->results->errors->total)) {
                                if ($xmlObjRes->results->errors->total > 0) {
                                    $return->failedCount = $xmlObjRes->results->errors->total;
                                    $innerTable = '';
                                    $productAlreadyExist = array();
                                    foreach ($xmlObjRes->results->errors->sample->error as $error) {
                                        $productName = '';
                                        try {
                                            $productNameObj = Mage::getModel('catalog/product')->loadByAttribute('sku', $error['sku']);
                                            if (is_object($productNameObj))
                                                $productName = $productNameObj->getName();
                                            else {
                                                Mage::log('Unable to get product Name for SKU: ' . $error['sku'], null, Flubit_Flubit_Helper_Data::FLUBIT_FEED);
                                            }
                                        } catch (Exception $e) {
                                            Mage::log('Unable to get name of product Get Feed Errors ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                                        }
                                        if ($error == 'Product already exists.') {
                                            $productAlreadyExist[] = $error['sku'];
                                        } else {
                                            $return->sku[] = $error['sku'];
                                            $innerTable .= '<tr>
                                        <td style="padding:4px;">' . $productName . '</td>
                                        <td style="padding:4px;">' . $error['sku'] . ' </td>
                                        <td style="padding:4px;"> ' . $error . ' </td>
                                    </tr>';
                                        }
                                    }
                                    if ($innerTable != '')
                                        $return->html = '<div>Following product(s) has failed to sync.</div><br/>
                                <style type="text/css">
                                #tableOuter{
                                        border-style: solid;
                                        border-width: 1px;
                                        border-collapse: collapse;
                                        margin: 0;
                                        padding:4;
                                        width:100%;
                                    } 

                                    #tableOuter th,#tableOuter td{
                                        border-style: solid;
                                        border-width: 0 1px 1px 0;
                                        border-collapse: collapse;
                                        margin: 0;
                                        padding:4;
                                    } 
                                </style>
                                        <table id="tableOuter" cellspacing="0" cellpadding="0">
                                        <tr>
                                                <th width="250"><strong>Product Name</strong></th>
                                                <th width="250"><strong>Product SKU</strong></th>
                                                <th width="250"><strong>Error Message</strong></th>
                                        </tr>
                                        ' . $innerTable . '
                                        </table>';
                                    if (count($productAlreadyExist) > 0) {
                                        $this->requeuFailedFeedSku($productAlreadyExist, '');
                                        Mage::log('Update Product to be in create request  :' . print_r($productAlreadyExist, true), null, 'up_error.log');
                                        // since returned error is product already exist in list
                                        //  such that it goen in update Request
                                    }
                                }
                            }
                        } else {
                            //Mage::log('Not Inside created block: ' . ' Feed Id = ' . $xmlObj   , null, 'temptest.log' ); 
                        }
                        if (isset($xmlObjRes->results->updated)) {
                            $return->mode = 'updated';
                            if (isset($xmlObjRes->results->errors->total)) {
                                if ($xmlObjRes->results->errors->total > 0) {
                                    $return->failedCount = $xmlObjRes->results->errors->total;
                                    $UpdateFailedSku = array();
                                    $innerTable = '';
                                    foreach ($xmlObjRes->results->errors->sample->error as $error) {
                                        $productName = '';
                                        try {
                                            $productName = Mage::getModel('catalog/product')->loadByAttribute('sku', $error['sku'])->getName();
                                        } catch (Exception $e) {
                                            Mage::log('Unable to get name of product Get Feed Errors ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                                        }

                                        //$return->sku[] = $error['sku'];

                                        if ($error == 'Product does not exist in cache.') // check if prodcut not in cache exists
                                            $UpdateFailedSku[] = $error['sku'];
                                        else
                                            $return->sku[] = $error['sku'];

                                        //Mage::log('Update Product Error :' . $error , null , 'up_error.log' );

                                        $innerTable .= '<tr>
                                        <td style="padding:4px;">' . $productName . '</td>
                                        <td style="padding:4px;">' . $error['sku'] . ' </td>
                                        <td style="padding:4px;"> ' . $error . ' </td>
                                    </tr>';
                                    }
                                    if ($innerTable != '')
                                        $return->html = '<div>Following product(s) Updates has failed to sync.</div><br/>
                                <style type="text/css">
                                #tableOuter2{
                                        border-style: solid;
                                        border-width: 1px;
                                        border-collapse: collapse;
                                        margin: 0;
                                        padding:4;
                                        width:100%;
                                    } 

                                    #tableOuter2 th,#tableOuter2 td{
                                        border-style: solid;
                                        border-width: 0 1px 1px 0;
                                        border-collapse: collapse;
                                        margin: 0;
                                        padding:4;
                                    } 
                                </style>
                                        <table id="tableOuter2" cellspacing="0" cellpadding="0">
                                        <tr>
                                                <th width="250"><strong>Product Name</strong></th>
                                                <th width="250"><strong>Product SKU</strong></th>
                                                <th width="250"><strong>Error Message</strong></th>
                                        </tr>
                                        ' . $innerTable . '
                                        </table>';
                                    // update products to create new if the returned skus have not in cache error
                                    if (count($UpdateFailedSku) > 0) {
                                        $this->requeuFailedFeedSku($UpdateFailedSku, 'created');
                                        //Mage::log('Update Product to be in create request  :' . print_r($UpdateFailedSku,true) , null , 'up_error.log' );
                                        // since returned error is not exist in cache we need to update product 
                                        //  such that it goen in create request
                                    }
                                }
                            }
                            $this->feedResponseFetchedMarkToDb($xmlObj);
                            // feed response recived mark in database
                            //Mage::log('After update mark db : ' . ' Feed Id = ' . $xmlObj   , null, 'temptest.log' ); 
                        } else {
                            //Mage::log('Not Inside Updated Block : ' . ' Feed Id = ' . $xmlObj   , null, 'temptest.log' ); 
                        }
                    }
                } else {
                    
                }
            }
            return $return;
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception getFeedErrors ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to mark the response of checked feed to DB
     * 
     * @param String
     * @return void
     */
    public function feedResponseFetchedMarkToDb($feedId) {
        //Mage::log('Inside MArk feed response : ' . ' Feed Id = ' . $feedId   , null, 'temptest.log' ); 
        try {

            $logs = Mage::getModel('flubit/logs')->getCollection()
                    ->addFieldToFilter('feed_id', $feedId);

            foreach ($logs as $flb) {
                //Mage::log('Save Feed Status As Fetched : ' . $flb->getId() . ' Feed Id = ' . $feedId   , null, 'temptest.log' );
                $flb->setData(array('flubit_id' => $flb->getId(), 'status' => 1))
                        ->save();
            }
        } catch (Exception $e) {
            Mage::log('Error Saving to Database Flubit Log Exception : ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to check all pending feeds whose status is not fetched
     * 
     * @param void
     * @return void
     */
    public function _checkPendingFeedStatus() {
        try {
            $logs = Mage::getModel('flubit/logs')->getCollection()
                    ->addFieldToFilter('status', 0);
            //Mage::log(print_r($logs, true), null, 'flubit_product_feed.log');
            foreach ($logs as $flb) {
                //Mage::log('Feeds Returned : ' . $flb->getFeedId(), null, 'temptest.log');
                $result = $this->getFeedErrors($flb->getFeedId());
                $this->logFlubitProductsRequestResponse('Request sent to check feed id: <b>"' . $flb->getFeedId() . '"</b>', $result->responsexml, 'Check Feed Response', $flb->getFeedId());
                if ($result->failedCount > 0) {
                    //Mage::log('Failed Count: ' . $result->failedCount . ' Mode = ' . $result->mode . ' SKU' . print_r($result->sku, true), null, 'temptest.log');
                    $this->requeuFailedFeedSku($result->sku, $result->mode);
                }
                //Mage::log('Response : ' . print_r($result,true)  , null, 'temptest.log' );
            }
        } catch (Exception $e) {
            Mage::log('Error Fetching Feeds : ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to requeue the products for create and update
     * 
     * @param array
     * @param String
     * 
     * @return void
     */
    public function requeuFailedFeedSku($sku, $mode) {
        try {
            if (is_array($sku)) {
                Mage::log('Requeue Failed Products: ' . print_r($sku, true), null, 'temptest.log');
                if (count($sku) > 0) {
                    $new = 0;

                    if ($mode == 'created')
                        $new = 1;
                    foreach ($sku as $flsku) {
                        $prod = Mage::getModel('flubit/flubit')->getCollection()
                                ->addFieldToFilter('sku', $flsku);
                        foreach ($prod as $flprod) {
                            try {
                                $flprod->setData(array('flubit_id' => $flprod->getId(), 'status' => 1, 'new' => $new))
                                        ->save();
                                Mage::log('updated logs ' . $flprod->getId() . ' new = ' . $new, null, 'temptest.log');
                            } catch (Exception $e) {
                                Mage::log('Error Flubit product update Exception : ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception getFeedErrors ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method to Error logging the products for create and update and Check Feed Response
     * 
     * @param array
     * @param String
     * 
     * @return void
     */
    function logFlubitProductsRequestResponse($request, $response, $mode, $feedId) {
        
        if ($mode == 'Create Product') {
            $action = 1;
        } else if ($mode == 'Update Product') {
            $action = 2;
        } else if ($mode == 'Check Feed Response') {
            $action = 10;
        }
        if ($request != '') {
            if ($action == 10) {
                $request = $request;
            } else {
                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = FALSE;
                $dom->loadXML($request);
                $dom->formatOutput = TRUE;
                $request = $dom->saveXml();
            }
        }
        $level = 2;

        if ($response != '' && $request != '') {
            $xmlObjResCreate = simplexml_load_string($response);

            if ($xmlObjResCreate->getName() == 'feed') {
                if ($xmlObjResCreate['status'] == 'processing' || $xmlObjResCreate['status'] == 'awaiting_validation' || $xmlObjResCreate['status'] == 'awaiting_processing') {
                    $level = 1;
                }
                if ($xmlObjResCreate['status'] == 'processed') {
                    if ($xmlObjResCreate->results->errors->total == 0) {
                        $level = 1;
                    }
                }
            } //feed if close
        }



        if (($request != '') || ($response != '')) {
            try {
                $flubitlog = Mage::getModel('flubitlog/flubitlog');
                $flubitlog->setData(
                                array(
                                    'request_xml' => $request,
                                    'feedid' => $feedId,
                                    'response_xml' => $response,
                                    'action' => $action,
                                    'datetime' => date('Y-m-d H:i:s'),
                                    'level' => $level
                                )
                        )
                        ->save();
            } catch (Exception $e) {
                
            }
        }
    }

}