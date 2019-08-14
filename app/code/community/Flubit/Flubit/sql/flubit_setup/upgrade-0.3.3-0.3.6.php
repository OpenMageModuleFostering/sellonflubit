<?php



/**

 * Script for Create Flubit feeds log 

 *

 * @package Flubit

 * @category Flubit_Sql

 * @author Flubit team

 */

$this->startSetup();
$sql = <<<SQLTEXT

alter table {$this->getTable('flubit/flubit')} add column available_from datetime null DEFAULT '0000-00-00 00:00:00' after is_deleted  ;

SQLTEXT;

$this->run($sql);


$this->endSetup();