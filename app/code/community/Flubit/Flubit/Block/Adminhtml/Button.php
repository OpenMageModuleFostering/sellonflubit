<?php



/**

 * Class Flubit Block Admin Button

 * 

 * @package Flubit

 * @category Flubit_Block

 * @author Flubit team

 */

class Flubit_Flubit_Block_Adminhtml_Button extends Mage_Adminhtml_Block_System_Config_Form_Field {



    /**

     * Method for Set Element

     * 

     * @param Varien_Data_Form_Element_Abstract $element

     * @return String

     */

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        try {

            $this->setElement($element);

            $url = $this->getUrl('adminhtml/flubit/generateProductXML/');



            $html = $this->getLayout()->createBlock('adminhtml/widget_button')

                    ->setType('button')

                    ->setClass('scalable')

                    ->setLabel('Sync')

                    ->setOnClick("setLocation('$url')")

                    ->toHtml();



            return $html;

        } catch (Exception $e) {

            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Button  _getElementHtml ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



}

