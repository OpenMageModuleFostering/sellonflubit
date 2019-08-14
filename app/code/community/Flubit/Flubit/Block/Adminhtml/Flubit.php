<?php

/**
 * Class Flubit Block Admin Flubit
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Adminhtml_Flubit extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * 
     * Construct for autoload and set property
     */
    public function __construct() {
        try {
            $this->_controller = 'adminhtml_flubit';
            $this->_blockGroup = 'flubit';
            $this->_headerText = Mage::helper('flubit')->__('Flubit Product Manager');
            parent::__construct();
            $this->removeButton('add');
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit  __construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}