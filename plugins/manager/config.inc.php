<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['xform']['classpaths']['action'][] = $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/classes/action/';
// $REX['ADDON']['xform']['classpaths']['validate'][] = $REX["INCLUDE_PATH"]."/addons/xform/plugins/manager/classes/validate/";
$REX['ADDON']['xform']['classpaths']['value'][] = $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/classes/value/';

$REX['ADDON']['xform']['templatepaths'][] = $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/templates/';

include $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/classes/basic/class.rex_xform_manager.inc.php';
include $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/classes/basic/class.rex_xform_manager_table.inc.php';

$mypage = 'manager';

if ($REX['REDAXO'] && !$REX['SETUP']) {
    $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/lang/');

    // $REX['ADDON']['name'][$mypage] = $I18N->msg("xform_table_manager");
    $REX['ADDON']['version'][$mypage] = '4.5.1';
    $REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
    $REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';
    $REX['ADDON']['navigation'][$mypage] = array(
        // rootPage nur aktivieren wenn sie direkt ausgewaehlt ist
        // da alle pages main-pages und daher separate oberpunkte sind
            'activateCondition' => array('page' => $mypage, 'subpage' => 'manager'),
        'hidden' => false
    );

    if ($REX['USER'] && !$REX['USER']->isAdmin()) {
            $REX['ADDON']['navigation'][$mypage]['hidden'] = true;
        }

    $REX['ADDON']['xform']['SUBPAGES'][] = array('manager' , $I18N->msg('xform_table_manager'));

    // Register EP for Mediapool File-Delete
    rex_register_extension('OOMEDIA_IS_IN_USE', 'rex_xform_manager::checkMediaInUse');

    $t = new rex_xform_manager();
    $tables = $t->getTables();

    $subpages = array();
    if (is_array($tables)) {
        foreach ($tables as $table) {
            // Recht um das AddOn ueberhaupt einsehen zu koennen
            $table_perm = 'xform[table:' . $table['table_name'] . ']';
            $REX['EXTPERM'][] = $table_perm;

            // check active-state and permissions
            if ($table['status'] == 1 && $table['hidden'] != 1 && $REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm))) {
                $table_name = rex_translate($table['name']);

                if ($I18N) {
                    $I18N->addMsg($table['table_name'], $table_name);
                }

                $be_page = new rex_be_page($table_name, array('page' => 'xform', 'subpage' => 'manager', 'tripage' => 'data_edit', 'table_name' => $table['table_name']));
                $be_page->setHref('index.php?page=xform&subpage=manager&tripage=data_edit&table_name=' . $table['table_name']);
                $subpages[] = new rex_be_main_page($mypage, $be_page);
            }
        }
    }
    $REX['ADDON']['pages'][$mypage] = $subpages;

    // hack - if data edit, then deactivate xform navigation
    if (rex_request('tripage', 'string') == 'data_edit') {
        $REX['ADDON']['navigation']['xform'] = array(
            'activateCondition' => array('page' => 'xformmm'),
            'hidden' => false
        );
    }


}
