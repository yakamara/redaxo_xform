<?php

class rex_xform_manager_table_api
{

  static
    $table_fields = array("status", "name", "description", "list_amount", "prio", "search", "hidden", "export", "import"),
    $debug = false,
    $rex_xform_table = "rex_xform_table",
    $rex_xform_field = "rex_xform_field";

  // ---------- TABLES

  static public function setTable(array $table, array $table_fields = array())
  {

    if (!isset($table['table_name'])) {
        throw new Exception('table[table_name] must be set');
    }
    $table_name = $table['table_name'];

    $currentTable = self::getTable($table_name);

    if (count($currentTable) == 0) {

      // Insert
      $table_insert = new rex_sql;
      $table_insert->debugsql = self::$debug;
      $table_insert->setTable(self::$rex_xform_table);
      $table_insert->setValue('table_name', $table_name);

      if(!isset($table["name"]) || $table["name"] == "") {
        $table["name"] = $table["table_name"];
      }

      foreach(self::$table_fields as $field) {
        if(isset($table[$field])) {
          $table_insert->setValue($field, $table[$field]);
        }
      }
      $table_insert->insert();

    } else {

      // Update
      foreach(self::$table_fields as $field) {
        if(isset($table[$field])) {
          $currentTable[$field] = $table[$field];
        }
      }

      if(!isset($table["name"]) || $table["name"] == "") {
        $table["name"] = $table["table_name"];
      }

      $table_update = new rex_sql;
      $table_update->debugsql = self::$debug;
      $table_update->setTable(self::$rex_xform_table);
      $table_update->setWhere('table_name = "'.mysql_real_escape_string($table_name).'"');

      foreach(self::$table_fields as $field) {
        if(isset($table[$field])) {
          $table_update->setValue($field, $table[$field]);
        }
      }
      $table_update->update();

    }

    if (count($table_fields) > 0) {
      foreach($table_fields as $field) {
        self::setTableField($table_name, $field);
      }
    }

    self::generateTablesAndFields();

    return self::getTable($table_name);

  }

  static public function setTables(array $tables)
  {
      foreach ($tables as $table) {
        self::setTable($table);
      }
  }

  static public function getTable( $table_name )
  {

    $t = rex_sql::factory();
    $t->debugsql = self::$debug;
    $tables = $t->getArray('select * from ' . self::$rex_xform_table . ' where table_name="' . mysql_real_escape_string($table_name) . '"');

    if (count($tables) > 1) {
      throw new Exception('only one tabledefinition is allowed. '.count($tables).' are found ['.$table_name.'');

    } else if (count($tables) == 1) {
      return $tables[0];

    } else {
      return array();

    }

  }

  static public function getTables()
  {

    $t = rex_sql::factory();
    $t->debugsql = self::$debug;
    return $t->getArray('select * from ' . self::$rex_xform_table );

  }


  static public function removeTable($table_name)
  {
    $t = rex_sql::factory();
    $t->debugsql = self::$debug;
    $t->setQuery('delete from ' . self::$rex_xform_table . ' where table_name="' . mysql_real_escape_string($table_name) . '"');

    $remove_fields = self::getTableFields($table_name, array());
    foreach($remove_fields as $remove_field) {
      self::removeTablefield($table_name, $remove_field);
    }

  }




  // ---------- FIELDS

  static public function setTableField($table_name, array $table_field)
  {
    if ($table_name == "") {
      throw new Exception('table_name must be set');
    }

    if (count($table_field) == 0) {
      throw new Exception('field must be a filled array');
    }

    $fieldIdentifier = array(
      'type_id' => $table_field["type_id"],
      'type_name' => $table_field["type_name"],
      'name' => $table_field["name"]
    );

    $currentFields = self::getTableFields($table_name, $fieldIdentifier);

    // validate specials
    if ($table_field["type_id"] == 'validate') {
      $table_field["list_hidden"] = 1;
      $table_field["search"] = 0;
    }

    self::createMissingFieldColumns($table_field);

    if (count($currentFields) > 1) {
      throw new Exception('more than one field found for table: '.$table_name.' with Fieldidentifier: '.implode(", ",$fieldIdentifier).'');

    } else if (count($currentFields) == 0) {

      // Insert
      $field_insert = new rex_sql;
      $field_insert->debugsql = self::$debug;
      $field_insert->setTable(self::$rex_xform_field);
      $field_insert->setValue('table_name', $table_name);

      foreach($table_field as $field_name => $field_value) {
        $field_insert->setValue($field_name, $field_value);
      }
      $field_insert->insert();

    } else {

      // Update
      $currentField = $currentFields[0];
      foreach($table_field as $field_name => $field_value) {
        $currentField[$field_name] = $field_value;
      }

      $field_update = new rex_sql;
      $field_update->debugsql = self::$debug;
      $field_update->setTable(self::$rex_xform_field);

      $add_where = array();
      foreach($fieldIdentifier as $field => $value) {
        $add_where[] = '`'.mysql_real_escape_string($field).'`="'.mysql_real_escape_string($table_name).'"';
      }

      $where = 'table_name="' . mysql_real_escape_string($table_name) . '"';
      if (count($add_where)>0) {
        $where .= ' and ('.implode(' and ', $add_where).') ';

      }

      $field_update->setWhere($where);

      foreach($table_field as $field_name => $field_value) {
        $field_update->setValue($field_name, $field_value);
      }
      $field_update->update();

    }

  }

  static public function getTableFields($table_name, array $fieldIdentifier = array())
  {

    $add_where = array();
    foreach($fieldIdentifier as $field => $value) {
      $add_where[] = '`'.mysql_real_escape_string($field).'`="'.mysql_real_escape_string($value).'"';
    }

    $where = ' where table_name="' . mysql_real_escape_string($table_name) . '"';
    if (count($add_where)>0) {
      $where .= ' and ('.implode(' and ', $add_where).') ';

    }

    $f = rex_sql::factory();
    $f->debugsql = self::$debug;
    return $f->getArray('select * from ' . self::$rex_xform_field . $where);

  }

  static public function removeTablefield($table_name, array $fieldIdentifier)
  {

    $add_where = array();
    foreach($fieldIdentifier as $field => $value) {
      $add_where[] = '`'.mysql_real_escape_string($field).'`="'.mysql_real_escape_string($value).'"';
    }

    $where = ' where table_name="' . mysql_real_escape_string($table_name) . '"';
    if (count($add_where)>0) {
      $where .= ' and ('.implode(' and ', $add_where).') ';

    }

    $f = rex_sql::factory();
    $f->debugsql = self::$debug;
    return $f->getArray('delete from ' . self::$rex_xform_field . $where);

  }



  // ---------- MIGRATION und Erstellung

  static public function migrateTable($table_name)
  {
    global $REX;
    $columns = rex_sql::showColumns($table_name);

    if (count($columns) == 0) {
      throw new Exception( $table_name . ' does not exists or no fields available');

    }

    $table = array(
      'table_name' => $table_name,
      'status' => 1
    );

    self::setTable($table);

    foreach($columns as $column) {
      if ($column["name"] != "id") {
        self::migrateField($table_name, $column);

      }

    }

  }

  static function migrateField($table_name, $column)
  {
    if ($column["name"] == "id") {
      return array();
    }

    $fields = array();

    // http://www.tutorialspoint.com/mysql/mysql-data-types.htm
    /*
     * TODO: missing types
    COLUMN_NAME z.B. "name"
    DATA_TYPE z.B. "int"
    COLUMN_TYPE z.B. "int(11)"
    COLUMN_DEFAULT z.B. "5"
    */

    preg_match('@^(.*)(\(.*\)*)@i', $column["type"], $r);

    if (isset($r[1])) {
      $column["clean_type"] = $r[1];
    } else {
      $column["clean_type"] = $column["type"];
    }

    switch($column["clean_type"]) {

      case("varchar"):
        $fields[] = array(
          'type_id' => 'value',
          'type_name' => 'text',
          'name' => $column["name"],
          'label' => $column["name"],
          'default' => (string) $column["default"],
          'no_db' => 0
        );

        preg_match('@^varchar\((.*)\)@i', $column["type"], $r);

        $size = $r[1];

        $fields[] = array(
          'type_id' => 'validate',
          'type_name' => 'size_range',
          'name' => $column["name"],
          'max' => $size,
          'message' => 'error: size max in '.$column["name"].' is '.$size
        );

        break;

      case("char"):
        $fields[] = array(
          'type_id' => 'value',
          'type_name' => 'text',
          'name' => $column["name"],
          'label' => $column["name"],
          'default' => (string) $column["default"],
          'no_db' => 0
        );

        preg_match('@^char\((.)*\)@i', $column["type"], $r);
        $size = $r[1];

        /*
        $fields[] = array(
          'type_id' => 'validate',
          'type_name' => 'size',
          'name' => $column["name"],
          'size' => $size,
          'message' => 'error: size max in '.$column["name"].' is '.$size
        );
        */

        break;


      case("int"):
        $fields[] = array(
          'type_id' => 'value',
          'type_name' => 'text',
          'name' => $column["name"],
          'label' => $column["name"],
          'default' => (string) $column["default"],
          'no_db' => 0
        );

        break;

      case("blob"):
      case("tinyblob"):
      case("mediumblob"):
      case("longblob"):
        // do nothing.
        break;

      case("text"):
      case("tinytext"):
      case("mediumtext"):
      case("longtext"):
      default:
        $fields[] = array(
          'type_id' => 'value',
          'type_name' => 'textarea',
          'name' => $column["name"],
          'label' => $column["name"],
          'default' => (string) $column["default"],
          'no_db' => 0
        );
        break;

    }

    foreach($fields as $field) {
      self::setTableField($table_name, $field);
    }

  }

  static function createMissingFieldColumns($field)
  {
    $columns = array();
    foreach (rex_sql::showColumns(self::$rex_xform_field) as $column) {
      $columns[$column['name']] = true;
    }

    $alterTable = array();
    foreach($field as $column => $value) {
      if (!isset($columns[$column])) {
        $alterTable[] = 'ADD `' . mysql_real_escape_string($column) . '` TEXT NOT NULL';
      }
      $columns[$column] = true;
    }

    if (count($alterTable)) {
      $alter = rex_sql::factory();
      $alter->debugsql = self::$debug;
      $alter->setQuery('ALTER TABLE `' .  self::$rex_xform_field . '` ' . implode(',', $alterTable));
    }

  }

  static function generateTablesAndFields($delete_old = false)
  {

    $types = rex_xform::getTypeArray();
    foreach (self::getTables() as $table) {

      $c = rex_sql::factory();
      $c->debugsql = self::$debug;
      $c->setQuery('CREATE TABLE IF NOT EXISTS `' . $table['table_name'] . '` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY )');

      // remember fields, create and in case delete
      $c->setQuery('SHOW COLUMNS FROM `' . $table['table_name'] . '`');
      $saved_columns = $c->getArray();

      foreach (self::getTableFields($table['table_name']) as $field) {
        $type_name = $field['type_name'];
        $type_id = $field['type_id'];

        if ($type_id == 'value') {
          $type_label = $field['name'];
          $dbtype = $types[$type_id][$type_name]['dbtype'];

          if ($dbtype != 'none' && $dbtype != '') {
            $add_column = true;
            foreach ($saved_columns as $uu => $vv) {
              if ($vv['Field'] == $type_label) {
                $add_column = false;
                unset($saved_columns[$uu]);
                break;
              }
            }

            if ($add_column) {
              $c->setQuery('ALTER TABLE `' . $table['table_name'] . '` ADD `' . $type_label . '` ' . $dbtype . ' NOT NULL');
            }
          }

        }

      }

      if ($delete_old === true) {
        foreach ($saved_columns as $uu => $vv) {
          if ($vv['Field'] != 'id') {
            $c->setQuery('ALTER TABLE `' . $table['table_name'] . '` DROP `' . $vv['Field'] . '` ');
          }
        }
      }

    }
  }


}
