<?php
/**
 * Class Flubitlog Block Flubitnd
 * 
 * @package Flubit
 * @category Flubitlog_Block
 * @author Flubit team
 */

class Flubit_Flubitlog_Block_Flubitlog extends Mage_Core_Block_Template
{	
	/**
     * method for prepare layout
     * 
     * @return type
     */
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	 /**
     * method for getFlubitlog
     * 
     * @return string
     */
     public function getFlubitlog()     
     { 
        if (!$this->hasData('flubitlog')) {
            $this->setData('flubitlog', Mage::registry('flubitlog'));
        }
        return $this->getData('flubitlog');
        
    }
}