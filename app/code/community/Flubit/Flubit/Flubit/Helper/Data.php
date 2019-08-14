<?php

/**
 * Class Flubit Helper Data
 * 
 * @package Flubit
 * @category Flubit_Helper
 * @author Flubit team
 */
class Flubit_Flubit_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * 
     */

    const FLUBIT_MISSING_CONFIG = 'flubit_missing_config.log';
    const FLUBIT_CREATE_PRODUCT = 'flubit_create_product.log';
    const FLUBIT_UPDATE_PRODUCT = 'flubit_update_product.log';
    const FLUBIT_CHECK_FEED = 'flubit_check_feed.log';
    const FLUBIT_EXCEPTIONS = 'flubit_exceptions.log';
    const FLUBIT_CREATE_ORDER = 'flubit_magento_create_order.log';
    const FLUBIT_DISPATCH_ORDER = 'flubit_magento_dispatch_order.log';
    const FLUBIT_CANCEL_ORDER = 'flubit_magento_cancel_order.log';
    const FLUBIT_REFUND_ORDER = 'flubit_magento_refund_order.log';
    const FLUBIT_OBSERVER_DELETE = 'flubit_magento_delete_product.log';
    const FLUBIT_FAILED_PRODUCT = 'flubit_failed_products.log';
    const FLUBIT_FEED = 'flubit_product_feed.log';
    const FLUBIT_ORDER_FETCH = 'Test_Order_Fetch.log';
    const FLUBIT_COMMUNICATION = 'flubit_communication.log';

    public function isFlubitOrder() {
        $checkout = $this->getCheckout();
        if ($checkout->getData('flubit_order')) {
            return true;
        } else {
            return false;
        }
    }

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function logCommunicationErrors($request, $response, $feedid, $mode = '') {
				
        if (($request != '') && ($response != '')) {
            try {
                $flubitlog = Mage::getModel('flubitlog/flubitlog');
                $flubitlog->setData(
                                array(
                                    'request_xml' => $request,
                                    'feedid' => $feedid,
                                    'response_xml' => $response,
                                    'action' => '8',
                                    'datetime' => date('Y-m-d H:i:s'),
                                    'level' => '2'
                                )
                        )
                        ->save();
            } catch (Exception $e) {
                Mage::log('Exception saving log to flubit' . $e ,NULL,Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
            }
        }
    }

}

