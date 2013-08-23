<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$sql = rex_sql::factory();

$sql->setQuery('CREATE TABLE IF NOT EXISTS `'.$REX['TABLE_PREFIX'].'xform_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `list_amount` tinyint(3) unsigned NOT NULL DEFAULT 50,
  `prio` int(11) NOT NULL,
  `search` tinyint(4) NOT NULL,
  `hidden` tinyint(4) NOT NULL,
  `export` tinyint(4) NOT NULL,
  `import` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE(`table_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

$sql->setQuery('ALTER TABLE `'.$REX['TABLE_PREFIX'].'xform_table` CHANGE `prio` `prio` INT NOT NULL');

$sql->setQuery('CREATE TABLE IF NOT EXISTS `'.$REX['TABLE_PREFIX'].'xform_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `prio` int(11) NOT NULL,
  `type_id` varchar(100) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `f1` text NOT NULL,
  `f2` text NOT NULL,
  `f3` text NOT NULL,
  `f4` text NOT NULL,
  `f5` text NOT NULL,
  `f6` text NOT NULL,
  `f7` text NOT NULL,
  `f8` text NOT NULL,
  `f9` text NOT NULL,
  `list_hidden` tinyint(4) NOT NULL,
  `search` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

$sql->setQuery('ALTER TABLE `'.$REX['TABLE_PREFIX'].'xform_field` CHANGE `prio` `prio` INT NOT NULL');

$sql->setQuery('CREATE TABLE IF NOT EXISTS `'.$REX['TABLE_PREFIX'].'xform_relation` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `source_table` VARCHAR( 100 ) NOT NULL ,
  `source_name` VARCHAR( 100 ) NOT NULL ,
  `source_id` INT NOT NULL ,
  `target_table` VARCHAR( 100 ) NOT NULL ,
  `target_id` INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

$REX['ADDON']['install']['manager'] = 1;
