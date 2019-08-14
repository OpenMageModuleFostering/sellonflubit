<?php

/**
 * Class Flubit Model Flubit Chunk
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Chunk {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
		    array('value' => "10", 'label' => Mage::helper('adminhtml')->__('10')),
		    array('value' => "20", 'label' => Mage::helper('adminhtml')->__('20')),
            array('value' => "50", 'label' => Mage::helper('adminhtml')->__('50')),
			array('value' => "100", 'label' => Mage::helper('adminhtml')->__('100')),
			array('value' => "200", 'label' => Mage::helper('adminhtml')->__('200')),
			array('value' => "300", 'label' => Mage::helper('adminhtml')->__('300')),
		);
    }

}
