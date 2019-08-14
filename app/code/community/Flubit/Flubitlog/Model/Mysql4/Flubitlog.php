<?php

/**
 * Class Flubit_Flubitlog_Model_Status 
 * 
 * @package Flubit
 * @category Flubit_Flubitlog_Model_Mysql4_Flubitlog
 * @author Flubit team
 */
 
class Flubit_Flubitlog_Model_Mysql4_Flubitlog extends Mage_Core_Model_Mysql4_Abstract
{	

	/**
     * 
     * Construct for autoload and set property
     */
	
    public function _construct()
    {    
        // Note that the flubitlog_id refers to the key field in your database table.
        $this->_init('flubitlog/flubitlog', 'flubitlog_id');
    }
}