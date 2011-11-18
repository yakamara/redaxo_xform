<?php

class rex_xform_timestamp extends rex_xform_abstract
{

	function enterObject()
	{
		$this->setValue(time());
		$this->params["value_pool"]["email"][$this->getElement(1)] = $this->getValue();
		if ($this->getElement(2) != "no_db") $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
	}
	
	function getDescription()
	{
		return "timestamp -> Beispiel: timestamp|status|[no_db]";
	}
}

?>