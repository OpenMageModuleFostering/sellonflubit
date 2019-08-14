<?php

/**
 * Class Flubit Adminhtml Flubit
 * 
 * @package Flubit
 * @category Flubit_Adminhtml
 * @author Flubit team
 */
class Flubit_Flubit_Adminhtml_FlubitController extends Mage_Adminhtml_Controller_Action {

    /**
     * Method for InitAction Load
     * @return \Flubit_Flubit_Adminhtml_FlubitController
     */
    protected function _initAction() {
        try {
            $this->loadLayout()
                    ->_setActiveMenu('flubit/items')
                    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

            return $this;
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Adminhtml_FlubitController  _initAction ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Index Method load Action and Render
     * 
     */
    public function indexAction() {
        // Let's call our initAction method which will set some basic params for each action
        try {
            $this->_initAction()
                    ->renderLayout();
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Adminhtml_FlubitController  indexAction ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Render to Edit
     * 
     */
    public function newAction() {
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }

    /**
     * Method for Render to Edit Action
     * 
     * @return none
     */
    public function editAction() {
        try {
            $this->_initAction();

            // Get id if available
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('flubit/flubit');

            if ($id) {
                // Load record
                $model->load($id);

                // Check if record is loaded
                if (!$model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('This baz no longer exists.'));
                    $this->_redirect('*/*/');

                    return;
                }
            }

            $this->_title($model->getId() ? $model->getName() : $this->__('New Baz'));

            $data = Mage::getSingleton('adminhtml/session')->getBazData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('flubit', $model);

            $this->_initAction()
                    ->_addBreadcrumb($id ? $this->__('Edit Baz') : $this->__('New Baz'), $id ? $this->__('Edit Baz') : $this->__('New Baz'))
                    ->_addContent($this->getLayout()->createBlock('foo_bar/adminhtml_baz_edit')->setData('action', $this->getUrl('*/*/save')))
                    ->renderLayout();
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Adminhtml_FlubitController  editAction ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Save Product Session Message
     * 
     */
    public function saveAction() {
        try {
            if ($postData = $this->getRequest()->getPost()) {
                $model = Mage::getSingleton('foo_bar/baz');
                $model->setData($postData);

                try {
                    $model->save();

                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The baz has been saved.'));
                    $this->_redirect('*/*/');

                    return;
                } catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this baz.'));
                }

                Mage::getSingleton('adminhtml/session')->setBazData($postData);
                $this->_redirectReferer();
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Adminhtml_FlubitController  saveAction ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }
	
	/** 0.3.2 **/
	public function massactionAction() {
		$flubitIds = $this->getRequest()->getParam('flubit_id');
		$statusAction = $this->getRequest()->getParam('status');
		$priceAction = $this->getRequest()->getParam('price');
		$priceIncreaseFixedAction = $this->getRequest()->getParam('priceIncreaseFixed');
		$priceIncreaseAction = $this->getRequest()->getParam('priceIncrease');
		$priceIncreaseCentAction = $this->getRequest()->getParam('priceIncreaseCent');
		$priceDecreaseAction = $this->getRequest()->getParam('priceDecrease');
		$priceDecreaseCentAction = $this->getRequest()->getParam('priceDecreaseCent');
		
		if (isset($statusAction)) {
		$message = $this -> updateMassStatusAction ($flubitIds,$statusAction);
		}
		if (isset($priceAction)) {
		$message = $this -> updateMassPriceAction ($flubitIds,$priceAction);
		}
		if (isset($priceIncreaseFixedAction)) {
		$message = $this -> updateMassPriceChangeAction ('fixedIncrease',$flubitIds,$priceIncreaseFixedAction);
		}
		if (isset($priceIncreaseAction)) {
		$message = $this -> updateMassPriceChangeAction ('priceIncrease',$flubitIds,$priceIncreaseAction);
		}
		if (isset($priceIncreaseCentAction)) {
		$message = $this -> updateMassPriceChangeAction ('percentIncrease',$flubitIds,$priceIncreaseCentAction);
		}
		if (isset($priceDecreaseAction)) {
		$message = $this -> updateMassPriceChangeAction ('priceDecrease',$flubitIds,$priceDecreaseAction);
		}
		if (isset($priceDecreaseCentAction)) {
		$message = $this -> updateMassPriceChangeAction ('percentDecrease',$flubitIds,$priceDecreaseCentAction);
		}
		
		$this->_getSession()->addSuccess($message);
		
		$this->_redirect('*/*/index');
	}
	
	public function updateMassStatusAction ($flubitIds,$statusAction) {
		 	if ($flubitIds) {
				foreach ($flubitIds as $flubitId) {
                	$collection = Mage::getModel('flubit/flubit')->getCollection();
                	$collection->addFieldToFilter('flubit_id', $flubitId)
                        ->addFieldToSelect('sku')
                        ->addFieldToSelect('flubit_id');	
                	foreach ($collection as $flubit) {
						
					switch ($statusAction) {
					case 'inactive':
						$UserDisabled = 1;
						$ActiveStatus = 0;
					break;
					default : // inactive price calculation
						$UserDisabled = 0;	
						$ActiveStatus = 1;	
					break;
					}
                    $sku = $flubit->getSku();
                    $flubit			->setUserDisabled($UserDisabled)
									->setActiveStatus($ActiveStatus)
                                    ->setStatus('1')
                            		->save();
					
              		$message .= Mage::helper('adminhtml')->__('Flubit Set '.$statusAction.' for "%s" <br>', $flubit->getSku());	
					}
				}
				return $message;
			 }
	}
	

	public function updateMassPriceAction ($flubitIds,$priceAction) {
		
            $priceBasedOn = Mage::getStoreConfig('flubit_section/flubit_setup/price_based_on');
            $globalPrice = Mage::getStoreConfig('flubit_section/flubit_setup/global_price');
		
            if ($flubitIds) {
				foreach ($flubitIds as $flubitId) {
                	$collection = Mage::getModel('flubit/flubit')->getCollection();
                	$collection->addFieldToFilter('flubit_id', $flubitId)
                        ->addFieldToSelect('sku')
                        ->addFieldToSelect('flubit_id')
                        ->addFieldToSelect('price');	
                	foreach ($collection as $flubit) {
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $flubit->getSku());
                    if (is_object($product)) {
                        if ($priceBasedOn) {
                            $priceOfProduct = $product->getData($priceBasedOn); // Get price based on selected price
                        } else {
                            $priceOfProduct = $product->getPrice($priceBasedOn); // get default magento price
                        }

					switch ($priceAction) {
					case 'manual':
						$priceActionChoice = 0;
						$flubitPrice =  $flubit->getPrice();
					break;
					default : // global price calculation
						$priceActionChoice = 1;	
                        $flubitPrice = $priceOfProduct * $globalPrice;
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
					break;
					}
                    $sku = $flubit->getSku();
                    $flubit			->setPrice($flubitPrice)
									->setUseGlobalPrice($priceActionChoice)
                                    ->setGlobalPriceUpdate('1')
                            		->save();
              		$message .= Mage::helper('adminhtml')->__('Flubit Price overriden for "%s" <br>', $flubit->getSku());
                	}
					
					}
				}
				return $message;
			}
		
	}
	
	
	public function updateMassPriceChangeAction($method,$flubitIds,$value) {
			// lets tidy the user input
			$value =  str_replace('%','', trim($value));
			$value =  str_replace('$','', trim($value));
			$value =  str_replace('€','', trim($value));
			$value =  str_replace('£','', trim($value));
		
            $priceBasedOn = Mage::getStoreConfig('flubit_section/flubit_setup/price_based_on');
            $globalPrice = Mage::getStoreConfig('flubit_section/flubit_setup/global_price');
            if ($flubitIds) {
				foreach ($flubitIds as $flubitId) {
					
                	$collection = Mage::getModel('flubit/flubit')->getCollection();
                	$collection->addFieldToFilter('flubit_id', $flubitId)
                        ->addFieldToSelect('sku')
                        ->addFieldToSelect('flubit_id')
                        ->addFieldToSelect('price');	
                	foreach ($collection as $flubit) {
						
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $flubit->getSku());
                    if (is_object($product)) {
                        if ($priceBasedOn) {
                            $priceOfProduct = $product->getData($priceBasedOn); // Get price based on selected price
                        } else {
                            $priceOfProduct = $product->getPrice($priceBasedOn); // get default magento price
                        }
					
						$flubitPrice =  $flubit->getPrice();
						$priceActionChoice = 0; // manual price
					switch ($method) {
					case 'fixedIncrease':
						// we are passinbg a fixed price
                        $flubitPrice = number_format($value, 2, '.', '');
						
					break;
					case 'priceIncrease':
						
                        $flubitPrice = $priceOfProduct + $value;
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
						
					break;
					case 'percentIncrease':
						if ($value > 1) { // assume customer passed a % eg 20%
							$value = $value / 100;
						}
                        $flubitPrice = $priceOfProduct * (1 + $value);
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
						
					break;
					case 'priceDecrease':
						
                        $flubitPrice = $priceOfProduct - $value;
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
						
					break;
					case 'percentDecrease':
						
						if ($value > 1) { // assume customer passed a % eg 25%
							$value = $value / 100;
						}
                        $flubitPrice = $priceOfProduct * (1 - $value);  // 25% decrease  = 75% product price = 0.75
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
						
					break;
					default : // default global price calculation
						$priceActionChoice = 1;	
                        $flubitPrice = $priceOfProduct * $globalPrice;
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
					break;
						
					}
					
					
                    $flubit			->setPrice($flubitPrice)
									->setUseGlobalPrice($priceActionChoice)
                                    ->setGlobalPriceUpdate('1')
                            		->save();
              		$message .= Mage::helper('adminhtml')->__('Flubit Price overriden for "%s" <br>', $flubit->getSku());
					}
					}
				}
				return $message;
			}
		
		
	}
	
	
	
	public function exportCsvAction()
			{
			$fileName = 'flubit.csv';
			$content = $this->getLayout()
				->createBlock('flubit/adminhtml_flubit_grid')
				->getCsv();
			$this->_prepareDownloadResponse($fileName, $content);
			}

	public function exportXmlAction()
			{
			$fileName = 'flubit.xml';
			$content = $this->getLayout()
				->createBlock('flubit/adminhtml_flubit_grid')
				->getXml();
			$this->_prepareDownloadResponse($fileName, $content);
			}
	

    /**
     * Method for Update Flubit Price
     * 
     */
    public function updateFlubitPriceAction() {
        try {
            $error = false;
            $message = '';

            $fieldId = (int) $this->getRequest()->getParam('id');
            $price = number_format($this->getRequest()->getParam('price'), 2, '.', '');
            $sku = $this->getRequest()->getParam('sku');
            if ($fieldId) {
                $collection = Mage::getModel('flubit/flubit')->getCollection();
                $collection->addFieldToFilter('flubit_id', $fieldId);

                foreach ($collection as $flubit) {
                    $sku = $flubit->getSku();
                    $flubit->setPrice($price)
                            ->setStatus(1)
                            ->save();
                }
              $message = Mage::helper('adminhtml')->__('Flubit  Price overriden for "%s"', $flubit->getSku());
            } else {
                $error = true;
                $message = 'An error occured. Please retry';
            }
            $return = array();
            if ($error) {
                $return['ERROR'] = 1;
            } else {
                $return['SUCCESS'] = 1;
                $return['PRICE'] = $price;
            }

            $return['MESSAGE'] = $message;
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Adminhtml_FlubitController  updateFlubitPriceAction ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * Method for Update Flubit Price Calculator
     * 
     */
    public function updateFlubitPriceCalculatorAction() {
        try {
            $error = false;
            $message = '';
            $fieldId = (int) $this->getRequest()->getParam('id');
            $status = $this->getRequest()->getParam('status');
            $priceBasedOn = Mage::getStoreConfig('flubit_section/flubit_setup/price_based_on');
            $globalPrice = Mage::getStoreConfig('flubit_section/flubit_setup/global_price');
            if ($fieldId && $status !== '') {
                $collection = Mage::getModel('flubit/flubit')->getCollection()
                        ->addFieldToFilter('flubit_id', $fieldId)
                        ->addFieldToSelect('sku')
                        ->addFieldToSelect('flubit_id')
                        ->addFieldToSelect('price');
                foreach ($collection as $flubit) {
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $flubit->getSku());
                    if (is_object($product)) {
                        if ($priceBasedOn) {
                            $priceOfProduct = $product->getData($priceBasedOn); // Get price based on selected price
                        } else {
                            $priceOfProduct = $product->getPrice($priceBasedOn); // get default magento price
                        }

                        $flubitPrice = $priceOfProduct * $globalPrice;
                        $flubitPrice = number_format($flubitPrice, 2, '.', '');
                        //updating the price in the main table
                        if ($status == '1') {
                            $flubit->setPrice($flubitPrice)
                                    ->setUseGlobalPrice('1')
                                    ->setGlobalPriceUpdate('1')
                                    ->setStatus('1')
                                    ->save();
                        } else {
                            $flubit->setGlobalPriceUpdate('1')
                                    ->setUseGlobalPrice('0')
                                    ->setStatus('1')
                                    ->save();
                        }
                        $message = Mage::helper('adminhtml')->__('Flubit  Price overriden for "%s"', $flubit->getSku());
                    } // product object check
                }
            } else {
                $error = true;
                $message = Mage::helper('adminhtml')->__('Missing flubit row id or status.');
            }

            $return = array();
            if ($error) {
                $return['ERROR'] = 1;
            } else {
                $return['SUCCESS'] = 1;
                $return['PRICE'] = $flubitPrice;
                $return['ID'] = $fieldId;
            }

            $return['MESSAGE'] = $message;
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Adminhtml_FlubitController  updateFlubitPriceCalculatorAction ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}