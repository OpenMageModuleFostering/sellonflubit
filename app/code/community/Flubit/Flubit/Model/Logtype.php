<?php

/**
 * Class Flubit Model Logs
 * 
 * @package Flubit
 * @category Flubit_Model_Ordertime
 * @author Flubit team
 */
class Flubit_Flubit_Model_Logtype extends Mage_Core_Model_Abstract {
    /**
     * Constant define for logging type
     *
     */

    const CPRODUCT = 'Create Product';
    const UPRODUCT = 'Update Product';
    const DPRODUCT = 'Delete Product';
    const CFEED = 'Check Feed Response';
    const FORDER = 'Fetch Order';
    const DORDER = 'Dispatch Order';
    const CORDER = 'Cancel Order';
    const RORDER = 'Refund Order';
    const MORDER = 'Order in Magento';
    const CERROR = 'Communication Error';

    /**
     * Gets the list of type for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'value' => self::CPRODUCT,
                'label' => Mage::helper('flubit')->__('Create Product')),
            array(
                'value' => self::UPRODUCT,
                'label' => Mage::helper('flubit')->__('Update Product')),
            array(
                'value' => self::DPRODUCT,
                'label' => Mage::helper('flubit')->__('Delete Product')),
            array(
                'value' => self::CFEED,
                'label' => Mage::helper('flubit')->__('Check Feed Response')),
            array(
                'value' => self::FORDER,
                'label' => Mage::helper('flubit')->__('Fetch Order')),
            array(
                'value' => self::DORDER,
                'label' => Mage::helper('flubit')->__('Dispatch Order')),
            array(
                'value' => self::CORDER,
                'label' => Mage::helper('flubit')->__('Cancel Order')),
            array(
                'value' => self::RORDER,
                'label' => Mage::helper('flubit')->__('Refund Order')),
            array(
                'value' => self::MORDER,
                'label' => Mage::helper('flubit')->__('Create Order in Magento')),
            array(
                'value' => self::CERROR,
                'label' => Mage::helper('flubit')->__('Communication Error'))
        );
    }

}