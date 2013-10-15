<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

if (!function_exists('rex_xform_manager_checkField')) {
  function rex_xform_manager_checkField($l, $v, $p)
  {
    return rex_xform_manager::checkField($l, $v, $p);
  }
}

class rex_xform_manager
{

  var $table = '';
  var $linkvars = array();
  var $type = '';
  var $dataPageFunctions = array();
  var $DataPageFilterFields = array();

  function rex_xform_manager()
  {
    global $REX;
    $this->setDataPageFunctions();

  }


  // ----- Permissions
  function setDataPageFunctions($f = array('add', 'delete', 'search', 'export', 'import', 'truncate_table'))
  {
    $this->dataPageFunctions = $f;
  }

  function hasDataPageFunction($f)
  {
    return in_array($f, $this->dataPageFunctions) ? true : false;
  }
  // -----


  // ----- Seitenausgabe

  function setLinkVars($linkvars)
  {
    $this->linkvars = $linkvars;
  }

  function getLinkVars()
  {
    return $this->linkvars;
  }




  // ---------------------------------- data functions

  public function getDataPage()
  {
    global $REX, $I18N;

    // ********************************************* DATA ADD/EDIT/LIST

    $func = rex_request('func', 'string', '');
    $data_id = rex_request('data_id', 'int', '');
    $show_list = true;

    // -------------- rex_xform_manager_search

    $rex_xform_searchfields = rex_request('rex_xform_searchfields', 'array');
    $rex_xform_searchtext = rex_request('rex_xform_searchtext', 'string');
    $rex_xform_search = rex_request('rex_xform_search', 'int', '0');
    $rex_xform_filter = rex_request('rex_xform_filter', 'array');
    $rex_xform_set = rex_request('rex_xform_set', 'array');



    // -------------- opener - popup for selection

    $popup = false;
    $rex_xform_manager_opener = rex_request('rex_xform_manager_opener', 'array');
    if (count($rex_xform_manager_opener) > 0) {
     if (isset($rex_xform_manager_opener['id'])) {
       $popup = true; // id, field, multiple

     }

    }



    // -------------- DEFAULT - LISTE AUSGEBEN

    $link_vars = '';
    foreach ($this->getLinkVars() as $k => $v) {
     $link_vars .= '&' . urlencode($k) . '=' . urlencode($v);

    }



    // -------------- TABELLE HOLEN

    unset($table);
    $tables = $this->getTables();
    if (!isset($table)) {
     if (count($tables) > 0) {
       $table = current($tables);

     } else {
    echo rex_warning($I18N->msg('table_not_found'));
       return;
       exit;

     }
    }

    rex_title($I18N->msg('table') . ': ' . rex_translate($table['name']) . ' <span class="table-name">[' . $table['table_name'] . ']</span>', '');

    echo rex_register_extension_point('XFORM_MANAGER_REX_INFO', '');

    $table['fields'] = $this->getTableFields($table['table_name']);




    $show_editpage = true;
    $show_editpage = rex_register_extension_point('XFORM_MANAGER_DATA_EDIT_FUNC', $show_editpage,
         array(
           'table' => $table,
           'link_vars' => $this->getLinkVars(),
         )
       );



    if ($show_editpage) {



     // -------------- DB FELDER HOLEN

     $fields = $table['fields'];
     $field_names = array();
     foreach ($fields as $field) {
       if ($field['type_id'] == 'value') {
         $field_names[] = $field['f1'];
       }
     }



     // -------------- DB DATA HOLEN

     $data = array();
     if ($data_id != '') {
       $gd = rex_sql::factory();
       $gd->setQuery('select * from ' . $table['table_name'] . ' where id=' . $data_id);
       if ($gd->getRows() == 1) {
         $datas = $gd->getArray();
         $data = current($datas);
       } else {
         $data_id = '';
       }
     }



     // -------------- Opener

     foreach ($rex_xform_manager_opener as $k => $v) {
       $link_vars .= '&rex_xform_manager_opener[' . $k . ']=' . urlencode($v);
     }



     // -------------- Searchfields / Searchtext

     foreach ($rex_xform_searchfields as $sf) {
       $link_vars .= '&rex_xform_searchfields[]=' . urlencode($sf);
     }
     $link_vars .= '&rex_xform_searchtext=' . urlencode($rex_xform_searchtext);
     $link_vars .= '&rex_xform_search=' . urlencode($rex_xform_search);



     // -------------- FILTER UND SETS PRFEN

     $em_url_filter = '';
     if (count($rex_xform_filter) > 0) {
       foreach ($rex_xform_filter as $k => $v) {
         if (in_array($k, $field_names)) {
           $em_url_filter .= '&amp;rex_xform_filter[' . $k . ']=' . urlencode($v);
         } else {
           unset($rex_xform_filter[$k]);
         }
       }
     };
     $em_url_set = '';
     if (count($rex_xform_set) > 0) {
       foreach ($rex_xform_set as $k => $v) {
         if (in_array($k, $field_names)) {
           $em_url_set .= '&amp;rex_xform_set[' . $k . ']=' . urlencode($v);
         } else {
           unset($rex_xform_set[$k]);
         }
       }
     };
     $em_url = $em_url_filter . $em_url_set;
     $em_rex_list = '';
     $em_rex_list .= '&list=' . urlencode(rex_request('list', 'string'));
     $em_rex_list .= '&sort=' . urlencode(rex_request('sort', 'string'));
     $em_rex_list .= '&sorttype=' . urlencode(rex_request('sorttype', 'string'));
     $em_rex_list .= '&start=' . urlencode(rex_request('start', 'string'));



     // ---------- Popup - no menue, header ...

     if ($popup) {
       echo '<link rel="stylesheet" type="text/css" href="../files/addons/xform/popup.css" media="screen, projection, print" />';
     }



     // -------------- Import
     if (!$popup && $func == 'import' && $this->hasDataPageFunction('import')) {
       include $REX['INCLUDE_PATH'] . '/addons/xform/plugins/manager/pages/data_import.inc.php';
       echo rex_content_block('<a href="index.php?' . $link_vars . $em_url . $em_rex_list . '"><b>&laquo; ' . $I18N->msg('back_to_overview') . '</b></a>');
     }



     // -------------- delete entry
     if ($func == 'delete' && $data_id != '' && $this->hasDataPageFunction('delete')) {
       $delete = true;
       if (rex_register_extension_point('XFORM_DATA_DELETE', $delete, array('id' => $data_id, 'value' => $data, 'table' => $table))) {
         $query = 'delete from ' . $table['table_name'] . ' where id=' . $data_id;
         $delsql = new rex_sql;
         // $delsql->debugsql=1;
         $delsql->setQuery($query);
         echo rex_info($I18N->msg('datadeleted'));
         $func = '';

         rex_register_extension_point('XFORM_DATA_DELETED', '', array('id' => $data_id, 'value' => $data, 'table' => $table));
       }

     }

     // -------------- delete dataset
     if ($func == 'dataset_delete' && $this->hasDataPageFunction('truncate_table')) {

       $delete = true;
       if (rex_register_extension_point('XFORM_DATA_DATASET_DELETE', $delete, array('table' => $table))) {
         $query = 'delete from ' . $table['table_name'] . $this->getDataListQueryWhere($rex_xform_filter, $rex_xform_searchfields , $rex_xform_searchtext );
         $delsql = new rex_sql;
         $delsql->setQuery($query);
         echo rex_info($I18N->msg('dataset_deleted'));
         $func = '';

         rex_register_extension_point('XFORM_DATA_DATASET_DELETED', '', array('table' => $table));
       }
     }

     // -------------- truncate table
     if ($func == 'truncate_table' && $this->hasDataPageFunction('truncate_table')) {
       $truncate = true;
       if (rex_register_extension_point('XFORM_DATA_TABLE_TRUNCATE', $truncate, array('table' => $table))) {
         $query = 'truncate table ' . $table['table_name'];
         $trunsql = new rex_sql;
         $trunsql->setQuery($query);
         echo rex_info($I18N->msg('table_truncated'));
         $func = '';

         rex_register_extension_point('XFORM_DATA_TABLE_TRUNCATED', '', array('table' => $table));
       }
     }

     // -------------- export dataset
     if ($func == 'dataset_export' && $this->hasDataPageFunction('export')) {

       ob_end_clean();

       $sql = $this->getDataListQuery($table, $rex_xform_filter, $rex_xform_searchfields, $rex_xform_searchtext);

       $data = '';
       $fields = array();
       $g = rex_sql::factory();
       $g->setQuery($sql);

       foreach ($g->getArray() as $d) {
         if ($data == '') {
           foreach ($d as $a => $b) {
             $fields[] = '"' . $a . '"';
           }
           $data = implode(';', $fields);
         }

         foreach ($d as $a => $b) {
           $d[$a] = '"' . str_replace('"', '""', $b) . '"';
         }
         $data .= "\n" . implode(';', $d);
       }

       // ----- download - save as

       $filename = 'export_data_' . date('YmdHis') . '.csv';
       $filesize = strlen($data);
       $filetype = 'application/octetstream';
       $expires = 'Mon, 01 Jan 2000 01:01:01 GMT';
       $last_modified = 'Mon, 01 Jan 2000 01:01:01 GMT';

       header('Expires: ' . $expires); // Date in the past
       header('Last-Modified: ' . $last_modified); // always modified
       header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
       header('Cache-Control: post-check=0, pre-check=0', false);
       header('Pragma: no-cache');
           header('Pragma: private');
           header('Cache-control: private, must-revalidate');
       header('Content-Type: ' . $filetype . '; name="' . $filename . '"');
       header('Content-Disposition: attachment; filename="' . $filename . '"');
       header('Content-Description: "' . $filename . '"');
       header('Content-Length: ' . $filesize);

       echo $data;

       exit;

     }



     // -------------- form
     if (($func == 'add'  && $this->hasDataPageFunction('add')) || $func == 'edit') {
       $back = rex_content_block('<a href="index.php?' . $link_vars . $em_url . $em_rex_list . '"><b>&laquo; ' . $I18N->msg('back_to_overview') . '</b></a>');

       $xform = new rex_xform;
       // $xform->setDebug(TRUE);
       foreach ($this->getLinkVars() as $k => $v) {
         $xform->setHiddenField($k, $v);
       }
       $xform->setHiddenField('func', $func);
       if (count($rex_xform_manager_opener) > 0) {
         foreach ($rex_xform_manager_opener as $k => $v) {
           $xform->setHiddenField('rex_xform_manager_opener[' . $k . ']', $v);
         }
       };

       // TODO
       if (count($rex_xform_filter) > 0) {
         foreach ($rex_xform_filter as $k => $v) {
           $xform->setHiddenField('rex_xform_filter[' . $k . ']', $v);
         }
       };
       if (count($rex_xform_set) > 0) {
         foreach ($rex_xform_set as $k => $v) {
           $xform->setHiddenField('rex_xform_set[' . $k . ']', $v);
         }
       };
       if (count($rex_xform_searchfields) > 0) {
         foreach ($rex_xform_searchfields as $k => $v) {
           $xform->setHiddenField('rex_xform_searchfields[' . $k . ']', $v);
         }
       };
       $xform->setHiddenField('rex_xform_search', $rex_xform_search);
       $xform->setHiddenField('rex_xform_searchtext', $rex_xform_searchtext);

       // for rexlist
       $xform->setHiddenField('list', rex_request('list', 'string'));
       $xform->setHiddenField('sort', rex_request('sort', 'string'));
       $xform->setHiddenField('sorttype', rex_request('sorttype', 'string'));
       $xform->setHiddenField('start', rex_request('start', 'string'));

       foreach ($fields as $field) {

         $values = array();
         for ($i = 1; $i < 10; $i++) {
           $values[$i] = $field['f' . $i];
         }
         if ($field['type_id'] == 'value') {
           if (in_array($values[1], $this->getFilterFields())) {
             // Feld vorhanden -> ignorieren -> hidden feld machen
             // TODO: Feld trotzdem noch aufnehmen, damit validierungen etc noch funktionieren
           } else {
             $xform->setValueField($field['type_name'], $values);
           }

         } elseif ($field['type_id'] == 'validate') {
           $xform->setValidateField($field['type_name'], $values);
         } elseif ($field['type_id'] == 'action') {
           $xform->setActionField($field['type_name'], $values);
         }
       }


       if (rex_request('rex_xform_show_formularblock', 'string') != '') {
         // Optional .. kann auch geloescht werden. Dient nur zu Hilfe beim Aufbau
         // von XForm-Formularen über php
         // Textblock gibt den formalarblock als text aus, um diesen in das xform modul einsetzen zu können.
         //  rex_xform_show_formularblock=1
         $text_block = '';
         foreach ($fields as $field) {
           $values = array(); for ($i = 1; $i < 10; $i++) {
             $values[] = $field['f' . $i];
           }
           if ($field['type_id'] == 'value') {
             $text_block .= "\n" . '$xform->setValueField("' . $field['type_name'] . '",array("' . implode('","', $values) . '"));';
           } elseif ($field['type_id'] == 'validate') {
             $text_block .= "\n" . '$xform->setValidateField("' . $field['type_name'] . '",array("' . implode('","', $values) . '"));';
           } elseif ($field['type_id'] == 'action') {
             $text_block .= "\n" . '$xform->setActionField("' . $field['type_name'] . '",array("' . implode('","', $values) . '"));';
           }
           // $text_block .= "\n".$field["type_name"].'|'.implode("|",$values);
         }
         echo '<pre>' . $text_block . '</pre>';
       }

       $xform->setObjectparams('main_table', $table['table_name']); // für db speicherungen und unique abfragen
       $xform->setObjectparams('submit_btn_label', $I18N->msg('submit'));

       // $xform->setObjectparams("manager_type",$this->getType());

       if ($func == 'edit') {
         $xform->setHiddenField('data_id', $data_id);
         $xform->setActionField('db', array($table['table_name'], "id=$data_id"));
         $xform->setObjectparams('main_id', $data_id);
         $xform->setObjectparams('main_where', "id=$data_id");
         $xform->setObjectparams('getdata', true);

         $xform->setObjectparams('submit_btn_label', $I18N->msg('save'));

       } elseif ($func == 'add') {
         $xform->setActionField('db', array($table['table_name']));

         $xform->setObjectparams('submit_btn_label', $I18N->msg('add'));

       }

       $xform->setObjectparams('rex_xform_set', $rex_xform_set);

       if ($func == 'edit') {
         $xform = rex_register_extension_point('XFORM_DATA_UPDATE', $xform, array('table' => $table, 'data_id' => $data_id, 'data' => $data));

       } elseif ($func == 'add') {
         $xform = rex_register_extension_point('XFORM_DATA_ADD', $xform, array('table' => $table));

       }

       $form = $xform->getForm();

       if ($xform->objparams['actions_executed']) {
         if ($func == 'edit') {
           echo rex_info($I18N->msg('thankyouforupdate'));
           $xform = rex_register_extension_point('XFORM_DATA_UPDATED', $xform, array('table' => $table, 'data_id' => $data_id, 'data' => $data));

         } elseif ($func == 'add') {
           echo rex_info($I18N->msg('thankyouforentry'));
           $xform = rex_register_extension_point('XFORM_DATA_ADDED', $xform, array('table' => $table));

         }

       }

       if ($xform->objparams['form_show'] || ($xform->objparams['form_showformafterupdate'] )) {

         echo $back;

         if ($func == 'edit') {
           echo '
             <div class="rex-addon-output">
               <h3 class="rex-hl2">' . $I18N->msg('editdata') . '</h3>
               <div class="rex-addon-content">' . $form . '</div>
             </div>';

         } else {
           echo '
             <div class="rex-addon-output">
               <h3 class="rex-hl2">' . $I18N->msg('adddata') . '</h3>
               <div class="rex-addon-content">' . $form . '</div>
             </div>';

         }

         echo rex_register_extension_point('XFORM_DATA_FORM', '', array('form' => $form, 'func' => $func, 'this' => $this, 'table' => $table));

         echo $back;

         $show_list = false;

       }

     }







     // ********************************************* LIST
     if ($show_list) {
       // ----- SUCHE
       if ($table['search'] == 1  && $this->hasDataPageFunction('search')) {
         $list = rex_request('list', 'string', '');
         $start = rex_request('start', 'string', '');
         $sort = rex_request('sort', 'string', '');
         $sorttype = rex_request('sorttype', 'string', '');

         $addsql = '';

         $checkboxes = '';
         $fields = array_merge(array(array('f1' => 'id', 'f2' => 'ID', 'type_id' => 'value', 'search' => 1, 'list_hidden' => 0)), $fields); // manually inject "id" into avail fields..
         foreach ($fields as $field) {
           if ($field['type_id'] == 'value' && $field['search'] == 1) {
             $checked = in_array($field['f1'], $rex_xform_searchfields) ? 'checked="checked"' : '';
             $checkboxes .= '<span class="xform-manager-searchfield"><input type="checkbox" name="rex_xform_searchfields[]" value="' . $field['f1'] . '" class="" id="' . $field['f1'] . '" ' . $checked . ' />&nbsp;<label for="' . $field['f1'] . '">' . rex_translate($field['f2']) . '</label></span>';
           }
         }
         $suchform = '';
         $suchform .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" ><div>';

         foreach ($this->getLinkVars() as $k => $v) {
           $suchform .= '<input type="hidden" name="' . $k . '" value="' . addslashes($v) . '" />';
         }

         if (count($rex_xform_filter) > 0) {
           foreach ($rex_xform_filter as $k => $v) {
             $suchform .= '<input type="hidden" name="rex_xform_filter[' . $k . ']" value="' . htmlspecialchars(stripslashes($v)) . '" />';
           }
         }
         if (count($rex_xform_set) > 0) {
           foreach ($rex_xform_set as $k => $v) {
             $suchform .= '<input type="hidden" name="rex_xform_set[' . $k . ']" value="' . htmlspecialchars(stripslashes($v)) . '" />';
           }
         }
         if (count($rex_xform_manager_opener) > 0) {
           foreach ($rex_xform_manager_opener as $k => $v) {
             $suchform .= '<input type="hidden" name="rex_xform_manager_opener[' . $k . ']" value="' . htmlspecialchars(stripslashes($v)) . '" />';
           }
         }

         if ($list != '') {
           $suchform .= '<input type="hidden" name="list" value="' . htmlspecialchars(stripslashes($list)) . '" />';
         };
         if ($start != '') {
           $suchform .= '<input type="hidden" name="start" value="' . htmlspecialchars(stripslashes($start)) . '" />';
         };
         if ($sort != '') {
           $suchform .= '<input type="hidden" name="sort" value="' . htmlspecialchars(stripslashes($sort)) . '" />';
         };
         if ($sorttype != '') {
           $suchform .= '<input type="hidden" name="sorttype" value="' . htmlspecialchars(stripslashes($sorttype)) . '" />';
         };

         $suchform .= '<input type="hidden" name="rex_xform_search" value="1" />';
         $suchform .= '</div>';

         $suchform .= '
           <table width="770" cellpadding="5" cellspacing="1" border="0" class="rex-table">
           <thead>
           <tr>
             <th>' . $I18N->msg('searchtext') . ' [<a href="#" id="xform_help_empty_toggler">?</a>]</th>
             <th>' . $I18N->msg('searchfields') . '</th>
             <th>&nbsp;</th>
           </tr>
           </thead>
           <tbody>
           <tr>
             <td class="grey" valign="top"><input type="text" name="rex_xform_searchtext" value="' . htmlspecialchars(stripslashes($rex_xform_searchtext)) . '" size="30" />
               <p id="xform_help_empty" style="display:none;">' . $I18N->msg('xform_help_empty') . '</p></td>
             <td class="grey" valign="top">' . $checkboxes . '</td>
             <td class="grey" valign="top"><input type="submit" name="send" value="' . $I18N->msg('search') . '"  class="inp100" /></td>
           </tr>
           </tbody>
           </table>';

         $suchform .= '</form>';

       } else {
         $suchform = '';
       }


       // -------------------------------------------------------------------

       $sql = $this->getDataListQuery($table, $rex_xform_filter, $rex_xform_searchfields, $rex_xform_searchtext);

       // ---------- LISTE AUSGEBEN
       if (!isset($table['list_amount']) || $table['list_amount'] < 1) {
         $table['list_amount'] = 30;
       }

       $list = rex_list::factory($sql, $table['list_amount']);
       $list->setColumnFormat('id', 'Id');

       foreach ($this->getLinkVars() as $k => $v) {
         $list->addParam($k, $v);
       }
       $list->addParam('table_name', $table['table_name']);

       if (count($rex_xform_filter) > 0) {
         foreach ($rex_xform_filter as $k => $v) {
           $list->addParam('rex_xform_filter[' . $k . ']', $v);
         }
       }
       if (count($rex_xform_set) > 0) {
         foreach ($rex_xform_set as $k => $v) {
           $list->addParam('rex_xform_set[' . $k . ']', $v);
         }
       }
       if (count($rex_xform_manager_opener) > 0) {
         foreach ($rex_xform_manager_opener as $k => $v) {
           $list->addParam('rex_xform_manager_opener[' . $k . ']', $v);
         }
       }

       if ($rex_xform_search != '') {
         $list->addParam('rex_xform_search', $rex_xform_search);
       };
       if ($rex_xform_searchtext != '') {
         $list->addParam('rex_xform_searchtext', $rex_xform_searchtext);
       };
       if (count($rex_xform_searchfields) > 0) {
         foreach ($rex_xform_searchfields as $k => $v) {
           $list->addParam('rex_xform_searchfields[' . $k . ']', $v);
         }
       }

       $list->setColumnParams('id', array('data_id' => '###id###', 'func' => 'edit' ));
       $list->setColumnSortable('id');

       foreach ($fields as $field) {

         // CALL CLASS'S LIST VALUE METHOD IF AVAILABLE
         if ($field['list_hidden'] == 0 && isset($field['type_name'])) {
           if (!class_exists('rex_xform_' . $field['type_name'])) {
             rex_xform::includeClass($field['type_id'], $field['type_name']);
           }
           if (method_exists('rex_xform_' . $field['type_name'], 'getListValue')) {
             $list->setColumnFormat(
               $field['f1'],
               'custom',
               array('rex_xform_' . $field['type_name'], 'getListValue'),
               array('field' => $field, 'fields' => $fields));
           }
         }

         if ($field['type_id'] == 'value') {
           if ($field['list_hidden'] == 1) {
             $list->removeColumn($field['f1']);
           } else {
             $list->setColumnSortable($field['f1']);
             $list->setColumnLabel($field['f1'], $field['f2']);
           }
         }
       }

       if (isset($rex_xform_manager_opener['id'])) {
         $list->addColumn('&uuml;bernehmen', '<a href="javascript:xform_manager_setData(' . $rex_xform_manager_opener['id'] . ',###id###,\'###' . $rex_xform_manager_opener['field'] . '### [id=###id###]\',' . $rex_xform_manager_opener['multiple'] . ')">&uuml;bernehmen</a>', -1, 'a');
       } else {
         $list->addColumn($I18N->msg('edit'), $I18N->msg('edit'));
         $list->setColumnParams($I18N->msg('edit'), array('data_id' => '###id###', 'func' => 'edit', 'start' => rex_request('start', 'string')));

         if ($this->hasDataPageFunction('delete')) {
           $list->addColumn($I18N->msg('delete'), $I18N->msg('delete'));
           $list->setColumnParams($I18N->msg('delete'), array('data_id' => '###id###', 'func' => 'delete'));
           $list->addLinkAttribute($I18N->msg('delete'), 'onclick', 'return confirm(\' id=###id### ' . $I18N->msg('delete') . ' ?\')');
         }
       }

       // hat diese Tabelle relationen ?
       // 55   rex_lit_titel   40     value     be_manager_relation   area_id   Region   rex_lit_area   name   1   0
       // id   table_name       prio   type_id   type_name             f1         f2       f3             f4     f5


    // *********************************************

       function xform_data_list_callback($params)
       {
         $id = $params['list']->getValue('id');
         $c = rex_sql::factory();
         // $c->debugsql = 1;
         $c->setQuery('select count(id) as counter from ' . $params['params']['table'] . ' where FIND_IN_SET(' . $id . ', ' . $params['params']['field'] . ');');
         return $c->getValue('counter');
       }
       $gr = rex_sql::factory();
       // $gr->debugsql = 1;
       $gr->setQuery('select * from '.$REX['TABLE_PREFIX'].'xform_field where type_name="be_manager_relation" and f3="' . $table['table_name'] . '"');
       $relation_fields = $gr->getArray();
       foreach ($relation_fields as $t) {
         $rel_id = 'rel-' . $t['table_name'] . '-' . $t['f1'];

         $relation_field_name = $t['table_name'] . '.' . $t['f1']; // '# <span title="['.$t["table_name"].'.'.$t["f1"].']">#</span>';
         if (strlen($relation_field_name) > 5) {
           $relation_field_name = '<span title="' . $relation_field_name . '">..' . substr($relation_field_name, -5) . '</span>';
         } else {
           $relation_field_name = '<span title="' . $relation_field_name . '">' . substr($relation_field_name, -5) . '</span>';
         }

         $list->addColumn($rel_id, '');
         $list->setColumnFormat($rel_id, 'custom', 'xform_data_list_callback', array('table' => $t['table_name'], 'field' => $t['f1']));
         $list->setColumnLabel($rel_id, $relation_field_name);
       }

    // *********************************************

       $list = rex_register_extension_point('XFORM_DATA_LIST', $list, array('table' => $table));
       echo '
       <div class="rex-addon-output">
         <div class="rex-hl2" style="font-size:12px;font-weight:bold;">
           <span style="float:left;">Datensatz: ';

       // ADD LINK
       if ($this->hasDataPageFunction('add')) {
         echo '<a href="index.php?' . htmlspecialchars($link_vars) . '&amp;func=add&amp;' . htmlspecialchars($em_url . $em_rex_list) . '">' . $I18N->msg('add') . '</a> | ';
       }

       // SEARCH LINK
       echo '<a href="#" id="searchtoggler">' . $I18N->msg('search') . '</a>';

       echo '</span>';

       // INFO LINK
       echo '<span style="float:right;">Tabelle: <a href="#" id="infotoggler">' . $I18N->msg('xform_table_info') . '</a>';

       if (($table['export'] == 1 && $this->hasDataPageFunction('export')) or $this->hasDataPageFunction('truncate_table')) {

          $dlink = array();
          if ($table['export'] == 1 && $this->hasDataPageFunction('export')) {
            $dlink[] = '<a href="index.php?' . htmlspecialchars($link_vars) . '&amp;func=dataset_delete&amp;' . htmlspecialchars($em_url . $em_rex_list) . '" id="dataset-delete" onclick="return confirm(\'' . $I18N->msg('dataset_delete_confirm') . '\');">' . $I18N->msg('delete') . '</a>';
          }

          if ($this->hasDataPageFunction('truncate_table')) {
            $dlink[] = '<a href="index.php?' . htmlspecialchars($link_vars) . '&amp;func=dataset_export&amp;' . htmlspecialchars($em_url . $em_rex_list) . '">' . $I18N->msg('export') . '</a>';
          }

          echo ' | ' . $I18N->msg('xform_dataset') . ': ' . implode(' / ', $dlink) . '';

       }

       if (!$popup && $table['import'] == 1 && $this->hasDataPageFunction('import')) {
         echo ' | <a href="index.php?' . htmlspecialchars($link_vars) . '&amp;func=import">' . $I18N->msg('import') . '</a>';
       }

       if ($this->hasDataPageFunction('truncate_table')) {
         echo ' | <a href="index.php?' . htmlspecialchars($link_vars) . '&amp;func=truncate_table&amp;' . htmlspecialchars($em_url . $em_rex_list) . '" id="truncate-table" onclick="return confirm(\'' . $I18N->msg('truncate_table_confirm') . '\');">' . $I18N->msg('truncate_table') . '</a>';
       }

       echo '</span><br style="clear:both;" /></div></div>';

       // SEARCHBLOCK
       $display = rex_request('rex_xform_search') == 1 ? 'block' : 'none';
       echo '<div id="searchblock" style="display:' . $display . ';">' . $suchform . '</div>';

       // INFOBLOCK

       echo '<div id="infoblock" style="display:none;/*padding:10px;*/"  class="rex-addon-output">
       <div class="rex-hl2" style="font-size:12px;font-weight:bold;">' . $I18N->msg('pool_file_details') . '</div>
       <ul>';
       echo '<li><b>' . $I18N->msg('xform_table_name') . '</b>: ' . $table['table_name'] . '</li>';
       if ($table['description'] != '') echo '<li><b>' . $I18N->msg('xform_description') . '</b>:' . nl2br($table['description']) . '</li>';
       if (isset($rex_xform_manager_opener['info'])) {
         echo '<li><b>' . $I18N->msg('openerinfo') . '</b>: ' . htmlspecialchars($rex_xform_manager_opener['info']) . '</li>';
       }
       echo '</ul></div><br style="clear:both;" />';



       echo $list->get();

       echo '

       <script type="text/javascript">/* <![CDATA[ */
         jQuery("#infotoggler").click(function(){jQuery("#infoblock").slideToggle("fast");});
         jQuery("#searchtoggler").click(function(){jQuery("#searchblock").slideToggle("fast");});
         jQuery("#xform_help_empty_toggler").click(function(){jQuery("#xform_help_empty").slideToggle("fast");});
         jQuery("#xform_search_reset").click(function(){window.location.href = "index.php?page=xform&subpage=manager&tripage=data_edit&table_name=' . $table['table_name'] . '&rex_xform_search=1";});
         jQuery("a.#truncate-table").click(function(){if(confirm("' . $I18N->msg('truncate_table_confirm') . '")){return true;} else {return false;}});
         jQuery("a.#dataset-delete").click(function(){if(confirm("' . $I18N->msg('dataset_delete_confirm') . '")){return true;} else {return false;}});
       /* ]]> */</script>';

     }

    } // end: $show_editpage

  }

  public function getDataListQueryWhere($rex_xform_filter = array(), $rex_xform_searchfields = array(), $rex_xform_searchtext = '')
  {
    $sql = '';
    if (count($rex_xform_filter) > 0) {
      $sql_filter = '';
      foreach ($rex_xform_filter as $k => $v) {
        if ($sql_filter != '') {
          $sql_filter .= ' AND ';
        }
        $sql_filter .= '`' . $k . '`="' . $v . '"';
      }
      $sql .= $sql_filter;
    }

    if (is_array($rex_xform_searchfields) && count($rex_xform_searchfields) > 0 && $rex_xform_searchtext != '') {
      $sf = array();
      foreach ($rex_xform_searchfields as $cs) {
        if ($rex_xform_searchtext == '(empty)') {
          $sf[] = ' (`' . $cs . '` = "" or `' . $cs . '` IS NULL) ';
        } elseif ($rex_xform_searchtext == '!(empty)') {
          $sf[] = ' (`' . $cs . '` <> "" and `' . $cs . '` IS NOT NULL) ';
        } else {
          $sf[] = ' `' . $cs . "` LIKE  '%" . $rex_xform_searchtext . "%'";
        }
      }
      if (count($sf) > 0) {
        $sql .= '( ' . implode(' OR ', $sf) . ' )';
      }
    }

    if ($sql != '') {
      $sql = ' where ' . $sql;
    }
    return $sql;
  }



  public function getDataListQuery($table, $rex_xform_filter = array(), $rex_xform_searchfields = array(), $rex_xform_searchtext = '')
  {
    global $REX;
    $where = false;

    $sql = 'select * from ' . $table['table_name'];

    $sql_felder = new rex_sql;
    $sql_felder->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'xform_field WHERE table_name="' . $table['table_name'] . '" AND type_id="value" ORDER BY prio');

    $felder = '';
    $max = $sql_felder->getRows();
    if ($max > 0) {
      for ($i = 0; $i < $sql_felder->getRows(); $i++) {
        $felder .= '`' . $sql_felder->getValue('f1') . '`';
        if ($i < $max - 1) {
          $felder .= ',';
        }
        $sql_felder->counter++;
      }
      $sql = 'select `id`,' . $felder . ' from `' . $table['table_name'] . '`';
    }

    $sql .= $this->getDataListQueryWhere($rex_xform_filter, $rex_xform_searchfields, $rex_xform_searchtext);
    $sql = rex_register_extension_point('XFORM_DATA_LIST_SQL', $sql, array('table' => $table));

    return $sql;
  }







  // ---------------------------------- table functions


  public function setFilterTable($table)
  {
    $this->filterTables[$table] = $table;
  }

  public function getFilterTables()
  {
    if (isset($this->filterTables) && is_array($this->filterTables))
    return $this->filterTables;
    else
    return array();
  }

  public function getTables()
  {
    global $REX;

    $where = '';
    foreach ($this->getFilterTables() as $t) {
      if ($where != '')
      $where .= ' OR ';
      $where .= '(table_name = "' . $t . '")';
    }

    if ($where != '') {
      $where = ' where ' . $where;
    }

    $tb = rex_sql::factory();
    // $tb->debugsql = 1;
    $tb->setQuery('select * from '.$REX['TABLE_PREFIX'].'xform_table ' . $where . ' order by prio,name');
    return $tb->getArray();
  }





  // ---------------------------------- field functions

  function getFieldPage()
  {
    global $REX, $I18N;

    // TODO: function rex_xform_manager_checkField

    // ********************************************* FIELD ADD/EDIT/LIST

    $func = rex_request('func', 'string', 'list');
    $page = rex_request('page', 'string', '');
    $subpage = rex_request('subpage', 'string', '');
    $tripage = rex_request('tripage', 'string', '');
    $type_id = rex_request('type_id', 'string');
    $type_name = rex_request('type_name', 'string');
    $field_id = rex_request('field_id', 'int');
    $show_list = true;

    $link_vars = '';
    foreach ($this->getLinkVars() as $k => $v) {
     $link_vars .= '&' . urlencode($k) . '=' . urlencode($v);
    }

    $TYPE = array('value' => $I18N->msg('values'), 'validate' => $I18N->msg('validates'), 'action' => $I18N->msg('action'));


    // ********************************** TABELLE HOLEN
    unset($table);
    $tables = $this->getTables();
    if (!isset($table)) {
     if (count($tables) > 0) {
       $table = current($tables);
     } else {
       echo 'Keine Tabelle gefunden';
       exit;
     }
    }
    foreach ($tables as $t) {
     if ($t['table_name'] == rex_request('table_name')) {
       $table = $t;
       break;
     }
    }

    $table_echo = '';
    //echo '<table cellpadding="5" class="rex-table" id="xform-alltables">';
    //echo '<tr><td>';
    foreach ($tables as $t) {
     if ($t['table_name'] == $table['table_name']) {
       $table_echo .= '<b>' . rex_translate($t['name']) . ' [' . $t['table_name'] . ']</b> ';
     } else {
       $table_echo .= ' <a href="index.php?' . $link_vars . '&table_name=' . $t['table_name'] . '">' . rex_translate($t['name']) . ' [' . $t['table_name'] . ']</a> ';
     }
    }
    //echo '</td></tr>';
    //echo '';
    if ($table['description'] != '') {
       //echo "<tr><td>".$table["description"].'</td></tr>';
       $table_echo .= '<p>' . $table['description'] . '</p>';
    }
    // if($rex_em_opener_info != "") { echo ' - '.$I18N->msg("openerinfo").': '.$rex_em_opener_info; }
    //echo '</table><br />';

    echo rex_content_block($table_echo);

    $table['fields'] = $this->getTableFields($table['table_name']);







    // ********************************************* Missing Fields

    $mfields = rex_xform_manager_table::getMissingFields($table['table_name']);
    // ksort($mfields);
    $type_real_field = rex_request('type_real_field', 'string');
    if ($type_real_field != '' && !array_key_exists($type_real_field, $mfields)) $type_real_field = '';

    if ($type_real_field != '') {
      ?>
     <div class="rex-addon-output"><h2 class="rex-hl2">Folgendes Feld wird verwendet: <?php echo $type_real_field; ?></h2><div class="rex-addon-content"><p class="rex-tx1"><?php

     $rfields = rex_xform_manager_table::getFields($table['table_name']);
     foreach ($rfields[$type_real_field] as $k => $v) {
       echo '<b>' . $k . ':</b> ' . $v . '<br />';
     }

     ?></p></div></div><?php

    }



    // ********************************************* CHOOSE FIELD
    $types = rex_xform::getTypeArray();
    if ($func == 'choosenadd') {
     // type and choose !!

     $link = 'index.php?' . $link_vars . '&table_name=' . $table['table_name'] . '&func=add&';

     if (!rex_xform_manager_table::hasId($table['table_name'])) {

       ?>
       <div class="rex-addon-output" id="xform-choosenadd"><h2 class="rex-hl2"><?php echo $I18N->msg('xform_id_is_missing'); ?></h2><div class="rex-addon-content">
       <p class="rex-tx1"><?php echo $I18N->msg('xform_id_missing_info'); ?></p>
       </div></div>
       <?php

     } else {

       ?>
       <div class="rex-addon-output" id="xform-choosenadd"><h2 class="rex-hl2"><?php echo $I18N->msg('choosenadd'); ?></h2><div class="rex-addon-content">
       <p class="rex-tx1"><?php echo $I18N->msg('choosenadd_description'); ?></p>
       </div></div>
       <?php

       if ($type_real_field == '' && count($mfields) > 0 ) {
         ?>
         <div class="rex-addon-output"><h2 class="rex-hl2">Es gibt noch Felder in der Tabelle welche nicht zugewiesen sind.</h2><div class="rex-addon-content">
         <?php
         $d = 0;
         foreach ($mfields as $k => $v) {
           $d++;
           $l = 'index.php?' . $link_vars . '&table_name=' . $table['table_name'] . '&func=choosenadd&type_real_field=' . $k . '&type_layout=t';
           echo '<a href="' . $l . '">' . $k . '</a>, ';
         }
         ?></div></div>

       <?php

       } ?>


         <div class="rex-addon-output xform-table_field">
           <div class="rex-area-col-2">

           <div class="rex-area-col-a">
               <h3 class="rex-hl2">beliebte <?php echo $TYPE['value']; ?></h3>
               <div class="rex-area-content"><p class="rex-tx1"><?php
         if (isset($types['value'])) {
           ksort($types['value']);
           foreach ($types['value'] as $k => $v) {
             if (isset($v['famous']) && $v['famous']) {
               echo '<p class="rex-button"><a class="rex-button" href="' . $link . 'type_id=value&type_name=' . $k . '&type_real_field=' . $type_real_field . '">' . $k . '</a> <span>' . $v['description'] . '</span></p>';
             }
           }
         }
         ?></p></div></div>

         <div class="rex-area-col-b"><h3 class="rex-hl2">beliebte <?php echo $TYPE['validate']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
         if (isset($types['validate'])) {
           ksort($types['validate']);
           foreach ($types['validate'] as $k => $v) {
             if (isset($v['famous']) && $v['famous']) {
             echo '<p class="rex-button"><a class="rex-button" href="' . $link . 'type_id=validate&type_name=' . $k . '">' . $k . '</a> <span>' . $v['description'] . '</span></p>';
             }
           }
         }
         ?></p></div></div>

       </div></div>

         <div class="rex-addon-output xform-table_field">
             <div class="rex-area-col-2">

         <div class="rex-area-col-a"><h3 class="rex-hl2"><?php echo $TYPE['value']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
         if (isset($types['value'])) {
           ksort($types['value']);
           foreach ($types['value'] as $k => $v) {
             if (!isset($v['famous']) || $v['famous'] !== true) {
             echo '<p class="rex-button"><a class="rex-button" href="' . $link . 'type_id=value&type_name=' . $k . '&type_real_field=' . $type_real_field . '">' . $k . '</a> <span>' . $v['description'] . '</span></p>';
             }
           }
         }
         ?></p></div></div>

         <div class="rex-area-col-b"><h3 class="rex-hl2"><?php echo $TYPE['validate']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
         if (isset($types['validate'])) {
           ksort($types['validate']);
           foreach ($types['validate'] as $k => $v) {
             if (!isset($v['famous']) || $v['famous'] !== true) {
             echo '<p class="rex-button"><a class="rex-button" href="' . $link . 'type_id=validate&type_name=' . $k . '">' . $k . '</a> <span>' . $v['description'] . '</span></p>';
             }
           }
         }
         ?></p></div></div>

       </div></div>

       <!--
       <div class="rex-addon-output">
       <h2 class="rex-hl2"><?php echo $TYPE['action']; ?></h2>
       <div class="rex-addon-content">
       <p class="rex-tx1"><?php
       if (isset($types['action'])) {
         ksort($types['action']);
         foreach ($types['action'] as $k => $v) {
           echo '<p class="rex-button">"<a href="' . $link . 'type_id=action&type_name=' . $k . '">' . $k . '</a>" - ' . $v['description'] . '</p>';
         }
       }
       ?></p>
       </div>
       </div>
       -->

       <?php

     }

     $table_echo = '<a href="index.php?' . $link_vars . '&amp;table_name=' . $table['table_name'] . '"><b>&laquo; ' . $I18N->msg('back_to_overview') . '</b></a>';
       echo rex_content_block($table_echo);

    }





    // ********************************************* FORMULAR


    if ( ($func == 'add' || $func == 'edit' )  && isset($types[$type_id][$type_name]) ) {

     $xform = new rex_xform;
     $xform->setDebug(false);

     foreach ($this->getLinkVars() as $k => $v) {
       $xform->setHiddenField($k, $v);
     }

     $xform->setHiddenField('func', $func);
     $xform->setHiddenField('table_name', $table['table_name']);
     $xform->setHiddenField('type_real_field', $type_real_field);

     $xform->setValueField('hidden', array('table_name', $table['table_name']));
     $xform->setValueField('hidden', array('type_name', $type_name, 'REQUEST'));
     $xform->setValueField('hidden', array('type_id', $type_id, 'REQUEST'));

     $xform->setValueField('text', array('prio', 'Prioritaet', (rex_xform_manager_table::getMaximumPrio($table['table_name']) + 10)));

     $i = 0;
     foreach ($types[$type_id][$type_name]['values'] as $v) {
       $i++;

       switch ($v['type']) {

         case 'name':

           if ($func == 'edit' ) {
             $xform->setValueField('showvalue', array('f' . $i, 'Name'));
           } else {
             if (!isset($v['value']) && $type_real_field != '')
               $v['value'] = $type_real_field;
             elseif (!isset($v['value']))
               $v['value'] = '';

             $xform->setValueField('text', array('f' . $i, 'Name', $v['value']));
             $xform->setValidateField('notEmpty', array('f' . $i, $I18N->msg('validatenamenotempty')));
             $xform->setValidateField('preg_match', array('f' . $i, "/(([a-zA-Z])+([a-zA-Z0-9\_])*)/", $I18N->msg('validatenamepregmatch')));
             $xform->setValidateField('customfunction', array('f' . $i, 'rex_xform_manager_checkField', array('table_name' => $table['table_name']), $I18N->msg('validatenamecheck')));
           }
           break;

         case 'no_db':
           // ToDo: Default Wert beachten


           $xform->setValueField('checkbox', array('f' . $i, $I18N->msg('donotsaveindb'), 'no_db', $v['default']));
           break;

         case 'boolean':
           // checkbox|check_design|Bezeichnung|Value|1/0|[no_db]
           if (!isset($v['default']))
             $v['default'] = '';
           $xform->setValueField('checkbox', array('f' . $i, $v['label'], '', $v['default']));
           break;

         case 'select':
           // select|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert|multiple=1
           $xform->setValueField('select', array('f' . $i, $v['label'], $v['definition'], '', $v['default'], 0));
           break;

         case 'table':
           // ist fest eingetragen, damit keine Dinge durcheinandergehen

           if ($func == 'edit' ) {
             $xform->setValueField('showvalue', array('f' . $i, $v['label']));
           } else {
             $_tables = rex_xform_manager_table::getTables();
             $_options = array();
             foreach ($_tables as $_table) {
                 $_options[$_table->getTableName()] = str_replace('=', '-', $_table->getName() . ' [' . $_table->getTableName() . ']') . '=' . $_table->getTableName();
                 $_options[$_table->getTableName()] = str_replace(',', '.', $_options[$_table->getTableName()]);
             }
             if (!isset($v['default'])) {
               $v['default'] = '';
             }
             $xform->setValueField('select', array('f' . $i, $v['label'], implode(',', $_options), '', $v['default'], 0));

           }
           break;

         case 'textarea':
           $xform->setValueField('textarea', array('f' . $i, $v['label']));
           break;

         case 'table.field':
           // Todo:

         case 'select_name':
           $_fields = array();
           foreach (rex_xform_manager_table::getXFormFieldsByType($table['table_name']) as $_k => $_v) {
             $_fields[] = $_k;
           }
           $xform->setValueField('select', array('f' . $i, $v['label'], implode(',', $_fields), '', '', 0));
           break;

         case 'select_names':
           // Todo: Mehrere Namen aus denanderen Federn ziehen und als multiselectbox anbieten
           $_fields = array();
           foreach (rex_xform_manager_table::getXFormFieldsByType($table['table_name']) as $_k => $_v) {
             $_fields[] = $_k;
           }
           $xform->setValueField('select', array('f' . $i, $v['label'], implode(',', $_fields), '', '', 1, 5));
           break;

         default:
           // nur beim "Bezeichnungsfeld"
           if ($i == 2 && $type_real_field != '' && !isset($v['value']))
             $v['value'] = $type_real_field;
           elseif (!isset($v['value']))
             $v['value'] = '';
           $xform->setValueField('text', array('f' . $i, $v['label'], $v['value']));
       }

     }

     $xform->setActionField('showtext', array('', '<p>' . $I18N->msg('thankyouforentry') . '</p>'));
     $xform->setObjectparams('main_table', $REX['TABLE_PREFIX'].'xform_field'); // f�r db speicherungen und unique abfragen

     if ($func == 'edit') {
       $xform->setObjectparams('submit_btn_label', $I18N->msg('save'));
       $xform->setHiddenField('field_id', $field_id);
       $xform->setActionField('manage_db', array($REX['TABLE_PREFIX'].'xform_field', "id=$field_id"));
       $xform->setObjectparams('main_id', $field_id);
       $xform->setObjectparams('main_where', "id=$field_id");
       $xform->setObjectparams('getdata', true);

     } elseif ($func == 'add') {
       $xform->setObjectparams('submit_btn_label', $I18N->msg('add'));
       $xform->setActionField('manage_db', array($REX['TABLE_PREFIX'].'xform_field'));

     }

     if ($type_id == 'value') {
       $xform->setValueField('checkbox', array('list_hidden', $I18N->msg('hideinlist'), 1, '1'));
         $xform->setValueField('checkbox', array('search', $I18N->msg('useassearchfieldalidatenamenotempty'), 1, '1'));

     } elseif ($type_id == 'validate') {
       $xform->setValueField('hidden', array('list_hidden', 1));

     }

     $form = $xform->getForm();

     if ($xform->objparams['form_show']) {
       if ($func == 'add')
         echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('addfield') . ' "' . $type_name . '"</h3><div class="rex-addon-content">';
       else
         echo '<div class="rex-addon-output"><h3 class="rex-hl2">' . $I18N->msg('editfield') . ' "' . $type_name . '"</h3><div class="rex-addon-content">';
       echo $form;
       echo '</div></div>';

         $table_echo = '<a href="index.php?' . $link_vars . '&amp;table_name=' . $table['table_name'] . '"><b>&laquo; ' . $I18N->msg('back_to_overview') . '</b></a>';
         echo rex_content_block($table_echo);

       $func = '';
     } else {
       if ($func == 'edit') {
         $this->generateAll();
         echo rex_info($I18N->msg('thankyouforupdate'));

       } elseif ($func == 'add') {
         $this->generateAll();
         echo rex_info($I18N->msg('thankyouforentry'));

       }
       $func = 'list';
     }
    }





    // ********************************************* LOESCHEN
    if ($func == 'delete') {

     $sf = new rex_sql();
     // $sf->debugsql = 1;
     $sf->setQuery('select * from '.$REX['TABLE_PREFIX'].'xform_field where table_name="' . $table['table_name'] . '" and id=' . $field_id);
     $sfa = $sf->getArray();
     if (count($sfa) == 1) {
       $query = 'delete from '.$REX['TABLE_PREFIX'].'xform_field where table_name="' . $table['table_name'] . '" and id=' . $field_id;
       $delsql = new rex_sql;
       // $delsql->debugsql=1;
       $delsql->setQuery($query);
       echo rex_info($I18N->msg('tablefielddeleted'));
       $this->generateAll();

     } else {
       echo rex_warning($I18N->msg('tablefieldnotfound'));
     }
     $func = 'list';
    }








    // ********************************************* CREATE/UPDATE FIELDS
    if ($func == 'updatetable') {
     $this->generateAll();
     echo rex_info($I18N->msg('tablesupdated'));
     $func = 'list';
    }

    if ($func == 'updatetablewithdelete') {
     $this->generateAll(array('delete_fields' => true));
     echo rex_info($I18N->msg('tablesupdated'));
     $func = 'list';
    }







    // ********************************************* LIST
    if ($func == 'list') {



     // ****** EP XFORM_MANAGER_TABLE_FIELD_FUNC

     $show_list = true;
     $show_list = rex_register_extension_point('XFORM_MANAGER_TABLE_FIELD_FUNC', $show_list,
           array(
             'table' => $t,
             'link_vars' => $this->getLinkVars(),
           )
         );



     if ($show_list) {


       function rex_xform_list_format($p, $value = '')
       {
         if ($value != '') $p['value'] = $value;
         switch ($p['list']->getValue('type_id')) {
           case 'validate':
             $style = 'color:#aaa;'; // background-color:#cfd9d9;
             break;
           case 'action':
             $style = 'background-color:#cfd9d9;';
             break;
           default:
             $style = 'background-color:#eff9f9;';
             break;
         }
         return '<td style="' . $style . '">' . $p['value'] . '</td>';
       }

       function rex_xform_list_edit_format($p)
       {
         global $REX, $I18N;
         return rex_xform_list_format($p, $p['list']->getColumnLink($I18N->msg('edit'), $I18N->msg('edit')));
       }

       function rex_xform_list_delete_format($p)
       {
         global $REX, $I18N;
         return rex_xform_list_format($p, $p['list']->getColumnLink($I18N->msg('delete'), $I18N->msg('delete')));
       }

       $table_echo = '
           <div class="rex-area-col-2">
               <div class="rex-area-col-a">
                   <a href="index.php?' . $link_vars . '&table_name=' . $table['table_name'] . '&func=choosenadd"><b>+ ' . $I18N->msg('addtablefield') . '</b></a>
               </div>
               <div class="rex-area-col-b rex-algn-rght">
                 <a href="index.php?' . $link_vars . '&table_name=' . $table['table_name'] . '&func=updatetable"><b>o ' . $I18N->msg('updatetable') . '</b></a>
                 <a href="index.php?' . $link_vars . '&table_name=' . $table['table_name'] . '&func=updatetablewithdelete" onclick="return confirm(\'' . $I18N->msg('updatetable_with_delete_confirm') . '\')"><b>o ' . $I18N->msg('updatetable_with_delete') . '</b></a>
               </div>
           </div>
           <div class="rex-clearer"></div>
           ';
       echo rex_content_block($table_echo);

       $sql = 'select * from '.$REX['TABLE_PREFIX'].'xform_field where table_name="' . $table['table_name'] . '" order by prio';
       $list = rex_list::factory($sql, 30);
       // $list->debug = 1;
       $list->setColumnFormat('id', 'Id');

       foreach ($this->getLinkVars() as $k => $v) {
         $list->addParam($k, $v);
       }

       $list->addParam('table_name', $table['table_name']);

       $list->removeColumn('table_name');
       $list->removeColumn('id');
       $list->removeColumn('list_hidden');
       $list->removeColumn('search');

       $list->setColumnLayout('prio', array('<th>###VALUE###</th>', '###VALUE###'));
       $list->setColumnFormat('prio', 'custom', 'rex_xform_list_format' );
       $list->setColumnLayout('type_id', array('<th>###VALUE###</th>', '###VALUE###'));
       $list->setColumnFormat('type_id', 'custom', 'rex_xform_list_format' );
       $list->setColumnLayout('type_name', array('<th>###VALUE###</th>', '###VALUE###'));
       $list->setColumnFormat('type_name', 'custom', 'rex_xform_list_format' );
       $list->setColumnLayout('f1', array('<th>label</th>', '###VALUE###')); // ###VALUE###
       $list->setColumnFormat('f1', 'custom', 'rex_xform_list_format' );

       for ($i = 2; $i < 10; $i++) {
         $list->removeColumn('f' . $i);
       }

       $list->addColumn($I18N->msg('edit'), $I18N->msg('edit'));
       $list->setColumnParams($I18N->msg('edit'), array('field_id' => '###id###', 'func' => 'edit', 'type_name' => '###type_name###', 'type_id' => '###type_id###', ));
       $list->setColumnLayout($I18N->msg('edit'), array('<th>###VALUE###</th>', '###VALUE###'));
       $list->setColumnFormat($I18N->msg('edit'), 'custom', 'rex_xform_list_edit_format' );

       $list->addColumn($I18N->msg('delete'), $I18N->msg('delete'));
       $list->setColumnParams($I18N->msg('delete'), array('field_id' => '###id###', 'func' => 'delete'));
       $list->setColumnLayout($I18N->msg('delete'), array('<th>###VALUE###</th>', '###VALUE###'));
       $list->setColumnFormat($I18N->msg('delete'), 'custom', 'rex_xform_list_delete_format' );
       $list->addLinkAttribute($I18N->msg('delete'), 'onclick', 'return confirm(\' [###type_id###, ###type_name###, ###f1###] ' . $I18N->msg('delete') . ' ?\')');

       echo $list->get();

     }

    }


  }



  // ----- Allgemeine Methoden



  // ----- Felder

  function getTableFields($table)
  {
    global $REX;

    $tb = rex_sql::factory();
    $tb->setQuery('select * from '.$REX['TABLE_PREFIX'].'xform_field where table_name="' . $table . '" order by prio');
    return $tb->getArray();
  }

  static function checkField($l, $v, $p)
  {
    global $REX;
    $q = 'select * from '.$REX['TABLE_PREFIX'].'xform_field where table_name="' . $p['table_name'] . '" and ' . $l . '="' . $v . '" LIMIT 1';
    $c = rex_sql::factory();
    // $c->debugsql = 1;
    $c->setQuery($q);
    if ($c->getRows() > 0) {
      // FALSE -> Warning = TRUE;
      return true;
    }
    return false;
  }

  function setFilterFields($DataPageFilterFields = array())
  {
    $this->DataPageFilterFields = $DataPageFilterFields;
  }

  function getFilterFields()
  {
    if (!is_array($this->DataPageFilterFields)) {
      return array();
    } else {
      return $this->DataPageFilterFields;
    }
  }

  function createTable($mifix = '', $data_table, $params = array(), $debug = false)
  {

    // Tabelle erstellen wenn noch nicht vorhanden
    $c = rex_sql::factory();
    $c->debugsql = $debug;
    $c->setQuery('CREATE TABLE IF NOT EXISTS `' . $data_table . '` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY );');

    // Tabellenset in die Basics einbauen, wenn noch nicht vorhanden
    $c = new rex_sql;
    $c->debugsql = $debug;
    $c->setQuery('DELETE FROM '.$REX['TABLE_PREFIX'].'xform_table where table_name="' . $data_table . '"');
    $c->setTable($REX['TABLE_PREFIX'].'xform_table');

    $params['table_name'] = $data_table;
    if (!isset($params['status']))
    $params['status'] = 1;

    if (!isset($params['name']))
    $params['name'] = 'Tabelle "' . $data_table . '"';

    if (!isset($params['prio']))
    $params['prio'] = 100;

    if (!isset($params['search']))
    $params['search'] = 0;

    if (!isset($params['hidden']))
    $params['hidden'] = 0;

    if (!isset($params['export']))
    $params['export'] = 0;

    foreach ($params as $k => $v) {
      $c->setValue($k, $v);
    }

    $c->insert();

    return true;

  }

  function addDataFields($data_table, $fields, $debug = false)
  {

    // definition fields table
    foreach ($fields as $field) {

      $table_name = $field['table_name']; // 'user';
      $type_id = $field['type_id']; // 'value';
      $type_name = $field['type_name']; // 'select';
      $f1 = $field['f1']; // 'status';

      if (!in_array($type_id, rex_xform::getTypes()))
      return false;

      $gs = rex_sql::factory();
      $gs->debugsql = $debug;
      $gs->setQuery('delete from '.$REX['TABLE_PREFIX'].'xform_field where table_name="' . $table_name . '" and type_id="' . $type_id . '" and type_name="' . $type_name . '" and f1="' . $f1 . '"');

      // fielddaten - datensatz anlegen
      $af = rex_sql::factory();
      $af->debugsql = $debug;
      $af->setTable($REX['TABLE_PREFIX'].'xform_field');
      foreach ($field as $k => $v) {
        $af->setValue($k, $v);
      }
      if (!$af->insert())
      return false;

      // datentabelle - spalte hinzufügen
      if ($type_id == 'value' && $type_name != '' && $f1 != '') {
        if ($classname = rex_xform::includeClass('value', $type_name)) {
          } else {
            return false;
          }
        $cl = new $classname;
        $definitions = $cl->getDefinitions();
        if (isset($definitions['dbtype']) && $definitions['dbtype'] != '') {
          // Structur in spalte anpassen
          $af = rex_sql::factory();
          $af->debugsql = $debug;
          $af->setQuery('ALTER TABLE `' . $data_table . '` ADD `' . $f1 . '` ' . $definitions['dbtype'] . ' NOT NULL ;');
        }
      }

    }

    return true;
  }



  function generateAll($f = array())
  {
    global $REX;

    $types = rex_xform::getTypeArray();
    foreach ($this->getTables() as $table) {

      // ********** Table schon vorhanden ?, wenn nein, dann anlegen
      $c = rex_sql::factory();
      // $c->debugsql = 1;
      $c->setQuery('CREATE TABLE IF NOT EXISTS `' . $table['table_name'] . '` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY )');

      // Felder merken, erstellen und eventuell loeschen
      $c->setQuery('SHOW COLUMNS FROM `' . $table['table_name'] . '`');
      $saved_columns = $c->getArray();

      foreach ($this->getTableFields($table['table_name']) as $field) {
        $type_name = $field['type_name'];
        $type_id = $field['type_id'];

        if ($type_id == 'value') {
          $type_label = $field['f1'];
          $dbtype = $types[$type_id][$type_name]['dbtype'];

          if ($dbtype != 'none' && $dbtype != '') {
            // Column schon vorhanden ?
            $add_column = true;
            foreach ($saved_columns as $uu => $vv) {
              if ($vv['Field'] == $type_label) {
                $add_column = false;
                unset($saved_columns[$uu]);
                break;
              }
            }

            // Column erstellen
            if ($add_column) {
              $c->setQuery('ALTER TABLE `' . $table['table_name'] . '` ADD `' . $type_label . '` ' . $dbtype . ' NOT NULL');
            }
          }

        }

      }

      if (isset($f['delete_fields']) && $f['delete_fields'] === true) {
        foreach ($saved_columns as $uu => $vv) {
          if ($vv['Field'] != 'id') {
            $c->setQuery('ALTER TABLE `' . $table['table_name'] . '` DROP `' . $vv['Field'] . '` ');
          }
        }
      }

    }
  }


  function repairAll()
  {
    // Alle Tabellen durchgehen und anpassen
    // - relation
    // - field
    // - data
    // - update tabletype
    // - NOT NULL
    // - Default value

  }

  static function checkMediaInUse($params)
  {
    global $REX, $I18N;

    $warning = $params['subject'];

    $sql = rex_sql::factory();
    $sql->setQuery('SELECT `table_name`, `type_name`, `f1` FROM `' . $REX['TABLE_PREFIX'] . 'xform_field` WHERE `type_id`="value" AND `type_name` IN("be_medialist","be_mediapool")');

    $rows = $sql->getRows();

    if ($rows == 0)
      return $warning;

    $where = array();
    $filename = addslashes($params['filename']);
    while ($sql->hasNext()) {
      $table = $sql->getValue('table_name');
      switch ($sql->getValue('type_name')) {
        case 'be_mediapool':
          $where[$table][] = $sql->getValue('f1') . '="' . $filename . '"';
          break;
        case 'be_medialist':
          $where[$table][] = 'FIND_IN_SET("' . $filename . '", ' . $sql->getValue('f1') . ')';
          break;
        default :
          trigger_error('Unexpected fieldtype "' . $sql->getValue('type_name') . '"!', E_USER_ERROR);
      }
      $sql->next();
    }

    $tupel = '';
    foreach ($where as $table => $cond) {
      $sql->setQuery('SELECT id FROM ' . $table . ' WHERE ' . implode(' OR ', $cond));

      while ($sql->hasNext()) {
        $sql_tupel = rex_sql::factory();
        $sql_tupel->setQuery('SELECT name FROM `' . $REX['TABLE_PREFIX'] . 'xform_table` WHERE `table_name`="' . $table . '"');

        $tupel .= '<li><a href="javascript:openPage(\'index.php?page=xform&amp;subpage=manager&amp;tripage=data_edit&amp;table_name=' . $table . '&amp;data_id=' . $sql->getValue('id') . '&amp;func=edit\')">' . $sql_tupel->getValue('name') . ' [id=' . $sql->getValue('id') . ']</a></li>';

        $sql->next();
      }
    }

    if ($tupel != '') {
      $warning[] = 'Tabelle<br /><ul>' . $tupel . '</ul>';
    }

    return $warning;
  }


}
