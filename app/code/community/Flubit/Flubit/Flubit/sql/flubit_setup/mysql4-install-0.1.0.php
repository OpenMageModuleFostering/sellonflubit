<?php

/**
 * Script for update product in flubit 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
create table {$this->getTable('flubit/flubit')}(flubit_id int not null auto_increment, name varchar(100), sku varchar(255) not null, price varchar(255) not null, qty int not null, primary key(flubit_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();

