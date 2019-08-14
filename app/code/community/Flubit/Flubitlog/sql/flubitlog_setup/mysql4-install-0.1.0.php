<?php
/**
 * Script for Error logging in flubit 
 *
 * @package Flubit
 * @category Flubit_Sql
 * @author Flubit team
 */
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('flubitlog/flubitlog')} ;
CREATE TABLE {$this->getTable('flubitlog/flubitlog')} (
  `flubitlog_id` int(11) unsigned NOT NULL auto_increment,
  `request_xml` varchar(255) NOT NULL default '',
  `response_xml` varchar(255) NOT NULL default '',
  `action` int(11) NULL ,
  `datetime` datetime NULL default '0000-00-00 00:00:00',
  `level` int (11) NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`flubitlog_id`)
);
");

$installer->endSetup(); 