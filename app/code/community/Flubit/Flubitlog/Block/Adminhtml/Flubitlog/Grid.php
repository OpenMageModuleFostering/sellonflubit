<?php

/**
 * Class Flubitlog Block Flubitlog Grid
 * 
 * @package Flubit
 * @category Flubitlog_Grid
 * @author Flubit team
 */
 
class Flubit_Flubitlog_Block_Adminhtml_Flubitlog_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
	/**
    * Construct for autoload and set property
    */
    public function __construct() {
        parent::__construct();
        $this->setId('flubitlogGrid');
        $this->setDefaultSort('flubitlog_id');
		$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
	
	/**
     * Method for Create Prepare Collection 
     * 
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('flubitlog/flubitlog')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
	/**
     * Method for Create Prepare Columns 
     * 
    */
    protected function _prepareColumns() {
        $this->addColumn('flubitlog_id', array(
            'header' => Mage::helper('flubitlog')->__('Log ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'flubitlog_id',
        ));
		
	$this->addColumn('feedid', array(
            'header' => Mage::helper('flubitlog')->__('Feed ID / Flubit Order ID'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'feedid',
        ));

        $this->addColumn('level', array(
            'header' => Mage::helper('log')->__('Level'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'level',
            'type' => 'options',
            'options' => array(
                1 => 'Success',
                2 => 'Error',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('log')->__('Action'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'action',
            'type' => 'options',
            'options' => array(
                1 => 'Create Product',
                2 => 'Update Product',
                9 => 'Delete Product',
                3 => 'Fetch Order',
                4 => 'Order Status Dispatch',
                5 => 'Order Status Refund',
				6 => 'Order Status Cancel',
                7 => 'Create Order Magento',
				8 => 'Communication Error',
                10 => 'Check Feed Response',
            ),
        ));

        $this->addColumn('datetime', array(
            'header' => Mage::helper('log')->__('Created at'),
            'width' => '100px',
            'index' => 'datetime',
            'type' => 'datetime',
					
        ));
	
        return parent::_prepareColumns();
    }
	
	/**
     * Method for Create Flubit logging 
     * 
     * @param data check String $xml
     * @return Xml String
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('flubitlog_id');
        $this->getMassactionBlock()->setFormFieldName('flubitlog');
        $this->getMassactionBlock()->addItem('delete', array(
          'label'    => Mage::helper('flubitlog')->__('Delete'),
          'url'      => $this->getUrl('*/*/massDelete'),
         'confirm'  => Mage::helper('flubitlog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('flubitlog/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        
        return $this;
    }
	
	/**
     * Method for Create Flubit logging 
     * 
     * @param data check String $xml
     * @return Xml String
     */
    public function getRowUrl($row) {

        if ($row['flubitlog_id']) {
            // $Id = Mage::getModel('flubitlog/flubitlog')->getId($row['id']);
            return $this->getUrl('*/*/detail', array('id' => $row['flubitlog_id']));
        }
    }

}