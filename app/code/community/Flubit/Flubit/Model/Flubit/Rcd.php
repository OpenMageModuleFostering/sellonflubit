<?php

/**
 * Class Flubit Model Flubit Source
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Rcd {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => "*/12 * * * *", 'label' => Mage::helper('adminhtml')->__('12mins')),
            array('value' => "*/27 * * * *", 'label' => Mage::helper('adminhtml')->__('27mins')),
            array('value' => "*/47 * * * *", 'label' => Mage::helper('adminhtml')->__('47mins')),
            );
    }

}
