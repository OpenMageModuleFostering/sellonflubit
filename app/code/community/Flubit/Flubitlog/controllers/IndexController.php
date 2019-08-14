<?php

/**
 * Class Flubitlog Index Controller 
 * 
 * @package Flubit
 * @category Flubitlog_IndexController
 * @author Flubit team
 */
class Flubit_Flubitlog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();     
		$this->renderLayout();
    }
}

