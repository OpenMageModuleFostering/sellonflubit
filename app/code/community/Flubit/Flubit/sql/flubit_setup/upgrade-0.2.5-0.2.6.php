<?php

/**
 * Script for Create Flubit Order 
 * 
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
create table {$this->getTable('flubit/globalproduct')}(id int not null auto_increment, product_id int(20) not null, created_at DATETIME DEFAULT NULL, flubit_status int(11) not null, update_status int(11) not null, primary key(id));
        
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();

