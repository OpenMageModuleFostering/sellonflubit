<?php

/**
 * Script for Create Flubit feeds log 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;
$installer->startSetup();

$installer->run(
"DELETE FROM {$installer->getTable('flubit/order')} where refund=1;" 
);
$installer->run(
"TRUNCATE TABLE {$installer->getTable('flubit/logs')} " 
);

$installer->endSetup();
