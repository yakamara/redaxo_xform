<?php


$REX['ADDON']['install']['manager'] = 0;

$sql = rex_sql::factory();
$sql->setQuery('DROP TABLE `'.$REX['TABLE_PREFIX'].'xform_table`;');
$sql->setQuery('DROP TABLE `'.$REX['TABLE_PREFIX'].'xform_field`;');
$sql->setQuery('DROP TABLE `'.$REX['TABLE_PREFIX'].'xform_relation`;');
