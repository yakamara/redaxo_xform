<?php

class rex_xform_mysql_datetime extends rex_xform_abstract
{

	function enterObject()
	{
		$this->setValue(date("Y-m-d H-i-s"));
		$this->params["value_pool"]["email"][$this->getElement(1)] = $this->getValue();
		if ($this->getElement(3) != "no_db") 
		  $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
	}
	
	function getDescription()
	{
		return "mysql_datetime -> Beispiel: mysql_datetime|status|[no_db]";
	}
}

?>