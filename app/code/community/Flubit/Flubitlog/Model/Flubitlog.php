<?php

/**
 * Class Flubitlog Flubitlog Model 
 * 
 * @package Flubit
 * @category Flubitlog_Model_Flubitlog
 * @author Flubit team
 */
class Flubit_Flubitlog_Model_Flubitlog extends Mage_Core_Model_Abstract
{	
	public function _construct()
    {
        parent::_construct();
        $this->_init('flubitlog/flubitlog');
    }
}