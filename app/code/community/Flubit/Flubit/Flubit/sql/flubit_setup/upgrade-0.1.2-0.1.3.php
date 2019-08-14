<?php

/**
 * Script for Create Flubit feeds log 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table {$this->getTable('flubit/logs')}(flubit_id int not null auto_increment, feed_id varchar(100), feed_type varchar(50) not null, status int(11) not null, created_at DATETIME DEFAULT NULL, primary key(flubit_id));
        
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
     