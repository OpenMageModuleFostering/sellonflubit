<?php

/**
 * Class admin Flubit Grid
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Adminhtml_Flubit_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * 
     * Construct for autoload and set property
     */
    public function __construct() {
        try {
            parent::__construct();
            $this->setId('flubitGrid');
            $this->setDefaultSort('flubit_id');
            $this->setDefaultDir('DESC');
            $this->setSaveParametersInSession(true);
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit_Grid  __construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * 
     * private method for prepare collection
     */
    protected function _prepareCollection() {
        try {
            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'price');
            $entity_id = $attributeModel->attribute_id;

            $collection = Mage::getModel('flubit/flubit')->getCollection();

            $this->setCollection($collection);
            //custom code
            $collection->getSelect()->join(array('product_entity' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity'), 'product_entity.sku = main_table.sku', array('product_entity.entity_id'));

            $collection->getSelect()->join(array('product_price' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_decimal'), 'product_price.entity_id = product_entity.entity_id and product_price.attribute_id = ' . $entity_id, array('mprice' => 'ROUND(product_price.`value`,2)'));
            ;

            return parent::_prepareCollection();
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit_Grid  _prepareCollection ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

    /**
     * 
     * private method for prepare Columns
     */
    protected function _prepareColumns() {
        $this->addColumn('flubit_id', array(
            'header' => Mage::helper('flubit')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'flubit_id',
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('flubit')->__('SKU'),
            'align' => 'left',
            'index' => 'sku',
            'width' => '200',
            'filter_index' => 'main_table.sku'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('flubit')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));
        $this->addColumn('active_status', array(
            'header' => Mage::helper('flubit')->__('Flubit Status'),
            'align' => 'left',
            'index' => 'active_status',
            'type' => 'options',
            'options' => array(
                0 => 'Inactive',
                1 => 'Active',
            ),
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('flubit')->__('Qty'),
            'width' => '150px',
            'index' => 'qty',
        ));
        $this->addColumn('mprice', array(
            'header' => Mage::helper('flubit')->__('Product Price'),
            'width' => '150px',
            'index' => 'mprice',
            'filter_index' => 'product_price.`value`'
        ));
        $this->addColumn('price', array(
            'header' => Mage::helper('flubit')->__('Flubit Price'),
            'width' => '150px',
            'renderer' => 'flubit/adminhtml_widget_grid_column_renderer_inline',
            'index' => 'price',
        ));
        $this->addColumn('use_global_price', array(
            'header' => Mage::helper('flubit')->__('Use Global Price'),
            'width' => '60px',
            'align' => 'center',
            'renderer' => 'flubit/adminhtml_widget_grid_column_renderer_inlineCheckbox',
            'index' => 'use_global_price',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('flubit')->__('Action'),
            'width' => '100',
            'align' => 'center',
            'renderer' => 'flubit/adminhtml_widget_grid_column_renderer_inlineUpdateButton',
            'filter' => false,
            'sortable' => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Method for Get Row Url
     * 
     * @param Mixed $row
     * @return String
     */
    public function getRowUrl($row) {
        try {
            if ($row['sku']) {
                $productId = Mage::getModel('catalog/product')->getIdBySku($row['sku']);
                return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $productId));
            }
        } catch (Exception $e) {
            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit_Grid  _prepareCollection ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);
        }
    }

}