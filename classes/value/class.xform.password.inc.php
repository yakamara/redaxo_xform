<?php

class rex_xform_password extends rex_xform_abstract
{

	function enterObject()
	{		
		if ($this->getValue() == "" && !$this->params["send"])
		{
			$this->setValue($this->getElement(3));
		}

		$wc = "";
		if (isset($this->params["warning"][$this->getId()])){
			$wc = $this->params["warning"][$this->getId()];
		}
		
		$this->params["form_output"][$this->getId()] = '
				<p class="formpassword formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
				<label class="password ' . $wc . '" for="'.$this->getFieldId().'" >' . $this->getElement(2) . '</label>
				<input type="password" class="password ' . $wc . '" name="'.$this->getFieldName().'" id="'.$this->getFieldId().'" value="" />
				</p>';
		$this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
		if ($this->getElement(4) != "no_db") $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
	}
	
	function getDescription()
	{
		return "password -> Beispiel: password|psw|Passwort|default_value|[no_db]";
	}
}

?>