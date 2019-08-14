<?php

/**
 * Class Flubit Payment Helper Data
 * 
 * @package Flubit
 * @category Flubit_Helper
 * @author Flubit team
 */
class Flubit_Flubit_Helper_Payment_Data extends Mage_Payment_Helper_Data {

    /**
     * Get and sort available payment methods for specified or current store
     *
     * array structure:
     *  $index => Varien_Simplexml_Element
     *
     * @param mixed $store
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null) {
        $res = array();
        foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
            $prefix = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';

            //Custom
            $flubitorder = Mage::helper('flubit')->isFlubitOrder();
            $validation = false;
            if ($flubitorder && $code == 'banktransfer') {
                $validation = true;
            }

            if ((!$model = Mage::getStoreConfig($prefix . 'model', $store)) && !$validation) {
                continue;
            }
            $methodInstance = Mage::getModel($model);
            if (!$methodInstance) {
                continue;
            }
            $methodInstance->setStore($store);
            if (!$methodInstance->isAvailable($quote)) {
                /* if the payment method cannot be used at this time */
                continue;
            }
            $sortOrder = (int) $methodInstance->getConfigData('sort_order', $store);
            $methodInstance->setSortOrder($sortOrder);
            $res[] = $methodInstance;
        }

        usort($res, array($this, '_sortMethods'));
        return $res;
    }

}