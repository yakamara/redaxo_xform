<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$table_name = rex_request('table_name', 'string');
$table = rex_xform_manager_table::get($table_name);

if ($table && $REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm('xform[table:' . $table_name . ']')) ) {

    try {

        $page = new rex_xform_manager();
        $page->setTable($table);
        $page->setLinkVars( array('page' => 'xform', 'subpage' => 'manager', 'tripage' => 'data_edit', 'table_name' => $table->getTableName()) );
        echo $page->getDataPage();

    } catch (Exception $e) {
      echo rex_warning($I18N->msg('xform_table_not_found'));

    }

}
