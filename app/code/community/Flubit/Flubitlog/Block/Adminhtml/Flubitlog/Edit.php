<?php

/**
 * Class Flubitlog Block Flubitnd
 * 
 * @package Flubit
 * @category Flubitlog_Block
 * @author Flubit team
 */
class Flubit_Flubitlog_Block_Adminhtml_Flubitlog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	
	/**
     * Construct and autoload initModule
     */
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'flubitlog';
        $this->_controller = 'adminhtml_flubitlog';

        $this->_updateButton('save', 'label', Mage::helper('flubitlog')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('flubitlog')->__('Delete Item'));
        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');

    }
	
	/**
     * Method for Create Flubit logging 
     * 
     * @param data check String $xml
     * @return Xml String
     */
    public function getHeaderText() {
        if (Mage::registry('flubitlog_data') && Mage::registry('flubitlog_data')->getId()) {
            return Mage::helper('flubitlog')->__("Log details ");
        } else {
            return Mage::helper('flubitlog')->__('Add Item');
        }
    }

}