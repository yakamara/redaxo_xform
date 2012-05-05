<?php

class rex_xform_objparams extends rex_xform_abstract
{

	function init()
	{
		$this->params[trim($this->getElement(1))] = trim($this->getElement(2));
	}

	function enterObject()
	{	
	}
	
	function getDescription()
	{
		return "objparams -> Beispiel: objparams|key|newvalue|";
	}

}

?>