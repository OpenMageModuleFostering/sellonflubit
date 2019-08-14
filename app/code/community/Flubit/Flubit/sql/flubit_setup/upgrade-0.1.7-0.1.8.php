<?php

/**
 * Script for Alter Flubit product update add new column
 * 
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
alter table {$this->getTable('flubit/flubit')} add column new char(5) null after status;
        
SQLTEXT;

$installer->run($sql);
$installer->endSetup();