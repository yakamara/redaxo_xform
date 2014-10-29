<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// ********************************************* TABLE ADD/EDIT/LIST

$func = rex_request('func', 'string', '');
$page = rex_request('page', 'string', '');
$subpage = rex_request('subpage', 'string', '');
$table_id = rex_request('table_id', 'int');

$show_list = true;


if ( $func == 'tableset_export' && $REX['USER']->isAdmin() ) {

    $xform_tables = array();
    foreach (rex_xform_manager_table::getAll() as $g_table) {
        $xform_tables[$g_table->getTableName()] = rex_translate("translate:".$g_table->getTableName()).' ['.$g_table->getTableName().']';
    }

    $xform = new rex_xform;
    $xform->setDebug(true);
    $xform->setHiddenField('page', $page);
    $xform->setHiddenField('subpage', $subpage);
    $xform->setHiddenField('func', $func);
    $xform->setObjectparams('real_field_names',true);
    $xform->setValueField('select', array('table_names', $I18N->msg('xform_manager_tables'), $xform_tables, 'multiple'=>1));
    $xform->setValidateField('empty', array('table_names', $I18N->msg('xform_manager_export_error_empty')));
    $form = $xform->getForm();

    if ($xform->objparams['form_show']) {

      echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('xform_manager_tableset_export') . '</h3>
      <div class="rex-addon-content">
      <p>' . $I18N->msg('xform_manager_tableset_export_info') . '</p>';
      echo $form;
      echo '</div></div>';

      echo rex_content_block('<a href="index.php?page=' . $page . '&amp;subpage=' . $subpage . '"><b>&laquo; ' . $I18N->msg('xform_back_to_overview') . '</b></a>');

      $show_list = false;

    } else {

        try {

            $table_names = rex_request("table_names");
            $return = rex_xform_manager_table_api::exportTablesets($table_names);

            $file_name = 'xform_manager_tableset_export_tables_'.date("YmdHis").'.json';

            ob_end_clean();

            header('Content-Type: application/json');
            header('Charset: UTF-8');
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Content-Length: ' . strlen($return));
            header('Pragma: public');
            header('Expires: 0');
            header('Content-Transfer-Encoding: binary');
            echo $return;

            exit;

        } catch (Exception $e) {
            $show_list = false;
            echo rex_warning($I18N->msg('xform_manager_table_export_failed', '', $e->getMessage()));

        }

    }

} else if ( $func == 'tableset_import' && $REX['USER']->isAdmin() ) {

  $xform = new rex_xform;
  $xform->setDebug(true);
  $xform->setHiddenField('page', $page);
  $xform->setHiddenField('subpage', $subpage);
  $xform->setHiddenField('func', $func);
  $xform->setObjectparams('real_field_names',true);
  $xform->setValueField('upload', array(
      'name'     => 'importfile',
      'label'    => $I18N->msg('xform_manager_table_import_jsonimportfile'),
      'max_size' => '1000', // max size in kb or range 100,500
      'types'    => '.json', // allowed extensions ".gif,.png"
      'required' => 1,
      'messages' => array(
          $I18N->msg('xform_manager_table_import_warning_min'),
          $I18N->msg('xform_manager_table_import_warning_max'),
          $I18N->msg('xform_manager_table_import_warning_type'),
          $I18N->msg('xform_manager_table_import_warning_selectfile')
        ),
      'modus'    => 'no_save',
      'no_db'    => 'no_db'
  ));

  $form = $xform->getForm();


  if ($xform->objparams['form_show']) {

    echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('xform_manager_tableset_import') . '</h3>
    <div class="rex-addon-content">';
    echo $form;
    echo '</div></div>';

    echo rex_content_block('<a href="index.php?page=' . $page . '&amp;subpage=' . $subpage . '"><b>&laquo; ' . $I18N->msg('xform_back_to_overview') . '</b></a>');

    $show_list = false;

  } else {

      try {
          $content = file_get_contents($xform->objparams['value_pool']['email']['importfile']);
          rex_xform_manager_table_api::importTablesets($content);
          echo rex_info($I18N->msg('xform_manager_table_import_success'));

      } catch (Exception $e) {
          echo rex_warning($I18N->msg('xform_manager_table_import_failed', '', $e->getMessage()));

      }

  }


} else if ( $func == 'migrate' && $REX['USER']->isAdmin() ) {

  $available_tables = rex_sql::showTables();
  $xform_tables = array();
  $missing_tables = array();

  foreach (rex_xform_manager_table::getAll() as $g_table) {
    $xform_tables[] = $g_table->getTableName();
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
  $xform->setValueField('select', array('table_name', $I18N->msg('xform_table'), $missing_tables));
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

} else if ( ($func == 'add' || $func == 'edit') && $REX['USER']->isAdmin() ) {

    $xform = new rex_xform;
    // $xform->setDebug(TRUE);
    $xform->setHiddenField('page', $page);
    $xform->setHiddenField('subpage', $subpage);
    $xform->setHiddenField('func', $func);

    $xform->setHiddenField('list', rex_request('list', 'string'));
    $xform->setHiddenField('sort', rex_request('sort', 'string'));
    $xform->setHiddenField('sorttype', rex_request('sorttype', 'string'));
    $xform->setHiddenField('start', rex_request('start', 'string'));

    $xform->setActionField('showtext', array('', $I18N->msg('xform_manager_table_entry_saved')));
    $xform->setObjectparams('main_table', rex_xform_manager_table::table());

    $xform->setValueField('prio', array('prio', $I18N->msg('xform_manager_table_prio'), 'name'));

    if ($func == 'edit') {
        $xform->setObjectparams('submit_btn_label', $I18N->msg('xform_save'));
        $xform->setValueField('showvalue', array('table_name', $I18N->msg('xform_manager_table_name')));
        $xform->setHiddenField('table_id', $table_id);
        $xform->setActionField('db', array(rex_xform_manager_table::table(), "id=$table_id"));
        $xform->setObjectparams('main_id', $table_id);
        $xform->setObjectparams('main_where', "id=$table_id");
        $xform->setObjectparams('getdata', true); // Datein vorher auslesen

    } elseif ($func == 'add') {
        $xform->setObjectparams('submit_btn_label', $I18N->msg('xform_add'));
        $xform->setValueField('text', array('table_name', $I18N->msg('xform_manager_table_name'), $REX['TABLE_PREFIX']));
        $xform->setValidateField('empty', array('table_name', $I18N->msg('xform_manager_table_enter_name')));
        $xform->setValidateField('customfunction', array('table_name', function ($label = '', $table = '', $params = '') {
            preg_match("/([a-z])+([0-9a-z\\_])*/", $table, $matches);
            return !count($matches) || current($matches) != $table;
        }, '', $I18N->msg('xform_manager_table_enter_specialchars')));
        $xform->setValidateField('customfunction', array('table_name', function ($label = '', $table = '', $params = '') {
            return (boolean) rex_xform_manager_table::get($table);
        }, '', $I18N->msg('xform_manager_table_exists')));
        $xform->setActionField('wrapper_value', array('table_name', '###value###')); // Tablename
        $xform->setActionField('db', array(rex_xform_manager_table::table()));

    }

    $xform->setValueField('text', array('name', $I18N->msg('xform_manager_name')));
    $xform->setValidateField('empty', array('name', $I18N->msg('xform_manager_table_enter_name')));

    $xform->setValueField('textarea', array('description', $I18N->msg('xform_manager_table_description'), 'css_class' => "short1"));
    $xform->setValueField('checkbox', array('status', $I18N->msg('xform_tbl_active')));
    // $xform->setValueField("fieldset",array("fs-list","Liste"));
    $xform->setValueField('text', array('list_amount', $I18N->msg('xform_manager_entries_per_page'), '50'));
    $xform->setValidateField('type', array('list_amount', 'int', $I18N->msg('xform_manager_enter_number')));

    $sortFields = array('id');
    if ($func === 'edit') {
        $sortFieldsSql = rex_sql::factory();
        $sortFieldsSql->setQuery('SELECT f.name FROM `' . rex_xform_manager_field::table() . '` f LEFT JOIN `' . rex_xform_manager_table::table() . '` t ON f.table_name = t.table_name WHERE t.id = ' . (int) $table_id . ' ORDER BY f.prio');
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

            $table = rex_xform_manager_table::get($table_name);
            if ($table) {
                $t = new rex_xform_manager();
                $t->setTable($table);
                $t->generateAll();
                echo rex_info($I18N->msg('xform_manager_table_added'));
            }


        }

    }

}


if ($func == 'delete' && $REX['USER']->isAdmin()) {

    $table_name = rex_request('table_name', 'string');
    echo rex_xform_manager_table_api::removeTable($table_name);

    $func = '';
    echo rex_info($I18N->msg('xform_manager_table_deleted'));
}


if ($show_list && $REX['USER']->isAdmin()) {

    // formatting func fuer status col
    function rex_xform_status_col($params)
    {
        global $I18N;
        $list = $params['list'];
        return $list->getValue('status') == 1 ? '<span style="color:green;">' . $I18N->msg('xform_tbl_active') . '</span>' : '<span style="color:red;">' . $I18N->msg('xform_tbl_inactive') . '</span>';
    }

    function rex_xform_hidden_col($params)
    {
        global $I18N;
        $list = $params['list'];
        return $list->getValue('hidden') == 1 ? '<span style="color:grey;">' . $I18N->msg('xform_hidden') . '</span>' : '<span>' . $I18N->msg('xform_visible') . '</span>';
    }

    function rex_xform_list_translate($params)
    {
        return rex_translate($params['subject']);
    }

    $table_echo = '<b>';
    $table_echo .= $I18N->msg('xform_manager_table').': <a href=index.php?page=' . $page . '&subpage=' . $subpage . '&func=add>' . $I18N->msg('xform_manager_create') . '</a>';
    $table_echo .= ' | <a href=index.php?page=' . $page . '&subpage=' . $subpage . '&func=migrate><b>' . $I18N->msg('xform_manager_migrate') . '</a>';
    $table_echo .= ' '.$I18N->msg('xform_manager_tableset').':</b> <a href=index.php?page=' . $page . '&subpage=' . $subpage . '&func=tableset_export>' . $I18N->msg('xform_manager_export') . '</a>';
    $table_echo .= ' | <a href=index.php?page=' . $page . '&subpage=' . $subpage . '&func=tableset_import>' . $I18N->msg('xform_manager_import') . '</a>';
    $table_echo .= '</b>';

    echo rex_content_block($table_echo);

    $sql = 'select id, prio, name, table_name, status, hidden from `' . rex_xform_manager_table::table() . '` order by prio,table_name';

    $list = rex_list::factory($sql, 30);
    $list->addParam('start', rex_request('start', 'int'));

    $list->removeColumn('id');

    $list->setColumnLabel('prio', $I18N->msg('xform_manager_table_prio_short'));
    $list->setColumnLabel('name', $I18N->msg('xform_manager_name'));
    $list->setColumnFormat('name', 'custom', 'rex_xform_list_translate');

    $list->setColumnLabel('table_name', $I18N->msg('xform_manager_table_name'));
    $list->setColumnParams('table_name', array('table_id' => '###id###', 'func' => 'edit'));

    $list->setColumnLabel('status', $I18N->msg('xform_manager_table_status'));
    $list->setColumnFormat('status', 'custom', 'rex_xform_status_col');

    $list->setColumnLabel('hidden', $I18N->msg('xform_manager_table_hidden'));
    $list->setColumnFormat('hidden', 'custom', 'rex_xform_hidden_col');

    $list->addColumn($I18N->msg('xform_edit'), $I18N->msg('xform_edit'));
    $list->setColumnParams($I18N->msg('xform_edit'), array('table_id' => '###id###', 'func' => 'edit'));

    $list->addColumn($I18N->msg('xform_delete'), $I18N->msg('xform_delete'));
    $list->setColumnParams($I18N->msg('xform_delete'), array('table_name' => '###table_name###', 'func' => 'delete'));
    $list->addLinkAttribute($I18N->msg('xform_delete'), 'onclick', 'return confirm(\' [###table_name###] ' . $I18N->msg('xform_delete') . ' ?\')');

    $list->addColumn($I18N->msg('xform_editfields'), $I18N->msg('xform_editfields'));
    $list->setColumnParams($I18N->msg('xform_editfields'), array('subpage' => 'manager', 'tripage' => 'table_field', 'table_name' => '###table_name###'));


    echo $list->get();
}


// ********************************************* LISTE OF TABLES TO EDIT FOR NOt ADMINS

if (!$REX['USER']->isAdmin()) {
    echo '<div class="rex-addon-output">';
    echo '<h2 class="rex-hl2">' . $I18N->msg('xform_table_overview') . '</h2>';
    echo '<div class="rex-addon-content"><ul>';

    $tables = rex_xform_manager_table::getAll();
    foreach ($tables as $table) {
        if ($table->isActive() && !$table->isHidden() && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table->getPermKey()))) {
            echo '<li><a href="index.php?page=xform&subpage=manager&tripage=data_edit&table_name=' . $table->getTableName() . '">' . rex_translate($table->getName()) . '</a></li>';
        }
    }

    echo '</ul></div>';
    echo '</div>';
}
