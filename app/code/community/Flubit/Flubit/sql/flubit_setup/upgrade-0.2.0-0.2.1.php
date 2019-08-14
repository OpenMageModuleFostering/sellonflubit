<?php

/**
 * Script for Alter Flubit product Column acrtive status or not 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
alter table {$installer->getTable('flubit/flubit')} add column active_status tinyint(1) not null DEFAULT 1 after new  ;
        
SQLTEXT;

$installer->run($sql);
$installer->endSetup();