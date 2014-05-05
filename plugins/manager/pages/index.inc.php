<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$tripage = rex_request('tripage', 'string');
$table_name = rex_request('table_name', 'string');

switch ($tripage) {
    case 'table_field':
        rex_title('XForm', $REX['ADDON']['xform']['SUBPAGES']);
        require $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/pages/table_field.inc.php';
        break;

    case 'table_import':
        // TODO:
        rex_title('XForm', $REX['ADDON']['xform']['SUBPAGES']);
        echo 'TODO:';
        require $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/pages/table_import.inc.php';
        break;

    case 'data_edit':
        require $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/pages/data_edit.inc.php';
        break;

    default:
        rex_title('XForm', $REX['ADDON']['xform']['SUBPAGES']);
        require $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/pages/table_edit.inc.php';
}
