<?php

/**
 * Class Flubit Model Logs
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Logs extends Mage_Core_Model_Abstract {

    /**
     * Constructor for Flubit logs 
     *  
     */
    public function _construct() {
        try {
        parent::_construct();
        $this->_init('flubit/logs');
        } catch (Exception $e) {
            Mage::log(__LINE__ . ' Flubit_Flubit_Model_Logs Exception _construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}