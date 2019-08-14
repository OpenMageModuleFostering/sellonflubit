<?php

/**
 * Class Flubit Block Admin Widget Grid Column Renderer Inlineupdate
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Adminhtml_Widget_Grid_Column_Renderer_InlineUpdateButton extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    /**
     * method for update price link
     * 
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        try {
            $html = parent::render($row);

            $html = '<a href="#" ';
            $html .= ' id="link_' . $row->getId() . '" ';
            $html .= 'onclick="updatePriceAndGlobalCalculator(' . $row->getId() . ', \'' . $row->getPrice() . '\',' . $row->getUseGlobalPrice() . '); return false">Update Price</a> ';
            return $html;
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Widget_Grid_Column_Renderer_InlineUpdateButton  render ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}