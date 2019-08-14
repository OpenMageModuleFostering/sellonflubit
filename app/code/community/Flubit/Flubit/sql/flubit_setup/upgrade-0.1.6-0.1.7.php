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
create table {$this->getTable('flubit/order')}(flubit_id int not null auto_increment, order_no varchar(20) null, flubit_order_id varchar(20) null, status varchar(20) null, primary key(flubit_id));
        
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();

