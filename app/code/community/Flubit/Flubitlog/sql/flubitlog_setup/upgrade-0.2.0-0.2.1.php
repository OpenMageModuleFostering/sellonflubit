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

$installer->run("

ALTER TABLE {$this->getTable('flubitlog/flubitlog')} 
MODIFY COLUMN `request_xml` LONGTEXT  NOT NULL,
MODIFY COLUMN `response_xml` LONGTEXT  NOT NULL;
");

$installer->endSetup();
