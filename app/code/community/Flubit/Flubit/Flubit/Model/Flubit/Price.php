<?php

/**
 * Class Flubit Model Flubit Price
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Price {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => "", 'label' => ''),
            array('value' => "price", 'label' => Mage::helper('adminhtml')->__('Price')),
            array('value' => "cost", 'label' => Mage::helper('adminhtml')->__('Cost')),
           
        );
    }

}
