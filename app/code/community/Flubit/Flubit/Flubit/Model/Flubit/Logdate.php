<?php

/**
 * Class Flubit Model Flubit Source
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Logdate {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
		    array('value' => "30", 'label' => Mage::helper('adminhtml')->__('1 Month')),
			array('value' => "90", 'label' => Mage::helper('adminhtml')->__('3 Month')),
		    array('value' => "180", 'label' => Mage::helper('adminhtml')->__('6 Month')),
            array('value' => "365", 'label' => Mage::helper('adminhtml')->__('12 Month')),
		);
    }

}
