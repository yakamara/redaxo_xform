<?php

class rex_xform_select extends rex_xform_abstract
{

	function enterObject()
	{

		$multiple = FALSE;
		if($this->getElement(6)==1)
		$multiple = TRUE;

		$size = (int) $this->getElement(7);
		if($size < 1)
		  $size = 1;

		$SEL = new rex_select();
		$SEL->setId($this->getFieldId());
		if($multiple) {
			if($size == 1) {
				$size = 2;
			}
			$SEL->setName($this->getFieldName()."[]");
			$SEL->setSize($size);
			$SEL->setMultiple(1);
		}else {
			$SEL->setName($this->getFieldName());
			$SEL->setSize(1);
		}

		foreach (explode(',', $this->getElement(3)) as $v) {
			$teile = explode('=', $v);
			$wert = $teile[0];
			if (isset ($teile[1])) {
				$bezeichnung = $teile[1];

			}else {
				$bezeichnung = $teile[0];

			}
			$SEL->addOption(rex_translate($wert), $bezeichnung);
		}

		if (!$this->params["send"] && $this->getValue()=="" && $this->getElement(5) != ""){
			$this->setValue($this->getElement(5));
		}

		if(!is_array($this->getValue())) {
			$this->setValue(explode(",",$this->getValue()));
		}

		foreach($this->getValue() as $v) {
			$SEL->setSelected($v);
		}

		$this->setValue(implode(",",$this->getValue()));

		$wc = "";
		if (isset($this->params["warning"][$this->getId()])) {
			$wc = $this->params["warning"][$this->getId()];
		}

		$SEL->setStyle(' class="select '.$wc.'"');

		$this->params["form_output"][$this->getId()] = '
      <p class="formselect '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
      <label class="select '.$wc.'" for="'.$this->getFieldId().'" >'.rex_translate($this->getElement(2)).'</label>'. 
		$SEL->get().
      '</p>';

		$this->params["value_pool"]["email"][$this->getElement(1)] = $this->getValue();
		if ($this->getElement(4) != "no_db") $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();

	}

	function getDescription()
	{
		return "select -> Beispiel: select|gender|Geschlecht *|Frau=w,Herr=m|[no_db]|defaultwert|multiple=1";
	}

	function getDefinitions()
	{
		return array(
            'type' => 'value',
            'name' => 'select',
            'values' => array(
				array( 'type' => 'name',   'label' => 'Feld' ),
				array( 'type' => 'text',    'label' => 'Bezeichnung'),
				array( 'type' => 'text',    'label' => 'Selektdefinition, kommasepariert',   'example' => 'w=Frau,m=Herr'),
				array( 'type' => 'no_db',   'label' => 'Datenbank',          'default' => 1),
				array( 'type' => 'text',    'label' => 'Defaultwert'),
				array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
				array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
				),
            'description' => 'Ein Selektfeld mit festen Definitionen',
            'dbtype' => 'text'
            );

	}

}

?>