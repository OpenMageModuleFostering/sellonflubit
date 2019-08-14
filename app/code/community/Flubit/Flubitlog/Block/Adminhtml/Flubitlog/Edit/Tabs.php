<?php
/**
 * Class Flubitlog Block Flubitlog Edit Tabs
 * 
 * @package Flubit
 * @category Flubitlog_Edit_Tabs
 * @author Flubit team
 */
class Flubit_Flubitlog_Block_Adminhtml_Flubitlog_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	
	/**
     * Construct and autoload initModule
     */
    public function __construct() {
        parent::__construct();
        //$this->setId('flubitlog_tabs');
        $this->setDestElementId('edit_form');
        //$this->setTitle(Mage::helper('flubitlog')->__('Item Information'));
    }
	
	/**
     * Method for Create Flubit logging 
     * 
     * @param data check String $xml
     * @return Xml String
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            // 'label'     => Mage::helper('flubitlog')->__('Item Information'),
            // 'title'     => Mage::helper('flubitlog')->__('Item Information'),
            'content' => $this->getLayout()->createBlock('flubitlog/adminhtml_flubitlog_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}