<?php

/**
 * Class Flubit_Flubitlog_Model_Status 
 * 
 * @package Flubit
 * @category Flubit_Flubitlog_Model_Status
 * @author Flubit team
 */
class Flubit_Flubitlog_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;
	
	/**
     * Method to Enable & Disable the logging 
     * 
     * @return  String
     */
	
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('flubitlog')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('flubitlog')->__('Disabled')
        );
    }
}