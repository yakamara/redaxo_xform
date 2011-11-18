<?php

class rex_xform_objparams extends rex_xform_abstract
{

	function init()
	{
		$vals = explode("#",trim($this->getElement(2)));
		if (count($vals)>1)
		{
			$this->params[trim($this->getElement(1))] = array();
			foreach($vals as $val)
			{
				$this->params[trim($this->getElement(1))][] = $val;
			}
		}else
		{
			$this->params[trim($this->getElement(1))] = trim($this->getElement(2));
		}
	}

	
	function enterObject()
	{	

	}
	
	
	function getDescription()
	{
		return "objparams -> Beispiel: objparams|key|newvalue";
	}

}

?>