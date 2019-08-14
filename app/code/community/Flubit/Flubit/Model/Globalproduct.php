<?php



/**

 * Class Flubit Model Logs

 * 

 * @package Flubit

 * @category Global product update Model

 * @author Flubit team

 */

class Flubit_Flubit_Model_Globalproduct extends Mage_Core_Model_Abstract {



    /**

     * Constructor for load global update product 

     * 

     */

    public function _construct() {

        try {

        parent::_construct();

        $this->_init('flubit/globalproduct');

         } catch (Exception $e) {

            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Model_Order  _construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }

    

     

    /**

     * Method to Collect Mass global update product and send in queqe

     */

    public function sendFlubitProductMassAttributeUpdate() {


       $flubitCollection = Mage::getModel('flubit/globalproduct')->getCollection()

                        ->addfieldtofilter('update_status', '0')

                        ->addFieldToSelect('product_id')

                        ->addFieldToSelect('flubit_status')

                        ->addFieldToSelect('id')

                        ->setPageSize('200');

       foreach ($flubitCollection as $flubitglobal) {

          $product[] = $flubitglobal->getFlubitStatus() . '_' . $flubitglobal->getProductId();

          $flubitglobal->setUpdateStatus('1')

                       ->save();

       }

       if(count($product) > 0)

       $this->saveFlubitProductMassAttributeData($product);

    }

    

    

    /**

     * Method for Mass Update Flubit Products

     * 

     * @param Varien_Event_Observer $observer

     * @return Boolean

     */

    public function saveFlubitProductMassAttributeData($productlist = array()) {

        try {

              foreach ($productlist as $product) {

                    $product = explode("_",$product);

                    $type = $product[0];

                    $product_id = $product[1];

                    $product = Mage::getModel('catalog/product')->load($product_id);

                    $flubit = Mage::getModel('flubit/flubit')->load($product->getSku(), 'sku');

                    if ($flubit->getId() != '') {

                        $flubit->setName($product->getName())

                                ->setStatus('1')

                                ->save();

                    } else {

                        if ($type == '1') {

                            $this->_saveFlubitProductData($product);

                        }

                    }

                }

            return;

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

            Mage::log('SKU : ' . $product->getSku() . ' ID '. $product->getId(), null, 'hum.txt');

            $flubit = Mage::getModel('flubit/flubit')->load($product->getSku(), 'sku');



            $flubitObj = Mage::getModel('flubit/flubit');

            //$flubitPrice = (number_format($flubitObj->getFlubitPrice($product), 2, '.', ''));

            

            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();//$flubitObj->getQty();

            //Mage::log(print_r($qty, TRUE), null, 'hum.txt');

            Mage::log('Quantity = ' . $qty, null, 'hum.txt');

            if ($flubit->getId() == '') {

                $flubit = Mage::getModel('flubit/flubit');

                $flubit->setName($product->getName())

                        ->setSku($product->getSku())

                        ->setPrice(0)

                        ->setQty($qty)

                        ->setStatus('1')

                        ->setNew('1')

                        ->save();

            } else {

                if (($this->checkEanValidation($product) && $this->checkImageValidation($product))) {

                    $flubit->setName($product->getName())

                            ->setSku($product->getSku())

                            ->setPrice(0)

                            ->setQty($qty)

                            ->setStatus('1')

                            ->save();

                }

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

    

    

}