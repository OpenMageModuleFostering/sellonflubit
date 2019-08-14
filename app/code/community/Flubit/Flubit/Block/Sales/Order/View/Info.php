<?php

/**
 * Class Flubit Block Sales order view info
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('flubit/sales/order/view/info.phtml');
    }

}