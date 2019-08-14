<?php

/**
 * Class Flubit Model Observer
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Observer {

    /**
     * Method to observe save event of magento
     * 
     * @param Varien_Event_Observer $observer
     */
    public function saveOrderAfter(Varien_Event_Observer $observer) {
        try {
            Mage::log('save run saveOrderAfter' , null, 'hum.txt');
            $order = $observer->getOrder();
            $order = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());

            foreach ($order->getAllItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($product->getFlubitProduct() != '1') {
                    continue;
                }
                $this->_saveFlubitProductData($product);
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'saveOrderAfter ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Lock Attributes
     * 
     * @param Varien_Event_Observer $observer
     */
    public function lockAttributes(Varien_Event_Observer $observer) {
        try {
            $product = $observer->getProduct();
            $product->lockAttribute('flubit_base_price');
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'lockAttributes ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Save Flubit Products
     * 
     * @param Varien_Event_Observer $observer
     * @return Boolean
     */
    public function saveFlubitProduct(Varien_Event_Observer $observer) {
        try {
            //Mage::log('save run saveFlubitProduct', null, 'hum.txt');
            $product = $observer->getProduct();
			$flubit = Mage::getModel('flubit/flubit')->load($product->getSku(), 'sku');
			$flubit_stat = $product->getFlubitProduct(); // if product flubit stat is true
			
            if ($flubit->getId() != '') {
            if ($product->getFlubitProduct() == '1') {
			$_product = Mage::getModel('catalog/product')->load($product->getId());
			$status_stat = $_product->getStatus(); // check if the product is active or inactive in magento 
			$stock = $_product->getStockItem();
			$stock_status = $stock->getIsInStock();
			
			$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
			
					if (($status_stat == 1) && ($stock_status) && ($qtyStock > 0)) {
						$status = 1;
					} else {
						$status = 0;
					}
			
                    $flubit->setName($product->getName())
                            ->setStatus('1')
                            ->setActiveStatus($status)
                            ->save();
                } else {
                    $flubit->setName($product->getName())
                            ->setStatus('1')
                            ->setActiveStatus('0')
                            ->save();
                }
                $this->_saveFlubitProductData($product);
                return;
            } else {
                if ($product->getFlubitProduct() == '1') {
                    $this->_saveFlubitProductData($product);
                }
            }
            
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'saveFlubitProduct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }
    
    /**
     * Method for Mass Update Flubit Products
     * 
     * @param Varien_Event_Observer $observer
     * @return Boolean
     */
    public function saveFlubitProductMassAttributeUpdate(Varien_Event_Observer $observer) {
        //Mage::log($observer->getEvent()->getData(),null,'hum.log');
        $attribute_data = $observer->getAttributesData();
        $update_flubit_table = FALSE;
        $flubit_stat = '';
		
		if (isset($attribute_data['flubit_product']))  {
			//Mage::log('flubit_product',null,'hum.log');
			$update_flubit_table = TRUE;
			$flubit_stat = $attribute_data['flubit_product'];
		}
			
		$product_ids = $observer->getProductIds();
        $store_id = $observer->getStoreId();
			
			if($flubit_stat == '') {
			   $update_flubit_table = TRUE;
			}
		
		try {
            if ($update_flubit_table) {
                Mage::log('inside update flubit product');
                foreach ($product_ids as $product_id) {
				if($flubit_stat == '') {
				$flubit_status = Mage::getModel('catalog/product')->load($product_id)->getFlubitProduct();
				} else {
				$flubit_status = $flubit_stat;
				}
				//Mage::log($flubit_status . '--' . $product_id,null,'hum.log');
                $flubitglobal = Mage::getModel('flubit/globalproduct');
                $flubitglobal->setProductId($product_id)
                        ->setFlubitStatus($flubit_status)
                        ->setUpdateStatus('0')
                        ->setCreatedAt(date('Y-m-d H:i:s'))
                        ->save();
                    } 
                }
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'saveFlubitProduct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }
    

    /**
     * Method for Save Flubit Products
     * 
     * @param Xml String $product
     * @return array
     */
    private function _saveFlubitProductData($product) {
	
        try {
            if ($product->getSku() == '') {
                Mage::log($product->getId() . ' no sku', null, 'hum.txt');
                return;
            }
            Mage::log('SKU : ' . $product->getSku() . ' ID '. $product->getId(), null, 'hum.log');
            $_product = Mage::getModel('catalog/product')->load($product->getId());
			
            $status_stat = $_product->getStatus(); // check if the product is active or inactive in magento 
			$stock = $_product->getStockItem();
			$stock_status = $stock->getIsInStock();
			
            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();//$flubitObj->getQty();
				if (($status_stat == 1) && ($stock_status) && ($qty > 0)) {
					$status = 1;
				} else {
					$status = 0;
				}
			$flubit = Mage::getModel('flubit/flubit')->load($product->getSku(), 'sku');
            $flubitObj = Mage::getModel('flubit/flubit');
            //Mage::log(print_r($qty, TRUE), null, 'hum.txt');
            Mage::log('Quantity = ' . $qty, null, 'hum.txt');
            if ($flubit->getId() == '') {
                $flubit = Mage::getModel('flubit/flubit');
                $flubit->setName($product->getName())
                        ->setSku($product->getSku())
                        ->setPrice(0)
                        ->setQty($qty)
                        ->setStatus('1')
						->setActiveStatus($status)
                        ->setNew('1')
                        ->save();
            } else {
                $flubit->setName($product->getName())
                        ->setSku($product->getSku())
                        ->setPrice(0)
                        ->setQty($qty)
                        ->setStatus('1')
						->setActiveStatus($status)
                        ->save();
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . '_saveFlubitProductData ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
        try {
            if (is_object($flubit)) {
                $flubitPrice = (number_format($flubitObj->getFlubitPrice($product), 2, '.', ''));
                $flubit->setPrice($flubitPrice)
                        ->save();
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . '_saveFlubitProductData ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Check Ean, Asin, Isbn, Mpn
     * 
     * @param string $pr
     * @return boolean
     */
    private function checkEanValidation($pr) {
        try {
            if (is_object($pr)) {
                if ($pr->getFlubitEan() || $pr->getFlubitAsin() || $pr->getFlubitIsbn() || $pr->getFlubitMpn()) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'checkEanValidation ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Check Image Available or Not
     * @param String $pr
     * @return boolean
     */
    private function checkImageValidation($pr) {
        try {
            if ($pr->getImage() != 'no_selection') {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'checkImageValidation ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Save Flubit Quantity
     * 
     * @param Integer $product
     * @return None
     */
    private function _saveFlubitQty($product) {
        try {
            if ($product->getSku() == '') {
                Mage::log($product->getId() . ' no sku', null, 'hum.txt');
                return;
            }
            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
            $flubit = Mage::getModel('flubit/flubit')->load($product->getSku(), 'sku');
            $flubitObj = Mage::getModel('flubit/flubit');
            if ($qty != $flubit->getQty()) {
                $flubit->setQty($qty)
                        ->setStatus('1')
                        ->save();
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . '_saveFlubitQty ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }
	
		 /* @Method :       cancleFlubitOrder
		 * @Parametere :    None 
		 * @return     :    None
		 */
	
		public function cancelFlubitOrder(Varien_Event_Observer $observer)
        {	
			try {
			$orderid = $observer->getEvent()->getCreditmemo()->order_id;
			Mage::log($orderid , null, Flubit_Flubit_Helper_Data::FLUBIT_OBSERVER_DELETE);
			$flubitorder = Mage::getModel('sales/order')->getCollection();
			$flubitorder->addFieldToFilter('main_table.entity_id', $orderid);
			$flubitorder->addFieldToSelect('flubit_order_id');
			//Mage::log(print_r($flubitorder->getData(),true) , null, Flubit_Flubit_Helper_Data::FLUBIT_OBSERVER_DELETE);	
				foreach ($flubitorder as $order) {
					Mage::log($order->getData('flubit_order_id') , null, Flubit_Flubit_Helper_Data::FLUBIT_OBSERVER_DELETE);
					$config = Mage::getModel('flubit/config');
					$collection = Mage::getModel('flubit/order')->getCollection();
					$collection->addFieldToFilter('flubit_order_id', $flubitOrderId);
					$collection->addFieldToSelect('dispatch');
					foreach ($collection as $dispatch) {
						
						if($dispatch->getDispatch() == '1') {
							$data = $config->refundFlubitOrders($order->getData('flubit_order_id'));
						} else {
							$data = $config->cancelFlubitOrders($order->getData('flubit_order_id'));
						}
					}
					
					
				}
				$order = Mage::getModel('sales/order')->load($orderid);
				$items = $order->getAllItems();		
				foreach ($items as $itemId => $item)
				{	
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
					$this->_saveFlubitQty($product);
				}
			} catch (Exception $e) {
            Mage::log("Exception Cancel Flubit Order : " . $e->getCode() . " message" . $e->getMessage(), null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
			}
		}
	
    /* @Method :        deleteFlubitProduct
     * @Parametere :    None 
     * @return     :    None
     */

    public function deleteFlubitProduct(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getProduct();
		$product_sku = $product->getData('sku');
		
        try {
            $data = Mage::getModel('flubit/flubit')->load($product_sku, 'sku');
			$data->setIsDeleted('1')
				 ->save();
            $config = Mage::getModel('flubit/config');
            $data = $config->inactiveProduct($product_sku, 0);
			//Mage::log($data . '------',null,'hum.log');
			$xml = $config->getproductFeedStatus($data,'xml');
			//Mage::log($xml . '+++++++',null,'hum.log');
            if (!isset($xml)) {
                Mage::log("Product is not deleted from flubit SKU :" . $product_sku , null, Flubit_Flubit_Helper_Data::FLUBIT_OBSERVER_DELETE);
            } else {
                $data = Mage::getModel('flubit/flubit')->load($product_sku, 'sku');
                $data->delete()->save();
            }
        } catch (Exception $e) {
            Mage::log("Exception Delete Failed : " . $e->getCode() . " message" . $e->getMessage(), null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

	
    /**
     * Remove send mail button in case of flubit order
     * 
     * @param Varien_Event_Observer $observer
     */
    public function removeSendMailButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getData( 'block' );
        
        //Order view page
        if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_View' && $block->getRequest()->getControllerName() == 'sales_order') {
            $order = $block->getOrder();
            if($order->getId() && $order->getData('flubit_order_id')) {
                $block->removeButton('send_notification');
                return;
            }
        }
        
        //Invoice view page
        if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Invoice_View' && $block->getRequest()->getControllerName() == 'sales_order_invoice') {
            $order = $block->getInvoice()->getOrder();
            if($order->getId() && $order->getData('flubit_order_id')) {
                $block->removeButton('send_notification');
                return;
            }
        }
        
        //Creditmemo view page
        if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Creditmemo_View' && $block->getRequest()->getControllerName() == 'sales_order_creditmemo') {
            $order = $block->getCreditmemo()->getOrder();
            if($order->getId() && $order->getData('flubit_order_id')) {
                $block->removeButton('send_notification');
                return;
            }
        }
        
        //Shipment view page
        if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Shipment_View' && $block->getRequest()->getControllerName() == 'sales_order_shipment') {
            $order = $block->getShipment()->getOrder();
            if($order->getId() && $order->getData('flubit_order_id')) {
                $block->removeButton('send_notification');
                return;
            }
        }
       
    }
    public function saveConfigObserver () {
         try {
         $write = Mage::getSingleton('core/resource')->getConnection('core_write');
         $flubit_product_table = Mage::getSingleton('core/resource')->getTableName('flubit/flubit');
         $write->query("UPDATE ".$flubit_product_table." SET `global_price_update` = '0'");
         } catch (Exception $e) {
            Mage::log(__LINE__ . ' Exception updateFlubitPrices  Model data ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
         }
        
    }
    
}