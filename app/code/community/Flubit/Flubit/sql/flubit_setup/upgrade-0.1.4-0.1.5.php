<?php

/**
 * Script for Flubit Product Update 
 * 
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
alter table {$this->getTable('flubit/flubit')} add column status varchar(50) null after qty;
        
SQLTEXT;

$installer->run($sql);
$installer->endSetup();
     