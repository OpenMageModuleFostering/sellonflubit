<?php

/**
 * Class Flubit Adminhtml Flubit
 * 
 * @package Flubit
 * @category Flubit_Flubit
 * @author Flubit team
 */
class Flubit_Flubit_FlubitController extends Mage_Adminhtml_Controller_Action {

    public function generateProductXMLAction() {
        $error = false;
        try {
            $result = Mage::getModel('flubit/flubit')->generateProductXML();
            $message = $this->__($result);
        } catch (Exception $e) {
            $error = true;
            $message = $this->__('%s', $e->getMessage());
        }

        if ($error) {
            Mage::getSingleton('adminhtml/session')->addError($message);
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        }

        $this->_redirect('adminhtml/system_config/edit');
    }

}