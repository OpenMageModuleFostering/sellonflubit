<?php

/**
 * Script for update product Delivery Standard, Express
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$this->startSetup();

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
 
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_brand')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_brand', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'Brand',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 4,                                                        // eav_entity_attribute.sort_order                     sort order in group        
        'type'                       => 'varchar',                                                // eav_attribute.backend_type                          backend storage type (varchar, text etc)
        'input'                      => 'text',                                                   // eav_attribute.frontend_input                        admin input type (select, text, textarea etc)        
        'required'                   => false,                                                     // eav_attribute.is_required                           required in admin
        'user_defined'               => true,                                                      // eav_attribute.is_user_defined                       editable in admin attributes section, false for not
        'unique'                     => false,                                                    // eav_attribute.is_unique                             unique value required
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,  // catalog_eav_attribute.is_global                     (products only) scope
        'visible'                    => true,                                                     // catalog_eav_attribute.is_visible                    (products only) visible on admin
        'visible_on_front'           => false,                                                    // catalog_eav_attribute.is_visible_on_front           (products only) visible on frontend (store) attribute table
        'used_in_product_listing'    => true,                                                    // catalog_eav_attribute.used_in_product_listing       (products only) made available in product listing
        'searchable'                 => true,                                                    // catalog_eav_attribute.is_searchable                 (products only) searchable via basic search
        'visible_in_advanced_search' => true,                                                    // catalog_eav_attribute.is_visible_in_advanced_search (products only) searchable via advanced search
        'filterable'                 => false,                                                    // catalog_eav_attribute.is_filterable                 (products only) use in layered nav
        'filterable_in_search'       => false,                                                    // catalog_eav_attribute.is_filterable_in_search       (products only) use in search results layered nav
        'comparable'                 => false,                                                    // catalog_eav_attribute.is_comparable                 (products only) comparable on frontend
        'is_html_allowed_on_front'   => false,                                                     // catalog_eav_attribute.is_visible_on_front           (products only) seems obvious, but also see visible
        'apply_to'                   => 'simple,configurable',                                    // catalog_eav_attribute.apply_to                      (products only) which product types to apply to
        'is_configurable'            => false,                                                    // catalog_eav_attribute.is_configurable               (products only) used for configurable products or not
        'used_for_sort_by'           => false,                                                    // catalog_eav_attribute.used_for_sort_by              (products only) available in the 'sort by' menu
        'position'                   => 4,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_standard_delivery')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_standard_delivery', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'Standard Delivery',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 5,                                                        // eav_entity_attribute.sort_order                     sort order in group        
        'type'                       => 'varchar',                                                // eav_attribute.backend_type                          backend storage type (varchar, text etc)
        'input'                      => 'text',                                                   // eav_attribute.frontend_input                        admin input type (select, text, textarea etc)        
        'required'                   => false,                                                     // eav_attribute.is_required                           required in admin
        'user_defined'               => true,                                                      // eav_attribute.is_user_defined                       editable in admin attributes section, false for not
        'unique'                     => false,                                                    // eav_attribute.is_unique                             unique value required
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,  // catalog_eav_attribute.is_global                     (products only) scope
        'visible'                    => true,                                                     // catalog_eav_attribute.is_visible                    (products only) visible on admin
        'visible_on_front'           => false,                                                    // catalog_eav_attribute.is_visible_on_front           (products only) visible on frontend (store) attribute table
        'used_in_product_listing'    => true,                                                    // catalog_eav_attribute.used_in_product_listing       (products only) made available in product listing
        'searchable'                 => true,                                                    // catalog_eav_attribute.is_searchable                 (products only) searchable via basic search
        'visible_in_advanced_search' => true,                                                    // catalog_eav_attribute.is_visible_in_advanced_search (products only) searchable via advanced search
        'filterable'                 => false,                                                    // catalog_eav_attribute.is_filterable                 (products only) use in layered nav
        'filterable_in_search'       => false,                                                    // catalog_eav_attribute.is_filterable_in_search       (products only) use in search results layered nav
        'comparable'                 => false,                                                    // catalog_eav_attribute.is_comparable                 (products only) comparable on frontend
        'is_html_allowed_on_front'   => false,                                                     // catalog_eav_attribute.is_visible_on_front           (products only) seems obvious, but also see visible
        'apply_to'                   => 'simple,configurable',                                    // catalog_eav_attribute.apply_to                      (products only) which product types to apply to
        'is_configurable'            => false,                                                    // catalog_eav_attribute.is_configurable               (products only) used for configurable products or not
        'used_for_sort_by'           => false,                                                    // catalog_eav_attribute.used_for_sort_by              (products only) available in the 'sort by' menu
        'position'                   => 5,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_express_delivery')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_express_delivery', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'Express Delivery',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 6,                                                        // eav_entity_attribute.sort_order                     sort order in group        
        'type'                       => 'varchar',                                                // eav_attribute.backend_type                          backend storage type (varchar, text etc)
        'input'                      => 'text',                                                   // eav_attribute.frontend_input                        admin input type (select, text, textarea etc)        
        'required'                   => false,                                                     // eav_attribute.is_required                           required in admin
        'user_defined'               => true,                                                      // eav_attribute.is_user_defined                       editable in admin attributes section, false for not
        'unique'                     => false,                                                    // eav_attribute.is_unique                             unique value required
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,  // catalog_eav_attribute.is_global                     (products only) scope
        'visible'                    => true,                                                     // catalog_eav_attribute.is_visible                    (products only) visible on admin
        'visible_on_front'           => false,                                                    // catalog_eav_attribute.is_visible_on_front           (products only) visible on frontend (store) attribute table
        'used_in_product_listing'    => true,                                                    // catalog_eav_attribute.used_in_product_listing       (products only) made available in product listing
        'searchable'                 => true,                                                    // catalog_eav_attribute.is_searchable                 (products only) searchable via basic search
        'visible_in_advanced_search' => true,                                                    // catalog_eav_attribute.is_visible_in_advanced_search (products only) searchable via advanced search
        'filterable'                 => false,                                                    // catalog_eav_attribute.is_filterable                 (products only) use in layered nav
        'filterable_in_search'       => false,                                                    // catalog_eav_attribute.is_filterable_in_search       (products only) use in search results layered nav
        'comparable'                 => false,                                                    // catalog_eav_attribute.is_comparable                 (products only) comparable on frontend
        'is_html_allowed_on_front'   => false,                                                     // catalog_eav_attribute.is_visible_on_front           (products only) seems obvious, but also see visible
        'apply_to'                   => 'simple,configurable',                                    // catalog_eav_attribute.apply_to                      (products only) which product types to apply to
        'is_configurable'            => false,                                                    // catalog_eav_attribute.is_configurable               (products only) used for configurable products or not
        'used_for_sort_by'           => false,                                                    // catalog_eav_attribute.used_for_sort_by              (products only) available in the 'sort by' menu
        'position'                   => 6,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_base_price')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_base_price', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'Flubit Base Price',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 7,                                                        // eav_entity_attribute.sort_order                     sort order in group        
        'type'                       => 'varchar',                                                // eav_attribute.backend_type                          backend storage type (varchar, text etc)
        'input'                      => 'text',                                                   // eav_attribute.frontend_input                        admin input type (select, text, textarea etc)        
        'required'                   => false,                                                     // eav_attribute.is_required                           required in admin
        'user_defined'               => true,                                                      // eav_attribute.is_user_defined                       editable in admin attributes section, false for not
        'unique'                     => false,                                                    // eav_attribute.is_unique                             unique value required
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,  // catalog_eav_attribute.is_global                     (products only) scope
        'visible'                    => true,                                                     // catalog_eav_attribute.is_visible                    (products only) visible on admin
        'visible_on_front'           => false,                                                    // catalog_eav_attribute.is_visible_on_front           (products only) visible on frontend (store) attribute table
        'used_in_product_listing'    => true,                                                    // catalog_eav_attribute.used_in_product_listing       (products only) made available in product listing
        'searchable'                 => true,                                                    // catalog_eav_attribute.is_searchable                 (products only) searchable via basic search
        'visible_in_advanced_search' => true,                                                    // catalog_eav_attribute.is_visible_in_advanced_search (products only) searchable via advanced search
        'filterable'                 => false,                                                    // catalog_eav_attribute.is_filterable                 (products only) use in layered nav
        'filterable_in_search'       => false,                                                    // catalog_eav_attribute.is_filterable_in_search       (products only) use in search results layered nav
        'comparable'                 => false,                                                    // catalog_eav_attribute.is_comparable                 (products only) comparable on frontend
        'is_html_allowed_on_front'   => false,                                                     // catalog_eav_attribute.is_visible_on_front           (products only) seems obvious, but also see visible
        'apply_to'                   => 'simple,configurable',                                    // catalog_eav_attribute.apply_to                      (products only) which product types to apply to
        'is_configurable'            => false,                                                    // catalog_eav_attribute.is_configurable               (products only) used for configurable products or not
        'used_for_sort_by'           => false,                                                    // catalog_eav_attribute.used_for_sort_by              (products only) available in the 'sort by' menu
        'position'                   => 7,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
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