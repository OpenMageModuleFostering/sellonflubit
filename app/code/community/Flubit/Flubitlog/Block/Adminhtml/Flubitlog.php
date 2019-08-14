<?php

/**
 * Class Flubitlog Block Admin Flubitlog
 * 
 * @package Flubit
 * @category Flubitlog_Block
 * @author Flubit team
 */
class Flubit_Flubitlog_Block_Adminhtml_Flubitlog extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        
        $flubit_log =  Mage::getModel('flubitlog/flubitlog')->getCollection()->getSize();
        if($flubit_log > 0) 
        $DisplayString = 'Please note the logs are only available for last 30 days';
        else
        $DisplayString = 'There are no logs at this time';
        
        $this->_controller = 'adminhtml_flubitlog';
        $this->_blockGroup = 'flubitlog';
        $this->_headerText = Mage::helper('flubitlog')->__('<span style="font-size:14px; font-weight:bold; color:#00AEED;">' . $DisplayString . '</span>');
		$this->_addButtonLabel = Mage::helper('flubitlog')->__('Add Item');
        parent::__construct();
        $this->_removeButton('add');
    }

}