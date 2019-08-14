<?php

/**
 * Script for Alter Flubit product Column isdeleted or not
 * 
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
alter table {$this->getTable('flubit/flubit')} add column is_deleted tinyint(1) not null DEFAULT 0 after status  ;
        
SQLTEXT;

$installer->run($sql);
$installer->endSetup();