<?php

/**
 * Class Flubitlog Block Flubitlog Edit Tab
 * 
 * @package Flubit
 * @category Flubitlog_Edit_Tab
 * @author Flubit team
 */
 
class Flubit_Flubitlog_Block_Adminhtml_Flubitlog_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{ 
	/**
	 * Method for Create Prepare Form
	 * 
	 */		
  protected function _prepareForm()
  {
      $model = Mage::registry('flubitlog_data');
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('flubitlog_form', array('legend'=>Mage::helper('flubitlog')->__('Request')));
      $fieldset->addField('request_xml', 'editor', array(
          'name'      => 'request_xml',
          'style'     => 'width:900px; height:250px;resize: none;',
          'wysiwyg'   => false,
          'readonly'  => true,
          'value' => $model['request_xml'],
      ));
      
      $fieldset->addField('response_xml', 'editor', array(
          'name'      => 'response_xml',
         'style'     => 'width:900px; height:250px;resize: none;',
          'wysiwyg'   => false,
          'readonly'  => true,
          'value' => $model['response_xml'],
      ));
	  
	return parent::_prepareForm();
  }
  
   
}