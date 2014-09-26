<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_manager_table
{

    var $values = array();

    function __construct($values = array())
    {
        if (!is_array($values) || count($values) == 0) {
            return false;
        }
        $this->values = $values;
        return true;
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
        $q = 'select * from ' . rex_xform_manager_table_api::$rex_xform_table . ' where table_name="' . $v . '" LIMIT 1';
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
        $sql = 'select max(prio) as prio from ' . rex_xform_manager_table_api::$rex_xform_table . '';
        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $gf->setQuery($sql);
        return $gf->getValue('prio');
    }

    static function getMaximumPrio($table_name)
    {
        global $REX;
        $sql = 'select max(prio) as prio from ' . rex_xform_manager_table_api::$rex_xform_field . ' where table_name="' . $table_name . '"';
        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $gf->setQuery($sql);
        return $gf->getValue('prio');

    }

    static function hasId($table_name)
    {
        global $REX;
        $sql = 'show fields from '.$table_name.' from '.mysql_real_escape_string($REX['DB']['1']['NAME']);
        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $fields = $gf->getArray($sql);
        foreach($fields as $field) {
            if ($field["Field"] == "id" && $field["Extra"] == "auto_increment") {
                return true;
            }
        }
        return false;
    }

    static function getXFormFields($table_name, $filter = array())
    {
        global $REX;
        $add_sql = '';
        foreach ($filter as $k => $v) {
            $add_sql = 'AND `' . mysql_real_escape_string($k) . '`="' . mysql_real_escape_string($v) . '"';
        }

        $sql = 'select * from ' . rex_xform_manager_table_api::$rex_xform_field . ' where table_name="' . $table_name . '" ' . $add_sql . ' order by prio';
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
        global $REX;
        $sql = 'show columns from ' . $table_name;
        $sql = 'SELECT COLUMN_NAME, EXTRA, COLUMN_KEY, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . $table_name . '" and TABLE_SCHEMA="' . mysql_real_escape_string($REX['DB']['1']['NAME']) . '"';

        $gf = rex_sql::factory();
        // $gf->debugsql = 1;
        $gf->setQuery($sql);
        $ga = $gf->getArray();

        $c = array();
        foreach ($ga as $v) {
            $c[$v['COLUMN_NAME']] = $v;
        }
        unset($c['id']);
        return $c;

    }

    static function getMissingFields($table_name)
    {
        $xfields = self::getXFormFieldsByType($table_name);
        $rfields = self::getFields($table_name);

        $c = array();
        foreach ($rfields as $k => $v) {
            if (!array_key_exists($k, $xfields)) {
                $c[$k] = $k;
            }
        }
        return $c;

    }

    public static function removeRelationTableRelicts($table)
    {
        global $REX;

        $sql = rex_sql::factory();
        $sql->setQuery('
            SELECT `table`, relation_table
            FROM ' . rex_xform_manager_table_api::$rex_xform_field . '
            WHERE table_name="' . $sql->escape($table) . '" AND type_id="value" AND type_name="be_manager_relation" AND relation_table != ""
        ');
        $deleteSql = rex_sql::factory();
        while ($sql->hasNext()) {
            $relationTableFields = self::getRelationTableFields($sql->getValue('relation_table'), $table, $sql->getValue('table'));
            if ($relationTableFields['source'] && $relationTableFields['target']) {
                $relationTable = $deleteSql->escape($sql->getValue('relation_table'));
                $deleteSql->setQuery('
                    DELETE FROM `' . $relationTable . '`
                    WHERE NOT EXISTS (SELECT * FROM `' . $table . '` WHERE id = ' . $relationTable . '.`' . $deleteSql->escape($relationTableFields['source']) . '`)
                ');
            }
            $sql->next();
        }
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
            $data = $sql->getArray('SELECT * FROM `' . rex_xform_manager_table_api::$rex_xform_field . '` WHERE type_id="value" AND type_name="be_manager_relation"');
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


}
