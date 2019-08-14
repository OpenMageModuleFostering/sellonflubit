<?php

class Flubit_Flubit_Model_Shipping extends Mage_Shipping_Model_Shipping {

    /**
     * Get carrier by its code
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCarrierByCode($carrierCode, $storeId = null) {
        //if flubit order pass the flatrate shipping method
        $flubitorder = Mage::helper('flubit')->isFlubitOrder();
        $validation = false;
        if ($flubitorder && $carrierCode == 'flatrate') {
            $validation = true;
        }

        if (!Mage::getStoreConfigFlag('carriers/' . $carrierCode . '/' . $this->_availabilityConfigField, $storeId) && !$validation) {
            return false;
        }

        $className = Mage::getStoreConfig('carriers/' . $carrierCode . '/model', $storeId);
        if (!$className) {
            return false;
        }
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }

}
