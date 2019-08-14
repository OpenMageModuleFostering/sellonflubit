<?php

/**
 * Class Flubit Model Flubit Source
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Source {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => "*/2 * * * *", 'label' => Mage::helper('adminhtml')->__('2mins')),
            array('value' => "*/5 * * * *", 'label' => Mage::helper('adminhtml')->__('5mins')),
            array('value' => "*/10 * * * *", 'label' => Mage::helper('adminhtml')->__('10mins')),
            array('value' => "*/25 * * * *", 'label' => Mage::helper('adminhtml')->__('25mins')),
			array('value' => "*/50 * * * *", 'label' => Mage::helper('adminhtml')->__('50mins')),
        );
    }

}
