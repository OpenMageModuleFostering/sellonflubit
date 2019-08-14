<?php

/**
 * Script for Alter Flubit product update add Global Price
 * 
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
alter table {$this->getTable('flubit/flubit')} add column use_global_price char(1) null DEFAULT 0 after status  ;
        
SQLTEXT;

$installer->run($sql);
$installer->endSetup();