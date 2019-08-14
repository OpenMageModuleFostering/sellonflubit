<?php

/**
 * Class Flubit Model Logs
 * 
 * @package Flubit
 * @category Flubit_Model_Ordertime
 * @author Flubit team
 */
class Flubit_Flubit_Model_Ordertime extends Mage_Core_Model_Abstract {

    /**
     * Constructor for load Flubit Order
     * 
     */
    public function _construct() {
        try {
            parent::_construct();
            $this->_init('flubit/ordertime');
        } catch (Exception $e) {
            Mage::log("Exception Delete Failed : " . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}