<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// ********************************************* TABLE ADD/EDIT/LIST

$table = $REX['TABLE_PREFIX'] . 'xform_table';
$table_field = $REX['TABLE_PREFIX'] . 'xform_field';

$func = rex_request('func', 'string', '');
$page = rex_request('page', 'string', '');
$subpage = rex_request('subpage', 'string', '');
$table_id = rex_request('table_id', 'int');
$table_name = rex_request('table_name', 'string');

$show_list = true;



if ( $func == 'migrate' && $REX['USER']->isAdmin() ) {

  $available_tables = rex_sql::showTables();
  $xform_tables = array();
  $missing_tables = array();

  foreach (rex_xform_manager_table_api::getTables() as $g_table) {
    $xform_tables[] = $g_table['table_name'];
  }

  foreach ($available_tables as $a_table) {
    if ( !in_array($a_table, $xform_tables)) {
      $missing_tables[$a_table] = $a_table;
    }

  }

  $xform = new rex_xform;
  $xform->setDebug(true);
  $xform->setHiddenField('page', $page);
  $xform->setHiddenField('subpage', $subpage);
  $xform->setHiddenField('func', $func);

  $xform->setValueField('select', array('table_name', $I18N->msg('table'), $missing_tables));

  $xform->setValueField('checkbox', array('convert_id', $I18N->msg('xform_manager_migrate_table_id_convert')));

  $form = $xform->getForm();


  if ($xform->objparams['form_show']) {

    echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('xform_manager_table_migrate') . '</h3>
    <div class="rex-addon-content">
    <p>' . $I18N->msg('xform_manager_table_migrate_info') . '</p>';

    echo $form;
    echo '</div></div>';

    echo rex_content_block('<a href="index.php?page=' . $page . '&amp;subpage=' . $subpage . '"><b>&laquo; ' . $I18N->msg('xform_back_to_overview') . '</b></a>');

    $show_list = false;

  } else {

    $table_name = $xform->objparams['value_pool']['sql']['table_name'];
    $convert_id = $xform->objparams['value_pool']['sql']['convert_id'];

    try {
      rex_xform_manager_table_api::migrateTable($table_name, $convert_id); // with convert id / auto_increment finder
      echo rex_info($I18N->msg('xform_manager_table_migrated_success'));

    } catch (Exception $e) {
      echo rex_warning($I18N->msg('xform_manager_table_migrated_failed', $table_name, $e->getMessage()));

    }

  }

} elseif ( ($func == 'add' || $func == 'edit') && $REX['USER']->isAdmin() ) {

    $xform = new rex_xform;
    // $xform->setDebug(TRUE);
    $xform->setHiddenField('page', $page);
    $xform->setHiddenField('subpage', $subpage);
    $xform->setHiddenField('func', $func);
    $xform->setActionField('showtext', array('', $I18N->msg('xform_manager_table_entry_saved')));
    $xform->setObjectparams('main_table', $table);

    $xform->setValueField('prio', array('prio', $I18N->msg('xform_manager_table_prio'), 'name'));

    if ($func == 'edit') {
        $xform->setObjectparams('submit_btn_label', $I18N->msg('save'));
        $xform->setValueField('showvalue', array('table_name', $I18N->msg('xform_manager_table_name')));
        $xform->setHiddenField('table_id', $table_id);
        $xform->setActionField('db', array($table, "id=$table_id"));
        $xform->setObjectparams('main_id', $table_id);
        $xform->setObjectparams('main_where', "id=$table_id");
        $xform->setObjectparams('getdata', true); // Datein vorher auslesen

    } elseif ($func == 'add') {
        $xform->setObjectparams('submit_btn_label', $I18N->msg('add'));
        $xform->setValueField('text', array('table_name', $I18N->msg('xform_manager_table_name'), $REX['TABLE_PREFIX']));
        $xform->setValidateField('empty', array('table_name', $I18N->msg('xform_manager_table_enter_name')));
        $xform->setValidateField('customfunction', array('table_name', '!rex_xform_manager_table::xform_checkTableName', '', $I18N->msg('xform_manager_table_enter_specialchars')));
        $xform->setValidateField('customfunction', array('table_name', 'rex_xform_manager_table::xform_existTableName', '', $I18N->msg('xform_manager_table_exists')));
        $xform->setActionField('wrapper_value', array('table_name', '###value###')); // Tablename
        $xform->setActionField('db', array($table));

    }

    $xform->setValueField('text', array('name', $I18N->msg('xform_manager_name')));
    $xform->setValidateField('empty', array('name', $I18N->msg('xform_manager_table_enter_name')));

    $xform->setValueField('textarea', array('description', $I18N->msg('xform_manager_table_description')));
    $xform->setValueField('checkbox', array('status', $I18N->msg('tbl_active')));
    // $xform->setValueField("fieldset",array("fs-list","Liste"));
    $xform->setValueField('text', array('list_amount', $I18N->msg('xform_manager_entries_per_page'), '50'));
    $xform->setValidateField('type', array('list_amount', 'int', $I18N->msg('xform_manager_enter_number')));

    $sortFields = array('id');
    if ($func === 'edit') {
        $sortFieldsSql = rex_sql::factory();
        $sortFieldsSql->setQuery('SELECT f.name FROM `' . $REX['TABLE_PREFIX'] . 'xform_field` f LEFT JOIN `' . $REX['TABLE_PREFIX'] . 'xform_table` t ON f.table_name = t.table_name WHERE t.id = ' . (int) $table_id . ' ORDER BY f.prio');
        while ($sortFieldsSql->hasNext()) {
            $sortFields[] = $sortFieldsSql->getValue('name');
            $sortFieldsSql->next();
        }
    }
    $xform->setValueField('select' , array('list_sortfield', $I18N->msg('xform_manager_sortfield'), implode(',', $sortFields)));
    $xform->setValueField('select', array('list_sortorder', $I18N->msg('xform_manager_sortorder'), array(
        'ASC' => $I18N->msg('xform_manager_sortorder_asc'),
        'DESC' => $I18N->msg('xform_manager_sortorder_desc'),
    )));

    $xform->setValueField('checkbox', array('search', $I18N->msg('xform_manager_search_active')));

    $xform->setValueField('checkbox', array('hidden', $I18N->msg('xform_manager_table_hide')));
    $xform->setValueField('checkbox', array('export', $I18N->msg('xform_manager_table_allow_export')));
    $xform->setValueField('checkbox', array('import', $I18N->msg('xform_manager_table_allow_import')));

    $form = $xform->getForm();

    if ($xform->objparams['form_show']) {
        if ($func == 'edit') {
            echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('xform_manager_edit_table') . '</h3><div class="rex-addon-content">';
        } else {
            echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('xform_manager_add_table') . '</h3><div class="rex-addon-content">';
        }
        echo $form;
        echo '</div></div>';
        echo rex_content_block('<a href="index.php?page=' . $page . '&amp;subpage=' . $subpage . '"><b>&laquo; ' . $I18N->msg('xform_back_to_overview') . '</b></a>');
        $show_list = false;
    } else {
        if ($func == 'edit') {
            echo rex_info($I18N->msg('xform_manager_table_updated'));
        } elseif ($func == 'add') {

            $table_name = $xform->objparams['value_pool']['sql']['table_name'];
            $t = new rex_xform_manager();
            $t->setFilterTable($table_name);
            $t->generateAll();
            echo rex_info($I18N->msg('xform_manager_table_added'));
        }
    }

}





// ********************************************* LOESCHEN
if ($func == 'delete' && $REX['USER']->isAdmin()) {

    echo rex_xform_manager_table_api::removeTable($table_name);

    $func = '';
    echo rex_info($I18N->msg('xform_manager_table_deleted'));
}





// ********************************************* LISTE
if ($show_list && $REX['USER']->isAdmin()) {

    // formatting func fuer status col
    function rex_xform_status_col($params)
    {
        global $I18N;
        $list = $params['list'];
        return $list->getValue('status') == 1 ? '<span style="color:green;">' . $I18N->msg('xform_tbl_active') . '</span>' : '<span style="color:red;">' . $I18N->msg('xform_tbl_inactive') . '</span>';
    }

    function rex_xform_list_translate($params)
    {
      global $I18N;
      return rex_translate($params['subject']);
    }

  $table_echo = '<a href=index.php?page=' . $page . '&subpage=' . $subpage . '&func=add><b>+ ' . $I18N->msg('xform_manager_table_add') . '</b></a>';
  $table_echo .= ' / <a href=index.php?page=' . $page . '&subpage=' . $subpage . '&func=migrate><b>+ ' . $I18N->msg('xform_manager_table_migrate') . '</b></a>';
    echo rex_content_block($table_echo);

    $sql = "select id, prio, name, table_name, status from $table order by prio,table_name";

    $list = rex_list::factory($sql, 30);

    $list->removeColumn('id');

    $list->setColumnLabel('prio', $I18N->msg('xform_manager_table_prio_short'));
    $list->setColumnLabel('name', $I18N->msg('xform_manager_name'));
    $list->setColumnFormat('name', 'custom', 'rex_xform_list_translate');


    $list->setColumnLabel('table_name', $I18N->msg('xform_manager_table_name'));
    $list->setColumnLabel('status', $I18N->msg('xform_manager_table_status'));

    $list->setColumnFormat('status', 'custom', 'rex_xform_status_col');
    $list->setColumnParams('table_name', array('table_id' => '###id###', 'func' => 'edit'));

    $list->addColumn($I18N->msg('edit'), $I18N->msg('edit'));
    $list->setColumnParams($I18N->msg('edit'), array('table_id' => '###id###', 'func' => 'edit'));

    $list->addColumn($I18N->msg('delete'), $I18N->msg('delete'));
    $list->setColumnParams($I18N->msg('delete'), array('table_name' => '###table_name###', 'func' => 'delete'));
    $list->addLinkAttribute($I18N->msg('delete'), 'onclick', 'return confirm(\' [###table_name###] ' . $I18N->msg('delete') . ' ?\')');

    $list->addColumn($I18N->msg('editfields'), $I18N->msg('editfields'));
    $list->setColumnParams($I18N->msg('editfields'), array('subpage' => 'manager', 'tripage' => 'table_field', 'table_name' => '###table_name###'));


    echo $list->get();
}


// ********************************************* LISTE OF TABLES TO EDIT FOR NOt ADMINS

if (!$REX['USER']->isAdmin()) {
    echo '<div class="rex-addon-output">';
    echo '<h2 class="rex-hl2">' . $I18N->msg('xform_table_overview') . '</h2>';
    echo '<div class="rex-addon-content"><ul>';

    $tables = rex_xform_manager_table_api::getTables();

    if (is_array($tables)) {
        foreach ($tables as $table) {
            $table_perm = 'xform[table:' . $table['table_name'] . ']';
            if ($table['status'] == 1 && $table['hidden'] != 1 && $REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm))) {
                echo '<li><a href="index.php?page=xform&subpage=manager&tripage=data_edit&table_name=' . $table['table_name'] . '">' . $table['name'] . '</a></li>';
            }
        }
    }

    echo '</ul></div>';
    echo '</div>';
}
