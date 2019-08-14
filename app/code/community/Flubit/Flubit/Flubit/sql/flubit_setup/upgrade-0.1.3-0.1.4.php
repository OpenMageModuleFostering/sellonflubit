<?php

/**
 * Script for Create Flubit feeds log 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$this->startSetup();

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup'); 

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_product')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_product', array(
        'label'                      => 'Flubit Product',
        'group'                      => 'General',
        'sort_order'                 => 99,
        'type'                       => 'int',
        'input'                      => 'boolean',
        'source'                     => 'eav/entity_attribute_source_table',
        'required'                   => false, 
        'user_defined'               => true,  
        'unique'                     => false, 
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                    => true,
        'visible_on_front'           => false,
        'used_in_product_listing'    => true, 
        'searchable'                 => false,
        'visible_in_advanced_search' => false,
        'filterable'                 => false,
        'filterable_in_search'       => false,
        'comparable'                 => false,
        'is_html_allowed_on_front'   => false,
        'apply_to'                   => 'simple,configurable',
        'is_configurable'            => false,
        'used_for_sort_by'           => false,
        'position'                   => 99,
        'used_for_promo_rules'       => false,
    ));
}