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


$installer->run("

ALTER TABLE {$installer->getTable('flubit/order')} ADD (`dispatch` tinyint ( 4 ) NOT NULL default '0',`refund` tinyint ( 4 ) NOT NULL default '0');

    ");

$installer->endSetup();
