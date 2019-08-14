<?php

class Flubit_Flubit_Model_Payment_Method_Flubitterms extends Mage_Payment_Model_Method_Checkmo {

    protected $_code = 'flubitterms';

    /**
     * Check whether payment method can be used
     *
     * TODO: payment method instance is not supposed to know about quote
     *
     * @param Mage_Sales_Model_Quote|null $quote
     *
     * @return bool
     */
    public function isAvailable($quote = null) {
        $checkResult = new StdClass;

        //Custom
        $flubitorder = Mage::helper('flubit')->isFlubitOrder();
        $validation = false;
        if ($flubitorder && $this->_code == 'flubitterms') {
            $validation = true;
        }

        $isActive = (bool) (int) $this->getConfigData('active', $quote ? $quote->getStoreId() : null);

        //Custom
        if ($validation) {
            $isActive = true;
        } else {
            $isActive = false;
        }

        $checkResult->isAvailable = $isActive;
        $checkResult->isDeniedInConfig = !$isActive; // for future use in observers
        Mage::dispatchEvent('payment_method_is_active', array(
            'result' => $checkResult,
            'method_instance' => $this,
            'quote' => $quote,
        ));
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.8', '>=')) {
            if ($checkResult->isAvailable && $quote) {
                $checkResult->isAvailable = $this->isApplicableToQuote($quote, self::CHECK_RECURRING_PROFILES);
            }
        } else if (version_compare($magentoVersion, '1.7', '>=')) {
            if ($checkResult->isAvailable) {
                $implementsRecurring = $this->canManageRecurringProfiles();
                // the $quote->hasRecurringItems() causes big performance impact, thus it has to be called last
                if ($quote && !$implementsRecurring && $quote->hasRecurringItems()) {
                    $checkResult->isAvailable = false;
                }
            }
        } else {
            
        }

        return $checkResult->isAvailable;
    }

}