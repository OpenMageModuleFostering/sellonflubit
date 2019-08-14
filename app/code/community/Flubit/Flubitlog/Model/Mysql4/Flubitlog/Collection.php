<?php

/**
 * Class Flubit Flubitlog Model Mysql4 Flubitlog Collection 
 * 
 * @package Flubit
 * @category Flubit_Flubitlog_Model_Mysql4_Flubitlog_Collection
 * @author Flubit team
 */
class Flubit_Flubitlog_Model_Mysql4_Flubitlog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{	
	
	/**
     * 
     * Construct for autoload and set property
     */

    public function _construct()
    {
        parent::_construct();
        $this->_init('flubitlog/flubitlog');
    }
}