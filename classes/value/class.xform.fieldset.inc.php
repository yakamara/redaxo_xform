<?php

class rex_xform_fieldset extends rex_xform_abstract
{

	function enterObject()
	{

		$class = '';
		if ($this->getElement(3) != "")
		{
			$class = ' class="'.$this->getElement(3).'" ';
		}

		$legend = "";
		if ($this->getElement(2) != "")
		{
			$legend = '<legend id="'.$this->getFieldId().'">' . $this->getElement(2) . '</legend>';
		}

		if($this->params["first_fieldset"])
		{
			$this->params["first_fieldset"] = false;
			$this->params["form_output"][$this->getId()] = $legend;

		}else
		{
			$this->params["form_output"][$this->getId()] = '</fieldset><fieldset'.$class.' id="'.$this->getHTMLId().'">'.$legend;

		}

	}

	function getDescription()
	{
		return "fieldset -> Beispiel: fieldset|label|Fieldsetbezeichnung|[class]";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'fieldset',
						'values' => array(
							array( 'type' => 'name',	'value' => '' ),
							array( 'type' => 'text',	'label' => 'Bezeichnung'),
						),
						'description' => 'hiermit kann man Bereiche in der Verwaltung erstellen.',
						'dbtype' => 'text'
					);
	}


}

?>