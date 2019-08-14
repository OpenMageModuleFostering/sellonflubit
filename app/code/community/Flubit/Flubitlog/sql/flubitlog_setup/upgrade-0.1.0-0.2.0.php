<?php
/**
 * Script for Alter Flubit log Column feedid 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();


$installer->run("

ALTER TABLE {$this->getTable('flubitlog/flubitlog')} ADD `feedid` VARCHAR( 50 ) NOT NULL default '';

    ");

$installer->endSetup();
