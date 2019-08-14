<?php

/**
 *  Script for update product Catalog
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$this->startSetup();

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
 
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_ean')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_ean', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'EAN',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 0,                                                        // eav_entity_attribute.sort_order                     sort order in group        
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
        'position'                   => 0,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_asin')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_asin', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'ASIN',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 1,                                                        // eav_entity_attribute.sort_order                     sort order in group        
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
        'position'                   => 1,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_isbn')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_isbn', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'ISBN',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 2,                                                        // eav_entity_attribute.sort_order                     sort order in group        
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
        'position'                   => 2,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'flubit_mpn')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'flubit_mpn', array(         // TABLE.COLUMN:                                       DESCRIPTION:
        'label'                      => 'MPN',                                                  // eav_attribute.frontend_label                        admin input label
        'group'                      => 'Flubit',                                                // (not a column)                                      tab in product edit screen
        'sort_order'                 => 3,                                                        // eav_entity_attribute.sort_order                     sort order in group        
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
        'position'                   => 3,                                                        // catalog_eav_attribute.position                      (products only) position in layered naviagtion
        'used_for_promo_rules'       => false,                                                    // catalog_eav_attribute.is_used_for_promo_rules       (products only) available for use in promo rules
    ));
}