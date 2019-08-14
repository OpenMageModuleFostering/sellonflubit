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

"ALTER IGNORE TABLE {$installer->getTable('flubit/flubit')} ADD UNIQUE INDEX(sku)" 

);




$installer->endSetup();
