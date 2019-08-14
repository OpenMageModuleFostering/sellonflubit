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
CREATE TABLE {$installer->getTable('flubit/ordertime')} (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_time` DATETIME NOT NULL,
  `fetched_orders` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);
        
SQLTEXT;

$installer->run($sql);
$installer->endSetup();