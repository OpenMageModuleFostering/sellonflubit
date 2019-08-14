<?php

/**
 * Class Flubit Block Flubitnd
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Flubit extends Mage_Core_Block_Template {

    /**
     * method for prepare layout
     * 
     * @return type
     */
    public function _prepareLayout() {
        try {
            return parent::_prepareLayout();
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Flubit  _prepareLayout ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * method for getFlubit
     * 
     * @return string
     */
    public function getFlubit() {
        try {
            if (!$this->hasData('flubit')) {
                $this->setData('flubit', Mage::registry('flubit'));
            }
            return $this->getData('flubit');
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Flubit  _prepareLayout ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}