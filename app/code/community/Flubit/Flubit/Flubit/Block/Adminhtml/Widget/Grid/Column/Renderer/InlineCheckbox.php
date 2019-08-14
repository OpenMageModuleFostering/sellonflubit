<?php

/**
 * Class Flubit Block Admin Widget Grid Column Renderer Inlinecheck
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Adminhtml_Widget_Grid_Column_Renderer_InlineCheckbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    /**
     * method for ggenerating inline checkbox
     * 
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        try {
            $html = parent::render($row);

            $html = '<input type="checkbox" ';
            $html .= 'id="checkbox_' . $row->getId() . '" ';
            $html .= 'name="' . $this->getColumn()->getId() . '" ';
            $html .= ($row->getData($this->getColumn()->getIndex()) == 1 ? 'checked="checked"' : '');
            $html .= 'onclick="updateFlubitPriceCalculation(this, ' . $row->getId() . ', ' . $row->getPrice() . '); return false"/>';
            return $html;
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Widget_Grid_Column_Renderer_InlineCheckbox  render ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}