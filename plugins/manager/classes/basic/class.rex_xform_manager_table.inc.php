<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_manager_table implements ArrayAccess
{

    var $values = array();
    var $table_fields = array();

    static $debug = false;
    static $db_table_table = 'rex_xform_table';
    static $db_field_table = 'rex_xform_field';

    function __construct( array $values)
    {
        global $REX, $I18N;
        if (!is_array($values) || count($values) == 0) {
            throw new Exception($I18N->msg('xform_table_not_found'));
        }
        $this->values = $values;

        $tb = rex_sql::factory();
        $tb->debugsql = self::$debug;
        $tb->setQuery('select * from ' . self::$db_field_table . ' where table_name="' . mysql_real_escape_string($this->getTablename()) . '" order by prio');

        $this->table_fields = array();
        foreach($tb->getArray() as $field) {
            $this->table_fields[] = new rex_xform_manager_field($field);
        }
    }

    static public function getByTablename($table_name)
    {
      global $REX, $I18N;

      $tb = rex_sql::factory();
      $tb->debugsql = self::$debug;
      $tables = $tb->getArray('select * from ' . self::$db_table_table . ' where table_name = "' . mysql_real_escape_string($table_name) . '"');
      if (count($tables) != 1) {
        throw new Exception($I18N->msg('xform_table_not_found'));
      }

      return new rex_xform_manager_table($tables[0]);

    }

    // -------------------------------------------------------------------------

    public function getTableName()
    {
        return $this->values["table_name"];
    }

    public function getName()
    {
        return $this->values["name"];
    }

    public function getId()
    {
        return $this->values["id"];
    }

    public function hasId()
    {
      $columns = rex_sql::showColumns($this->getTableName());
      foreach($columns as $column) {
        if ($column["name"] == "id" && $column["extra"] == "auto_increment") {
          return true;
        }
      }
      return false;
    }

    public function isActive()
    {
        if ($this->values["status"] == 1) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if ($this->values["hidden"] == 1) {
            return true;
        }
        return false;
    }

    public function isSearchable()
    {
        if ($this->values["search"] == 1) {
            return true;
        }
        return false;
    }

    public function isImportable()
    {
      if ($this->values["import"] == 1) {
        return true;
      }
      return false;
    }

    public function isExportable()
    {
      if ($this->values["export"] == 1) {
        return true;
      }
      return false;
    }

    public function getSortFieldName()
    {
        return $this->values['list_sortfield'];
    }

    public function getSortOrderName()
    {
        return $this->values['list_sortorder'];
    }

    public function getListAmount()
    {
        if (!isset($this->values['list_amount']) || $this->values['list_amount'] < 1) {
            $this->values['list_amount'] = 30;
        }
        return $this->values['list_amount'];
    }


    public function getDescription()
    {
        return $this->values['description'];
    }

    // Fields of XForm Definitions
    public function getTableFields()
    {
        return $this->table_fields;
    }

    // Database Fielddefinition
    public function getColumns()
    {
        $columns = rex_sql::showColumns($this->getTableName());
        $c = array();
        foreach ($columns as $column) {
          $c[$column['name']] = $column;
        }
        unset($c['id']);
        return $c;
    }

    public function getMissingFields()
    {
        $xfields = self::getXFormFieldsByType($this->getTableName());
        $rfields = self::getColumns();

        $c = array();
        foreach ($rfields as $k => $v) {
            if (!array_key_exists($k, $xfields)) {
                $c[$k] = $k;
            }
        }
        return $c;
    }


    public function getPermKey()
    {
        return 'xform[table:' . $this->getTableName() . ']';
    }

    public function getArray()
    {
        return $this->values;

    }



    public function removeRelationTableRelicts()
    {
        $relation_exists = false;
        foreach($this->getTableFields() as $field) {
            if ($field->getElement("relation_table")) {
                $relation_exists = true;
            }
        }
        if (!$relation_exists) {
            return;
        }

        $sql = rex_sql::factory();
        $sql->setQuery('
                  SELECT `table`, relation_table
                  FROM ' . self::$db_field_table . '
                  WHERE table_name="' . $sql->escape($this->getTableName()) . '" AND type_id="value" AND type_name="be_manager_relation" AND relation_table != ""
              ');
        $deleteSql = rex_sql::factory();
        while ($sql->hasNext()) {
            $relationTableFields = self::getRelationTableFields($sql->getValue('relation_table'), $this->getTableName(), $sql->getValue('table'));
            if ($relationTableFields['source'] && $relationTableFields['target']) {
                $relationTable = $deleteSql->escape($sql->getValue('relation_table'));
                $deleteSql->setQuery('
                  DELETE FROM `' . $relationTable . '`
                  WHERE NOT EXISTS (SELECT * FROM `' . $this->getTableName() . '` WHERE id = ' . $relationTable . '.`' . $deleteSql->escape($relationTableFields['source']) . '`)
                ');
            }
            $sql->next();
        }
    }


    // -------------------------------------------------------------------------

    static function getTables() {

      $table_array = rex_sql::factory();
      $table_array->debugsql = self::$debug;
      $table_array = $table_array->getArray('select * from ' . self::$db_table_table . ' order by prio');

      $tables = array();
      foreach($table_array as $t) {
          $tables[] = new rex_xform_manager_table($t);
      }
      return $tables;

    }






  // -------------------------------------------------------------------------

    static function checkTableName($table)
    {
        preg_match("/([a-z])+([0-9a-z\_])*/", $table, $matches);
        if (count($matches) > 0 && current($matches) == $table) {
            return true;
        }
        return false;
    }


    // -------------------------------------------- xform custom function

    static function xform_checkTableName($label = '', $table = '', $params = '')
    {
        return self::checkTableName($table);
    }

    static function xform_existTableName($l = '', $v = '', $params = '')
    {
        global $REX;
        $q = 'select * from ' . self::$db_table_table . ' where table_name="' . $v . '" LIMIT 1';
        $c = rex_sql::factory();
        // $c->debugsql = 1;
        $c->setQuery($q);
        if ($c->getRows() > 0) {
            return true;
        }
        return false;
    }


    // -------------------------------------------------------------------------

    static function getMaximumTablePrio()
    {
        global $REX;
        $sql = 'select max(prio) as prio from ' . self::$db_table_table . '';
        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $gf->setQuery($sql);
        return $gf->getValue('prio');
    }

    static function getMaximumPrio($table_name)
    {
        global $REX;
        $sql = 'select max(prio) as prio from ' . self::$db_field_table . ' where table_name="' . $table_name . '"';
        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $gf->setQuery($sql);
        return $gf->getValue('prio');

    }

    static function getXFormFields($table_name, $filter = array())
    {
        global $REX;
        $add_sql = '';
        foreach ($filter as $k => $v) {
            $add_sql = 'AND `' . mysql_real_escape_string($k) . '`="' . mysql_real_escape_string($v) . '"';
        }

        $sql = 'select * from ' . rex_xform_manager_table::$db_field_table . ' where table_name="' . $table_name . '" ' . $add_sql . ' order by prio';
        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $gf->setQuery($sql);
        $ga = $gf->getArray();

        $c = array();
        foreach ($ga as $v) {
            $c[$v['name']] = $v;
        }
        return $c;

    }

    static function getXFormFieldsByType($table_name, $type_id = 'value')
    {
        return self::getXFormFields($table_name, array('type_id' => $type_id));

    }

    static function getFields($table_name)
    {
        $columns = rex_sql::showColumns($table_name);
        $c = array();
        foreach ($columns as $column) {
            $c[$column['name']] = $column;
        }
        unset($c['id']);
        return $c;
    }

    public static function getRelationTableFields($relationTable, $sourceTable, $targetTable)
    {
        $source = null;
        $target = null;
        foreach (self::getRelations($relationTable) as $relation) {
            if (!$source && $relation['table'] === $sourceTable) {
                $source = $relation['name'];
            }
            if (!$target && $relation['table'] === $targetTable) {
                $target = $relation['name'];
            }
            if ($source && $target) {
                return array('source' => $source, 'target' => $target);
            }
        }
        return array('source' => null, 'target' => null);
    }

    public static function getRelations($table)
    {
        global $REX;
        static $relations;

        if (!isset($relations)) {
            $relations = array();
            $sql = rex_sql::factory();
            $data = $sql->getArray('SELECT * FROM `' . rex_xform_manager_table::$db_field_table . '` WHERE type_id="value" AND type_name="be_manager_relation"');
            foreach ($data as $row) {
                $relations[$row['table_name']][$row['name']] = $row;
            }
        }

        return isset($relations[$table]) ? $relations[$table] : array();
    }

    public static function getRelation($table, $column)
    {
        $relations = self::getRelations($table);

        return isset($relations[$column]) ? $relations[$column] : null;
    }


    // ------------------------------------------- Array Access
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
           $this->values[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->values[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->values[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->values[$offset]) ? $this->values[$offset] : null;
    }

}

global $REX;
rex_xform_manager_table::$db_table_table = $REX['TABLE_PREFIX'].'xform_table';
rex_xform_manager_table::$db_field_table = $REX['TABLE_PREFIX'].'xform_field';
