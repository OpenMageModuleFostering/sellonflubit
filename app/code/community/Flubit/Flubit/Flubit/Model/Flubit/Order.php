<?php

/**
 * Class Flubit Model Flubit Source
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Order {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
			array('value' => "*/3 * * * *", 'label' => Mage::helper('adminhtml')->__('3mins')),
            array('value' => "*/7 * * * *", 'label' => Mage::helper('adminhtml')->__('7mins')),
            array('value' => "*/14 * * * *", 'label' => Mage::helper('adminhtml')->__('14mins')),
            array('value' => "*/21 * * * *", 'label' => Mage::helper('adminhtml')->__('21mins')),
            array('value' => "*/28 * * * *", 'label' => Mage::helper('adminhtml')->__('28mins')),
			array('value' => "*/56 * * * *", 'label' => Mage::helper('adminhtml')->__('56mins')),
            
        );
    }

}
