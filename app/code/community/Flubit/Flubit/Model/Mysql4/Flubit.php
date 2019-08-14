<?php

/**
 * Class Flubit Model Mysql Flubit
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Mysql4_Flubit extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Constructor for Flubit call
     * 
     */
    protected function _construct() {
        $this->_init("flubit/flubit", "flubit_id");
    }

}