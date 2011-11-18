<?php

class rex_xform_manager_table {

	
	var $values = array();
	
	function __construct($values = array())
	{
		if(!is_array($values) || count($values) == 0)
		{
			return FALSE;
		}
		$this->values = $values;
		return TRUE;
	}
	
	function getTableName()
	{
		return $this->values["table_name"];
	}

	function getName()
	{
		return $this->values["name"];
	}
	
	function isValid()
	{
		if(count($this->values) > 5) return TRUE;
		else return FALSE;
	}
	
	function factory($values)
	{
		$a = new rex_xform_manager_table($values);
		return $a;
	}
	
	
	
	// -------------------------------------------------------------------------
	
	function getTables($f = array())
	{
		
		$where = array();
		if(count($f) >0)
		{
			foreach($f as $t)
			{
				if($where != "") 
					$where .= ' OR ';
				$where[] = '(table_name = "'.$t.'")';
			}
		}
		
		if(count($where) > 0)
		{
			$where = ' where '.implode(" OR ",$where);
		}else
		{
			$where = '';
		}
		
		$tb = rex_sql::factory();
		// $tb->debugsql = 1;
		$tb->setQuery('select * from rex_xform_table '.$where.' order by prio,name');

		$return = array();
		foreach($tb->getArray() as $t)
		{
			$return[$t["table_name"]] = rex_xform_manager_table::factory($t);
			if(!$return[$t["table_name"]]->isValid())
			{
				unset($return[$t["table_name"]]);
			}
		}

		return $return;
	
	}
	
	function getTablesAsArray($f = array())
	{
		$tables = rex_xform_manager_table::getTables($f);
		$return = array();
		if(count($tables)>0)
		{
			foreach($tables as $t) {
				$return[] = $t->getTableName();
			}
		}
		return $return;
	
	}
	
	
	
	// -------------------------------------------------------------------------
	
	function getMaximumPrio($table_name)
	{
		$sql = 'select max(prio) as prio from rex_xform_field where table_name="'.$table_name.'" order by prio';
		$gf = rex_sql::factory();
		// $gf->debugsql = 1;
		$gf->setQuery($sql);
		return $gf->getValue("prio");

	}
	
	function hasId($table_name)
	{
		global $REX;
		// $sql = 'show columns from '.$table_name;
		$sql = 'SELECT COLUMN_NAME, EXTRA, COLUMN_KEY, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.$table_name.'" and COLUMN_NAME="id" and EXTRA = "auto_increment" and COLUMN_KEY="PRI" and TABLE_SCHEMA="'.mysql_real_escape_string($REX['DB']['1']['NAME']).'"';
		$gf = rex_sql::factory();
		// $gf->debugsql = 1;
		$gf->setQuery($sql);
		
		if($gf->getRows()==1) return TRUE;
		else return FALSE;
	}
	
	function getXFormFields($table_name, $filter = array())
	{
		$add_sql = "";
		foreach($filter as $k => $v) {
			$add_sql = 'AND `'.mysql_real_escape_string($k).'`="'.mysql_real_escape_string($v).'"';
		}
	
		$sql = 'select * from rex_xform_field where table_name="'.$table_name.'" '.$add_sql.' order by prio';
		$gf = rex_sql::factory();
		// $gf->debugsql = 1;
		$gf->setQuery($sql);
		$ga = $gf->getArray();

		$c = array();
		foreach($ga as $v) {
			$c[$v["f1"]] = $v;
		}	
		return $c;
	
	}

	function getXFormFieldsByType($table_name,$type_id="value")
	{
		return rex_xform_manager_table::getXFormFields($table_name, array("type_id" => $type_id));
	
	}



	function getFields($table_name)
	{
		global $REX;
		$sql = 'show columns from '.$table_name;
		$sql = 'SELECT COLUMN_NAME, EXTRA, COLUMN_KEY, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.$table_name.'" and TABLE_SCHEMA="'.mysql_real_escape_string($REX['DB']['1']['NAME']).'"';
		
		$gf = rex_sql::factory();
		// $gf->debugsql = 1;
		$gf->setQuery($sql);
		$ga = $gf->getArray();

		$c = array();
		foreach($ga as $v)
		{
			$c[$v["COLUMN_NAME"]] = $v;
		}	
		unset($c["id"]);
		return $c;	
	
	}

	function getMissingFields($table_name)
	{
		$xfields = rex_xform_manager_table::getXFormFields($table_name);
		$rfields = rex_xform_manager_table::getFields($table_name);
		
		$c = array();
		foreach($rfields as $k => $v)
		{
			if(!array_key_exists($k,$xfields))
			{
				$c[$k] = $k;
			}
		}
		return $c;
	
	}
	
	
	
	

}