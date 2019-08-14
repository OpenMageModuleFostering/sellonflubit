<?php

/**
 * Class Flubit Model Flubit Source
 * 
 * @package Flubit
 * @category Flubit_Model
 * @author Flubit team
 */
class Flubit_Flubit_Model_Flubit_Logs {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => "0 */24 * * *", 'label' => Mage::helper('adminhtml')->__('1day')),
			array('value' => "0 0 */2 * *", 'label' => Mage::helper('adminhtml')->__('2day')),
			array('value' => "0 0 */3 * *", 'label' => Mage::helper('adminhtml')->__('3day')),
			array('value' => "0 0 */7 * *", 'label' => Mage::helper('adminhtml')->__('7day')),
			array('value' => "0 0 */15 * *", 'label' => Mage::helper('adminhtml')->__('15day')),
        );
    }

}
