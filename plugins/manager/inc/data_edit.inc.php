<?php

// ********************************************* DATA ADD/EDIT/LIST

$func = rex_request("func","string","");
$data_id = rex_request("data_id","int","");
$show_list = TRUE;

// -------------- rex_xform_manager_search

$rex_xform_searchfields = rex_request("rex_xform_searchfields","array");
$rex_xform_searchtext = rex_request("rex_xform_searchtext","string");
$rex_xform_search = rex_request("rex_xform_search","int","0");
$rex_xform_filter = rex_request("rex_xform_filter","array");
$rex_xform_set = rex_request("rex_xform_set","array");



// -------------- opener - popup for selection

$popup = FALSE;
$rex_xform_manager_opener = rex_request("rex_xform_manager_opener","array");
if(count($rex_xform_manager_opener)>0) {
  if(isset($rex_xform_manager_opener["id"])) {
    $popup = TRUE; // id, field, multiple

  }

}



// -------------- DEFAULT - LISTE AUSGEBEN

$link_vars = "";
foreach($this->getLinkVars() as $k => $v) {
  $link_vars .= '&'.urlencode($k).'='.urlencode($v);

}



// -------------- TABELLE HOLEN

unset($table);
$tables = $this->getTables();
if(!isset($table)) {
  if(count($tables) > 0) {
    $table = current($tables);

  }else {
  rex_title($I18N->msg("table_not_found"), "");
    return;
    exit;

  }
}

rex_title($I18N->msg("table").": ".rex_translate($table["name"]).' <span class="table-name">['.$table["table_name"].']</span>', "");

$table["fields"] = $this->getTableFields($table["table_name"]);




$show_editpage = TRUE;
$show_editpage = rex_register_extension_point('XFORM_MANAGER_DATA_EDIT_FUNC', $show_editpage,
      array(
        'table' => $table,
        'link_vars' => $this->getLinkVars(),
      )
    );



if($show_editpage) {



  // -------------- DB FELDER HOLEN

  $fields = $table['fields'];
  $field_names = array();
  foreach($fields as $field){
    if($field["type_id"] == "value") {
      $field_names[] = $field["f1"];
    }
  }



  // -------------- DB DATA HOLEN

  $data = array();
  if($data_id != "") {
    $gd = rex_sql::factory();
    $gd->setQuery('select * from '.$table["table_name"].' where id='.$data_id);
    if($gd->getRows()==1) {
      $datas = $gd->getArray();
      $data = current($datas);
    }else {
      $data_id = "";
    }
  }



  // -------------- Opener

  foreach($rex_xform_manager_opener as $k => $v)
  {
    $link_vars .= '&rex_xform_manager_opener['.$k.']='.urlencode($v);
  }



  // -------------- Searchfields / Searchtext

  foreach($rex_xform_searchfields as $sf)
  {
    $link_vars .= '&rex_xform_searchfields[]='.urlencode($sf);
  }
  $link_vars .= '&rex_xform_searchtext='.urlencode($rex_xform_searchtext);
  $link_vars .= '&rex_xform_search='.urlencode($rex_xform_search);



  // -------------- FILTER UND SETS PRFEN

  $em_url_filter = "";
  if(count($rex_xform_filter)>0) {
    foreach($rex_xform_filter as $k => $v) {
      if(in_array($k,$field_names)) { $em_url_filter .= '&amp;rex_xform_filter['.$k.']='.urlencode($v); }
      else { unset($rex_xform_filter[$k]); }
    }
  };
  $em_url_set = "";
  if(count($rex_xform_set)>0) {
    foreach($rex_xform_set as $k => $v) {
      if(in_array($k,$field_names)) { $em_url_set .= '&amp;rex_xform_set['.$k.']='.urlencode($v); }
      else { unset($rex_xform_set[$k]); }
    }
  };
  $em_url = $em_url_filter.$em_url_set;
  $em_rex_list = "";
  $em_rex_list .= '&amp;list='.urlencode(rex_request('list','string'));
  $em_rex_list .= '&amp;sort='.urlencode(rex_request('sort','string'));
  $em_rex_list .= '&amp;sorttype='.urlencode(rex_request('sorttype','string'));
  $em_rex_list .= '&amp;start='.urlencode(rex_request('start','string'));



  // ---------- Popup - no menue, header ...

  if($popup) {
    echo '<link rel="stylesheet" type="text/css" href="../files/addons/xform/popup.css" media="screen, projection, print" />';
  }



  // -------------- Import
  if(!$popup && $func == "import" && $this->hasDataPageFunction("import")) {
    include $REX["INCLUDE_PATH"].'/addons/xform/plugins/manager/pages/data_import.inc.php';
    echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?'.$link_vars.$em_url.$em_rex_list.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';

  }



  // -------------- delete entry
  if($func == "delete" && $data_id != "" && $this->hasDataPageFunction("delete"))
  {
    $delete = TRUE;
    if(rex_register_extension_point('XFORM_DATA_DELETE', $delete, array("id"=>$data_id, "value"=>$data, "table"=>$table)))
    {
      $query = 'delete from '.$table["table_name"].' where id='.$data_id;
      $delsql = new rex_sql;
      // $delsql->debugsql=1;
      $delsql->setQuery($query);
      echo rex_info($I18N->msg("datadeleted"));
      $func = "";

      rex_register_extension_point('XFORM_DATA_DELETED', "", array("id"=>$data_id, "value"=>$data, "table"=>$table));
    }

  }



  // -------------- truncate table
  if($func == "truncate_table" && $this->hasDataPageFunction("truncate_table"))
  {
    $truncate = TRUE;
    if(rex_register_extension_point('XFORM_DATA_TABLE_TRUNCATE', $truncate, array("table"=>$table)))
    {
      $query = 'truncate table '.$table["table_name"];
      $trunsql = new rex_sql;
      $trunsql->setQuery($query);
      echo rex_info($I18N->msg("table_truncated"));
      $func = "";

      rex_register_extension_point('XFORM_DATA_TABLE_TRUNCATED', "", array("table"=>$table));
    }

  }



  // -------------- form
  if(($func == "add"  && $this->hasDataPageFunction("add")) || $func == "edit")
  {
    $back = '<table cellpadding="5" class="rex-table"><tr><td><a href="index.php?'.$link_vars.$em_url.$em_rex_list.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';

    $xform = new rex_xform;
    // $xform->setDebug(TRUE);
    foreach($this->getLinkVars() as $k => $v) {
      $xform->setHiddenField($k, $v);
    }
    $xform->setHiddenField("func",$func);
    if(count($rex_xform_manager_opener)>0) { foreach($rex_xform_manager_opener as $k => $v) { $xform->setHiddenField('rex_xform_manager_opener['.$k.']',$v); } };

    // TODO
    if(count($rex_xform_filter)>0) { foreach($rex_xform_filter as $k => $v) { $xform->setHiddenField('rex_xform_filter['.$k.']',$v); } };
    if(count($rex_xform_set)>0) { foreach($rex_xform_set as $k => $v) { $xform->setHiddenField('rex_xform_set['.$k.']',$v); } };
    if(count($rex_xform_searchfields)>0) { foreach($rex_xform_searchfields as $k => $v) { $xform->setHiddenField('rex_xform_searchfields['.$k.']',$v); } };
    $xform->setHiddenField("rex_xform_search",$rex_xform_search);
    $xform->setHiddenField("rex_xform_searchtext",$rex_xform_searchtext);

    // for rexlist
    $xform->setHiddenField('list',rex_request('list','string'));
    $xform->setHiddenField('sort',rex_request('sort','string'));
    $xform->setHiddenField('sorttype',rex_request('sorttype','string'));
    $xform->setHiddenField('start',rex_request('start','string'));

    foreach($fields as $field)
    {

      $values = array();
      for($i=1;$i<10;$i++){ $values[$i] = $field["f".$i]; }
      if($field["type_id"] == "value")
      {
        if(in_array($values[1],$this->getFilterFields()))
        {
          // Feld vorhanden -> ignorieren -> hidden feld machen
          // TODO: Feld trotzdem noch aufnehmen, damit validierungen etc noch funktionieren
        }else
        {
          $xform->setValueField($field["type_name"],$values);
        }

      }elseif($field["type_id"] == "validate")
      {
        $xform->setValidateField($field["type_name"],$values);
      }elseif($field["type_id"] == "action")
      {
        $xform->setActionField($field["type_name"],$values);
      }
    }


    if(rex_request("rex_xform_show_formularblock","string") != "")
    {
      // Optional .. kann auch geloescht werden. Dient nur zu Hilfe beim Aufbau
      // von XForm-Formularen über php
      // Textblock gibt den formalarblock als text aus, um diesen in das xform modul einsetzen zu können.
      //	rex_xform_show_formularblock=1
      $text_block = '';
      foreach($fields as $field)
      {
        $values = array(); for($i=1;$i<10;$i++){ $values[] = $field["f".$i]; }
        if($field["type_id"] == "value") {
          $text_block .= "\n".'$xform->setValueField("'.$field["type_name"].'",array("'.implode('","',$values).'"));';
        }elseif($field["type_id"] == "validate") {
          $text_block .= "\n".'$xform->setValidateField("'.$field["type_name"].'",array("'.implode('","',$values).'"));';
        }elseif($field["type_id"] == "action") {
          $text_block .= "\n".'$xform->setActionField("'.$field["type_name"].'",array("'.implode('","',$values).'"));';
        }
        // $text_block .= "\n".$field["type_name"].'|'.implode("|",$values);
      }
      echo '<pre>'.$text_block.'</pre>';
    }

    $xform->setObjectparams("main_table",$table["table_name"]); // für db speicherungen und unique abfragen
    $xform->setObjectparams("submit_btn_label",$I18N->msg('submit'));

    // $xform->setObjectparams("manager_type",$this->getType());

    if($func == "edit")
    {
      $xform->setHiddenField("data_id",$data_id);
      $xform->setActionField("db",array($table["table_name"],"id=$data_id"));
      $xform->setObjectparams("main_id",$data_id);
      $xform->setObjectparams("main_where","id=$data_id");
      $xform->setObjectparams("getdata",TRUE);

      $xform->setObjectparams("submit_btn_label",$I18N->msg('save'));

    }elseif($func == "add")
    {
      $xform->setActionField("db",array($table["table_name"]));
      
      $xform->setObjectparams("submit_btn_label",$I18N->msg('add'));
      
    }

    $xform->setObjectparams("rex_xform_set",$rex_xform_set);

    $form = $xform->getForm();

    // Formular ausgeben wenn
    // - fehler
    // - edit und nur save
    // - add und nur save

    // formular nicht ausgeben wenn
    // - wenn edit und schliessen
    // - add und schliessen
    // - und nur wenn kein fehler

    if($xform->objparams["form_show"] || ($xform->objparams["form_showformafterupdate"] )) {

      if($xform->objparams["send"]) {
        if($func == "edit") {
          if($form == "") {
            echo rex_info($I18N->msg("thankyouforupdate"));
            $xform = rex_register_extension_point('XFORM_DATA_UPDATED', $xform, array("table"=>$table));
          }
        }elseif($func == "add") {
          if($form == "") {
            echo rex_info($I18N->msg("thankyouforentry"));
            $xform = rex_register_extension_point('XFORM_DATA_ADDED', $xform, array("table"=>$table));
          }
        }
      }

      echo $back.'<br />';

      if($func == "edit") {
        echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("editdata").'</h3><div class="rex-area-content">';
      }else {
        echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("adddata").'</h3><div class="rex-area-content">';
      }
      echo $form;
      echo '</div></div>';

      echo rex_register_extension_point('XFORM_DATA_FORM', '', array("form" => $form, "func" => $func, "this" => $this, "table"=>$table));

      echo '<br />&nbsp;<br />'.$back;

      $show_list = FALSE;
    }else {
      if($func == "edit") {
        echo rex_info($I18N->msg("thankyouforupdate"));
      }elseif($func == "add"){
        echo rex_info($I18N->msg("thankyouforentry"));
      }
    }
  }







  // ********************************************* LIST
  if($show_list)
  {
    // ----- SUCHE
    if($table["search"]==1  && $this->hasDataPageFunction("search"))
    {
      $list = rex_request("list","string","");
      $start = rex_request("start","string","");
      $sort = rex_request("sort","string","");
      $sorttype = rex_request("sorttype","string","");

      $addsql = "";

      $checkboxes = '';
      $fields = array_merge(array(array('f1'=>'id','f2'=>'ID','type_id'=>'value','search'=>1,'list_hidden'=>0)),$fields); // manually inject "id" into avail fields..
      foreach($fields as $field)
      {
        if($field["type_id"] == "value" && $field["search"] == 1)
        {
          $checked = in_array($field["f1"],$rex_xform_searchfields) ? 'checked="checked"' : '';
          $checkboxes .= '<span class="xform-manager-searchfield"><input type="checkbox" name="rex_xform_searchfields[]" value="'.$field["f1"].'" class="" id="'.$field["f1"].'" '.$checked.' />&nbsp;<label for="'.$field["f1"].'">'.rex_translate($field["f2"]).'</label></span>';
        }
      }

      $suchform = '<table width=770 cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff class="rex-table">';
      $suchform .= '<form action="'.$_SERVER['PHP_SELF'].'" method="poost" >';

      foreach($this->getLinkVars() as $k => $v)
      {
        $suchform .= '<input type="hidden" name="'.$k.'" value="'.addslashes($v).'" />';
      }

      if(count($rex_xform_filter)>0) { foreach($rex_xform_filter as $k => $v) { $suchform .= '<input type="hidden" name="rex_xform_filter['.$k.']" value="'.htmlspecialchars(stripslashes($v)).'" />'; } }
      if(count($rex_xform_set)>0) { foreach($rex_xform_set as $k => $v) { $suchform .= '<input type="hidden" name="rex_xform_set['.$k.']" value="'.htmlspecialchars(stripslashes($v)).'" />'; } }
      if(count($rex_xform_manager_opener)>0) { foreach($rex_xform_manager_opener as $k => $v) { $suchform .= '<input type="hidden" name="rex_xform_manager_opener['.$k.']" value="'.htmlspecialchars(stripslashes($v)).'" />'; } }

      if($list != "") { $suchform .= '<input type="hidden" name="list" value="'.htmlspecialchars(stripslashes($list)).'" />'; };
      if($start != "") { $suchform .= '<input type="hidden" name="start" value="'.htmlspecialchars(stripslashes($start)).'" />'; };
      if($sort != "") { $suchform .= '<input type="hidden" name="sort" value="'.htmlspecialchars(stripslashes($sort)).'" />'; };
      if($sorttype != "") { $suchform .= '<input type="hidden" name="sorttype" value="'.htmlspecialchars(stripslashes($sorttype)).'" />'; };

      $suchform .= '<input type="hidden" name="rex_xform_search" value="1" />';
      $suchform .= '<tr>
          <th>'.$I18N->msg('searchtext').' [<a href="#" id="xform_help_empty_toggler">?</a>]</th>
          <th>'.$I18N->msg('searchfields').'</th>
          <th>&nbsp;</th>
        </tr>';
      $suchform .= '<tr>
          <td class="grey" valign="top"><input type="text" name="rex_xform_searchtext" value="'.htmlspecialchars(stripslashes($rex_xform_searchtext)).'" size="30" />
          <p id="xform_help_empty" style="display:none;">'.$I18N->msg('xform_help_empty').'</p></td>
          <td class="grey" valign="top">'.$checkboxes.'</td>
          <td class="grey" valign="top"><input type="submit" name="send" value="'.$I18N->msg('search').'"  class="inp100" />';
      if(rex_request('rex_xform_search')==1 && rex_request('rex_xform_searchtext')!='')
      {
        $suchform .= '&nbsp;<input type="button" name="reset" value="'.$I18N->msg('form_reset').'" class="inp100" id="xform_search_reset"/>';
      }
      $suchform .= '</td>
        </tr>';
      $suchform .= '</form>';
      $suchform .= '</table>';

      //echo $suchform;
    }else{
    $suchform = '';
    }

    $where = false;

    // ---------- SQL AUFBAUEN
    $sql = "select * from ".$table["table_name"];



    //-------------------------------------------------------------------
    //TABELLENFELDER AUCH NACH PRIO AUSGEBEN

    $sql_felder = new rex_sql;
    $sql_felder->debugsql = 0;
    $sql_felder->setQuery("SELECT * FROM rex_xform_field WHERE table_name='".$table["table_name"]."' AND type_id='value' ORDER BY prio");

    $felder = '';
    $max = $sql_felder->getRows();
    if ($max > 0){
      for($i=0;$i<$sql_felder->getRows();$i++)
      {
        $felder .= $sql_felder->getValue("f1");

        if ($i<$max-1) $felder .= ",";
        $sql_felder->counter++;

      }

      // ---------- SQL AUFBAUEN
      $sql = "select id,".$felder." from ".$table["table_name"]."";

    }



    if(count($rex_xform_filter)>0)
    {
      $where = true;
      $sql .= ' where ';
      $sql_filter = '';
      foreach($rex_xform_filter as $k => $v)
      {
        if($sql_filter != '') {
          $sql_filter .= ' AND ';
        }
        $sql_filter .= '`'.$k.'`="'.$v.'"';
      }
      $sql .= $sql_filter;
    }

    if($rex_xform_search == 1)
    {
      if(is_array($rex_xform_searchfields) && count($rex_xform_searchfields)>0 && $rex_xform_searchtext != ""){
        if(!$where) {
          $sql .= ' WHERE ';
        }
        $sf = array();
        foreach($rex_xform_searchfields as $cs) {
          if($rex_xform_searchtext== "(empty)") $sf[] = ' (`'.$cs.'` = "" or `'.$cs.'` IS NULL) ';
          elseif($rex_xform_searchtext== "!(empty)") $sf[] = ' (`'.$cs.'` <> "" and `'.$cs.'` IS NOT NULL) ';
          else $sf[] = " `".$cs."` LIKE  '%".$rex_xform_searchtext."%'";
        }
        if(count($sf)>0) {
          $sql .= '( '.implode(" OR ",$sf).' )';
        }
      }
    }

    // ********************************************* Export
    // export is here because the query has been build here.
    if($func == "export" && $this->hasDataPageFunction("export"))
    {
      include $REX["INCLUDE_PATH"].'/addons/xform/plugins/manager/pages/data_export.inc.php';
    }

    // ---------- LISTE AUSGEBEN
    if(!isset($table["list_amount"]) || $table["list_amount"]<1) { $table["list_amount"] = 30; }

    $list = rex_list::factory($sql,$table["list_amount"]);
    $list->setColumnFormat('id', 'Id');

    foreach($this->getLinkVars() as $k => $v) {
      $list->addParam($k, $v);
    }
    $list->addParam("table_name", $table["table_name"]);

    if(count($rex_xform_filter)>0) { foreach($rex_xform_filter as $k => $v) { $list->addParam('rex_xform_filter['.$k.']',$v); } }
    if(count($rex_xform_set)>0) { foreach($rex_xform_set as $k => $v) { $list->addParam('rex_xform_set['.$k.']',$v); } }
    if(count($rex_xform_manager_opener)>0) { foreach($rex_xform_manager_opener as $k => $v) { $list->addParam('rex_xform_manager_opener['.$k.']',$v); } }

    if($rex_xform_search != "") { $list->addParam("rex_xform_search",$rex_xform_search); };
    if($rex_xform_searchtext != "") { $list->addParam("rex_xform_searchtext",$rex_xform_searchtext); };
    if(count($rex_xform_searchfields)>0) { foreach($rex_xform_searchfields as $k => $v) { $list->addParam('rex_xform_searchfields['.$k.']',$v); } }

    $list->setColumnParams("id", array("data_id"=>"###id###", "func"=>"edit" ));
    $list->setColumnSortable("id");

    foreach($fields as $field) {

      // CALL CLASS'S LIST VALUE METHOD IF AVAILABLE
      if($field['list_hidden']==0 && isset($field['type_name'])) {
        if(!class_exists('rex_xform_'.$field['type_name'])) {
          rex_xform::includeClass($field['type_id'],$field['type_name']);
        }
        if(method_exists('rex_xform_'.$field['type_name'],'getListValue')) {
          $list->setColumnFormat(
            $field["f1"],
            'custom',
            array('rex_xform_'.$field['type_name'], 'getListValue'),
            array('field' => $field, 'fields' => $fields));
        }
      }

      if($field["type_id"] == "value") {
        if($field["list_hidden"] == 1) {
          $list->removeColumn($field["f1"]);
        }else {
          $list->setColumnSortable($field["f1"]);
          $list->setColumnLabel($field["f1"],$field["f2"]);
        }
      }
    }

    if(isset($rex_xform_manager_opener["id"])) {
      $list->addColumn('&uuml;bernehmen','<a href="javascript:xform_manager_setData('.$rex_xform_manager_opener["id"].',###id###,\'###'.$rex_xform_manager_opener["field"].'### [id=###id###]\','.$rex_xform_manager_opener["multiple"].')">&uuml;bernehmen</a>',-1,"a");
    }else
    {
      $list->addColumn($I18N->msg('edit'),$I18N->msg('edit'));
      $list->setColumnParams($I18N->msg('edit'), array("data_id"=>"###id###","func"=>"edit","start"=>rex_request("start","string")));

      if($this->hasDataPageFunction("delete"))
      {
        $list->addColumn($I18N->msg('delete'),$I18N->msg('delete'));
        $list->setColumnParams($I18N->msg('delete'), array("data_id"=>"###id###","func"=>"delete"));
        $list->addLinkAttribute($I18N->msg('delete'), 'onclick', 'return confirm(\' id=###id### '.$I18N->msg('delete').' ?\')');
      }
    }

    $list = rex_register_extension_point('XFORM_DATA_LIST', $list, array("table"=>$table));
    echo '
    <div class="rex-addon-output">
      <div class="rex-hl2" style="font-size:12px;font-weight:bold;">
        <span style="float:left;">Datensatz: ';

    // ADD LINK
    if($this->hasDataPageFunction("add"))
    {
      echo '<a href="index.php?'.$link_vars.'&func=add&'.$em_url.$em_rex_list.'">'.$I18N->msg("add").'</a> | ';
    }

    // SEARCH LINK
    echo '<a href="#" id="searchtoggler">'.$I18N->msg("search").'</a>';

    echo '</span>';

    // INFO LINK
    echo '<span style="float:right;">Tabelle: <a href="#" id="infotoggler">'.$I18N->msg("pool_file_details").'</a>';

    if(($table["export"] == 1 && $this->hasDataPageFunction("export")) or ($table["import"] == 1 && $this->hasDataPageFunction("import")))
    {
      // EXPORT FUNC
      if($table["export"] == 1 && $this->hasDataPageFunction("export")) {
        echo ' | <a href="index.php?'.$link_vars.'&func=export&'.$em_url.$em_rex_list.'">'.$I18N->msg("export").'</a>';
      }
      // IMPORT FUNC
      if(!$popup && $table["import"] == 1 && $this->hasDataPageFunction("import")) {
        echo ' | <a href="index.php?'.$link_vars.'&func=import">'.$I18N->msg("import").'</a>';
      }
      // TRUNCATE FUNC
      if($this->hasDataPageFunction("truncate_table")) {
        echo ' | <a href="index.php?'.$link_vars.'&func=truncate_table&'.$em_url.$em_rex_list.'" id="truncate-table">'.$I18N->msg("truncate_table").'</a>';
      }
    }

    echo '</span><br style="clear:both;" /></div></div>';

    // SEARCHBLOCK
    $display = rex_request('rex_xform_search')==1 ? 'block' : 'none';
    echo '<div id="searchblock" style="display:'.$display.';">'.$suchform.'</div>';

    // INFOBLOCK
    echo '<div id="infoblock" style="display:none;/*padding:10px;*/"  class="rex-addon-output">
    <div class="rex-hl2" style="font-size:12px;font-weight:bold;">'.$I18N->msg("pool_file_details").'</div>
    <ul>';
    echo '<li><b>'.$I18N->msg("xform_table_name").'</b>: '.$table["table_name"].'</li>';
    if($table["description"] != "") echo '<li><b>'.$I18N->msg("xform_description").'</b>:'.nl2br($table["description"]).'</li>';
    if(isset($rex_xform_manager_opener["info"])) { echo '<li><b>'.$I18N->msg("openerinfo").'</b>: '.htmlspecialchars($rex_xform_manager_opener["info"]).'</li>'; }
    echo '</ul></div><br style="clear:both;" />';

    echo $list->get();

    echo '

    <script type="text/javascript">
      jQuery("#infotoggler").click(function(){jQuery("#infoblock").slideToggle("fast");});
      jQuery("#searchtoggler").click(function(){jQuery("#searchblock").slideToggle("fast");});
      jQuery("#xform_help_empty_toggler").click(function(){jQuery("#xform_help_empty").slideToggle("fast");});
      jQuery("#xform_search_reset").click(function(){window.location.href = "index.php?page=xform&subpage=manager&tripage=data_edit&table_name='.$table["table_name"].'&rex_xform_search=1";});
      jQuery("a.#truncate-table").click(function(){if(confirm("'.$I18N->msg("truncate_table_confirm").'")){return true;} else {return false;}});
    </script>';

  }

} // end: $show_editpage
