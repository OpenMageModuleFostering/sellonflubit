<?php

/**
 * Class Flubit Model Logs
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Order extends Mage_Core_Model_Abstract {

    /**
     * Constructor for load Flubit Order
     * 
     */
    public function _construct() {
        try {
        parent::_construct();
        $this->_init('flubit/order');
         } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Model_Order  _construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}