<?php

/**
 * Class Flubit Block Admin Widget Grid Column Renderer Inline
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Adminhtml_Widget_Grid_Column_Renderer_Inline extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    /**
     * function to generate inline price textbox
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        try {
            $html = parent::render($row);

            $html = '<input type="text" ';
            $html .= 'id="price_' . $row->getId() . '" ';
            $html .= 'name="' . $this->getColumn()->getId() . '" ';
            $html .= 'value="' . number_format($row->getData($this->getColumn()->getIndex()), 2, '.', '') . '"';
            $html .= 'class="onenter validate-number input-text ' . $this->getColumn()->getInlineCss() . '" />';
            return $html;
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Widget_Grid_Column_Renderer_Inline  render ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}