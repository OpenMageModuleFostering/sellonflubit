<?php



/**

 * Script for Create Flubit feeds log 

 *

 * @package Flubit

 * @category Flubit_Sql

 * @author Flubit team

 */

$this->startSetup();
$sql = <<<SQLTEXT

alter table {$this->getTable('flubit/flubit')} add column user_disabled tinyint(1) not null DEFAULT 0 after is_deleted  ;

SQLTEXT;

$this->run($sql);

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup'); 

if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_base_price')) {
	$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_base_price');
}

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_upc')) {

    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_upc', array(         

        'label'                      => 'UPC',                                                 

        'group'                      => 'Flubit',                                              

        'sort_order'                 => 1,                                                       

        'type'                       => 'varchar',                                               

        'input'                      => 'text',                                                  

        'required'                   => false,                                                    

        'user_defined'               => true,                                                   

        'unique'                     => true,                                                   

        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 

        'visible'                    => true,                                                    

        'visible_on_front'           => false,                                                    

        'used_in_product_listing'    => false,                                                    

        'searchable'                 => false,                                                    

        'visible_in_advanced_search' => false,                                                   

        'filterable'                 => false,                                                   

        'filterable_in_search'       => false,                                                    

        'comparable'                 => false,                                                   

        'is_html_allowed_on_front'   => false,                                                    

        'apply_to'                   => 'simple,configurable',                                    

        'is_configurable'            => false,                                                   

        'used_for_sort_by'           => false,                                                    

        'position'                   => 1,                                                       

        'used_for_promo_rules'       => false,                                                   

    ));

}

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'flubit_order_id',
    'smallint(6) DEFAULT NULL'
);

// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'flubit_order_id',
    'flubit_order_id'
);

// Now you need to fullfill existing rows with data from address table

$select = $this->getConnection()->select();
$select->join(
    array('order'=>$this->getTable('sales/order')),
    $this->getConnection()->quoteInto(
        'order.entity_id = order_grid.entity_id'
    ),
    array('flubit_order_id' => 'flubit_order_id')
);
$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);

$this->endSetup();