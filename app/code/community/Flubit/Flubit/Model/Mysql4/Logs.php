<?php

/**
 * Class Flubit Model Mysql Flubit Logs
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Mysql4_Logs extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Constructor for Flubit logs 
     *  
     */
    protected function _construct() {
        $this->_init("flubit/logs", "flubit_id");
    }

}