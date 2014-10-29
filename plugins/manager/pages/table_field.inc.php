<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$table_name = rex_request('table_name', 'string');
$table = rex_xform_manager_table::get($table_name);

if ($table) {

    try {

        $page = new rex_xform_manager();
        $page->setTable($table);
        $page->setLinkVars(  array('page' => 'xform', 'subpage' => 'manager', 'tripage' => 'table_field') );
        echo $page->getFieldPage();

    } catch (Exception $e) {

        echo rex_warning($I18N->msg('xform_table_not_found'));

    }

}
