<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

if (!function_exists('rex_xform_manager_checkField'))
{
  function rex_xform_manager_checkField($l,$v,$p)
  {
    return rex_xform_manager::checkField($l,$v,$p);
  }
}

if (!function_exists('rex_xform_manager_checkLabelInTable'))
{
  function rex_xform_manager_checkLabelInTable($l,$v)
  {
    return rex_xform_manager::checkLabelInTable($l,$v);
  }
}

if (!class_exists('rex_xform_manager'))
{
  class rex_xform_manager
  {

    var $table = "";
    var $linkvars = array();
    var $type = "";
    var $dataPageFunctions = array();
    var $DataPageFilterFields = array();

    function rex_xform_manager()
    {
      global $REX;
      $this->setDataPageFunctions();

    }


    // ----- Permissions
    function setDataPageFunctions($f = array("add","delete","search","export","import","truncate_table"))
    {
      $this->dataPageFunctions = $f;
    }

    function hasDataPageFunction($f)
    {
      return in_array($f,$this->dataPageFunctions)? TRUE : FALSE;
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
      global $REX,$I18N;
      include $REX["INCLUDE_PATH"]."/addons/xform/plugins/manager/inc/data_edit.inc.php";
    }

    public function getDataListQueryWhere($rex_xform_filter = array(), $rex_xform_searchfields = array(), $rex_xform_searchtext = "")
    {
      $sql = "";
      if(count($rex_xform_filter)>0) {
        $sql_filter = '';
        foreach($rex_xform_filter as $k => $v) {
          if($sql_filter != '') {
            $sql_filter .= ' AND ';
          }
          $sql_filter .= '`'.$k.'`="'.$v.'"';
        }
        $sql .= $sql_filter;
      }
  
      if(is_array($rex_xform_searchfields) && count($rex_xform_searchfields)>0 && $rex_xform_searchtext != ""){
        $sf = array();
        foreach($rex_xform_searchfields as $cs) {
          if($rex_xform_searchtext== "(empty)") {
            $sf[] = ' (`'.$cs.'` = "" or `'.$cs.'` IS NULL) ';
          } elseif($rex_xform_searchtext== "!(empty)") { 
            $sf[] = ' (`'.$cs.'` <> "" and `'.$cs.'` IS NOT NULL) ';
          }else {
            $sf[] = " `".$cs."` LIKE  '%".$rex_xform_searchtext."%'";
          }
        }
        if(count($sf)>0) {
          $sql .= '( '.implode(" OR ",$sf).' )';
        }
      }
      
      if($sql != "") { $sql = ' where '.$sql; }
      return $sql;
    }



    public function getDataListQuery($table, $rex_xform_filter = array(), $rex_xform_searchfields = array(), $rex_xform_searchtext = "")
    {
    
      $where = false;

      $sql = "select * from ".$table["table_name"];
  
      $sql_felder = new rex_sql;
      $sql_felder->setQuery("SELECT * FROM rex_xform_field WHERE table_name='".$table["table_name"]."' AND type_id='value' ORDER BY prio");
  
      $felder = '';
      $max = $sql_felder->getRows();
      if ($max > 0) {
        for($i=0;$i<$sql_felder->getRows();$i++) {
          $felder .= '`'.$sql_felder->getValue("f1").'`';
          if ($i<$max-1) {
            $felder .= ",";
          }
          $sql_felder->counter++;
        }
        $fieldarray = explode(',', $felder);
        $sortarray = array('asc', 'desc');
        (in_array('`'.$table['list_sortfield'].'`',$fieldarray)) ? $sortfield = $table['list_sortfield'] : $sortfield = 'id';
        (in_array(strtolower($table['list_sortorder']), $sortarray)) ? $sortorder = strtolower($table['list_sortorder']) : $sortorder = 'asc';
        
        $sql = "select `id`,".$felder." from `".$table["table_name"]."` order by ".$sortfield." ".$sortorder;
      }
    
      $sql .= $this->getDataListQueryWhere($rex_xform_filter, $rex_xform_searchfields, $rex_xform_searchtext);
      $sql = rex_register_extension_point('XFORM_DATA_LIST_SQL',$sql);
    
      return $sql;
    }

    





    // ---------------------------------- table functions


    public function setFilterTable($table)
    {
      $this->filterTables[$table] = $table;
    }

    public function getFilterTables()
    {
      if(isset($this->filterTables) && is_array($this->filterTables))
      return $this->filterTables;
      else
      return array();
    }

    public function getTables()
    {
      global $REX;

      $where = '';
      foreach($this->getFilterTables() as $t) {
        if($where != "")
        $where .= ' OR ';
        $where .= '(table_name = "'.$t.'")';
      }

      if($where != "") {
        $where = ' where '.$where;
      }

      $tb = rex_sql::factory();
      // $tb->debugsql = 1;
      $tb->setQuery('select * from rex_xform_table '.$where.' order by prio,name');
      return $tb->getArray();
    }





    // ---------------------------------- field functions

    function getFieldPage()
    {
      // TODO
      global $REX,$I18N;
      include $REX["INCLUDE_PATH"]."/addons/xform/plugins/manager/inc/table_field.inc.php";
    }



    // ----- Allgemeine Methoden



    // ----- Felder

    function getTableFields($table)
    {
      global $REX;

      $tb = rex_sql::factory();
      $tb->setQuery('select * from rex_xform_field where table_name="'.$table.'" order by prio');
      return $tb->getArray();
    }

    function checkField($l,$v,$p)
    {
      global $REX;
      $q = 'select * from rex_xform_field where table_name="'.$p["table_name"].'" and '.$l.'="'.$v.'" LIMIT 1';
      $c = rex_sql::factory();
      // $c->debugsql = 1;
      $c->setQuery($q);
      if($c->getRows()>0)
      {
        // FALSE -> Warning = TRUE;
        return TRUE;
      }else
      {
        return FALSE;
      }
    }

    function checkLabelInTable($l,$v)
    {
      global $REX;
      $q = 'select * from rex_xform_table where '.$l.'="'.$v.'" LIMIT 1';
      $c = rex_sql::factory();
      // $c->debugsql = 1;
      $c->setQuery($q);
      if($c->getRows()>0)
      {
        // FALSE -> Warning = TRUE;
        return TRUE;
      }else
      {
        return FALSE;
      }
    }

    function setFilterFields($DataPageFilterFields = array()) {
      $this->DataPageFilterFields = $DataPageFilterFields;
    }

    function getFilterFields() {
      if(!is_array($this->DataPageFilterFields)) {
        return array();
      }else {
        return $this->DataPageFilterFields;
      }
    }

    function createTable($mifix = "", $data_table, $params = array(), $debug = FALSE)
    {

      // Tabelle erstellen wenn noch nicht vorhanden
      $c = rex_sql::factory();
      $c->debugsql = $debug;
      $c->setQuery('CREATE TABLE IF NOT EXISTS `'.$data_table.'` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY );');

      // Tabellenset in die Basics einbauen, wenn noch nicht vorhanden
      $c = new rex_sql;
      $c->debugsql = $debug;
      $c->setQuery('DELETE FROM rex_xform_table where table_name="'.$data_table.'"');
      $c->setTable('rex_xform_table');

      $params["table_name"] = $data_table;
      if(!isset($params["status"]))
      $params["status"] = 1;

      if(!isset($params["name"]))
      $params["name"] = 'Tabelle "'.$data_table.'"';

      if(!isset($params["prio"]))
      $params["prio"] = 100;

      if(!isset($params["search"]))
      $params["search"] = 0;

      if(!isset($params["hidden"]))
      $params["hidden"] = 0;

      if(!isset($params["export"]))
      $params["export"] = 0;

      foreach($params as $k => $v)
      {
        $c->setValue($k, $v);
      }

      $c->insert();

      return TRUE;

    }

    function addDataFields($data_table, $fields, $debug = FALSE)
    {

      // definition fields table
      foreach($fields as $field)
      {

        $table_name = $field['table_name']; // 'user';
        $type_id = $field['type_id']; // 'value';
        $type_name = $field['type_name']; // 'select';
        $f1 = $field['f1']; // 'status';

        if(!in_array($type_id,rex_xform::getTypes()))
        return FALSE;

        $gs = rex_sql::factory();
        $gs->debugsql = $debug;
        $gs->setQuery('delete from rex_xform_field where table_name="'.$table_name.'" and type_id="'.$type_id.'" and type_name="'.$type_name.'" and f1="'.$f1.'"');

        // fielddaten - datensatz anlegen
        $af = rex_sql::factory();
        $af->debugsql = $debug;
        $af->setTable('rex_xform_field');
        foreach($field as $k => $v)
        {
          $af->setValue($k, $v);
        }
        if(!$af->insert())
        return FALSE;

        // datentabelle - spalte hinzufügen
        if($type_id == "value" && $type_name != "" && $f1 != "")
        {
          if ($classname = rex_xform::includeClass('value',$type_name)){ }else { return FALSE; }
          $cl = new $classname;
          $definitions = $cl->getDefinitions();
          if(isset($definitions["dbtype"]) && $definitions["dbtype"]!="")
          {
            // Structur in spalte anpassen
            $af = rex_sql::factory();
            $af->debugsql = $debug;
            $af->setQuery('ALTER TABLE `'.$data_table.'` ADD `'.$f1.'` '.$definitions["dbtype"].' NOT NULL ;');
          }
        }

      }

      return TRUE;
    }



    function generateAll($f = array())
    {
      global $REX;

      $types = rex_xform::getTypeArray();
      foreach($this->getTables() as $table)
      {

        // ********** Table schon vorhanden ?, wenn nein, dann anlegen
        $c = rex_sql::factory();
        // $c->debugsql = 1;
        $c->setQuery('CREATE TABLE IF NOT EXISTS `'.$table["table_name"].'` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY )');

        // Felder merken, erstellen und eventuell loeschen
        $c->setQuery('SHOW COLUMNS FROM `'.$table["table_name"].'`');
        $saved_columns = $c->getArray();

        foreach($this->getTableFields($table["table_name"]) as $field)
        {
          $type_name = $field["type_name"];
          $type_id = $field["type_id"];

          if($type_id == "value")
          {
            $type_label = $field["f1"];
            $dbtype = $types[$type_id][$type_name]['dbtype'];

            if($dbtype != "none" && $dbtype != "")
            {
              // Column schon vorhanden ?
              $add_column = TRUE;
              foreach($saved_columns as $uu => $vv)
              {
                if ($vv["Field"] == $type_label)
                {
                  $add_column = FALSE;
                  unset($saved_columns[$uu]);
                  break;
                }
              }

              // Column erstellen
              if($add_column)
              {
                $c->setQuery('ALTER TABLE `'.$table["table_name"].'` ADD `'.$type_label.'` '.$dbtype.' NOT NULL');
              }
            }

          }

        }

        if(isset($f["delete_fields"]) && $f["delete_fields"] === TRUE)
        {
          foreach($saved_columns as $uu => $vv)
          {
            if ($vv["Field"] != "id")
            {
              $c->setQuery('ALTER TABLE `'.$table["table_name"].'` DROP `'.$vv["Field"].'` ');
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

  }

}
