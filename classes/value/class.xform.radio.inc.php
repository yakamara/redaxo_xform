<?php

// Dateiname: class.xform.radio.inc.php

class rex_xform_radio extends rex_xform_abstract
{
	
	function enterObject()
	{

		$SEL = new rex_radio();
		$SEL->setId($this->getHTMLId());
		
		$SEL->setName($this->getFieldName());


		$options = explode(";",$this->getElement(3));

		foreach($options as $option)
		{
			$t = explode("=",$option);
			$v = $t[0];
			$k = $t[1];
			$SEL->addOption($v, $k);
			$sqlnames[$k] = $t[0];
		}

		$wc = "";
		if (isset($this->params["warning"][$this->getId()])) 
			$wc = $this->params["warning"][$this->getId()];

		$SEL->setStyle(' class="select ' . $wc . '"');

		if ($this->getValue() == "" && $this->getElement(4) != "") 
			$this->setValue($this->getElement(4));

		if(!is_array($this->getValue()))
		{
			$this->setValue(explode(",",$this->getValue()));
		}

		foreach($this->getValue() as $v)
		{
			$SEL->setSelected($v);
		}
		
		$this->params["form_output"][$this->getId()] = '
			<p class="formradio formlabel-'.$this->getName().'"  id="'.$this->getHTMLId().'">
				<label class="radio ' . $wc . '" for="' . $this->getHTMLId() . '" >' . $this->getElement(2) . '</label>
			</p>'.$SEL->get();

		$this->setValue(implode(",",$this->getValue()));

		$this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
		if ($this->getElement(5) != "no_db") 
			$this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();

	}
	
	function getDescription()
	{
		return "radio -> Beispiel: radio|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert";
	}
}