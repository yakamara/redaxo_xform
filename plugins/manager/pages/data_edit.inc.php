<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$table_name = rex_request('table_name', 'string');

if ($table_name != '' && $REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm('xform[table:' . $table_name . ']')) ) {

  $page = new rex_xform_manager();
  $page->setFilterTable($table_name);
  $page->setLinkVars(  array('page' => 'xform', 'subpage' => 'manager', 'tripage' => 'data_edit', 'table_name' => $table_name)  );
  echo $page->getDataPage();

}
