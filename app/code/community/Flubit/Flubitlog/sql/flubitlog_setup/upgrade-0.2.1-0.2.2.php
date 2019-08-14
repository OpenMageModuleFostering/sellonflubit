<?php
/**
 * Script for Alter Flubit log Column requestxml and responseml datatype 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();

$installer->run(
"TRUNCATE TABLE {$installer->getTable('flubitlog/flubitlog')} " 
);
$installer->endSetup();