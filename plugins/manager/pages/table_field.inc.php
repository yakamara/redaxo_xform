<?php

$table_name = rex_request('table_name',"string");

$page = new rex_xform_manager();
$page->setFilterTable($table_name);
$page->setLinkVars(	array('page'=>'xform','subpage'=>'manager','tripage'=>'table_field') );
echo $page->getFieldPage();

?>