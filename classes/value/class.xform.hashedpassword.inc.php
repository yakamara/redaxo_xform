<?php

class rex_xform_hashedpassword extends rex_xform_abstract
{

	function enterObject()
	{
		$classes = "";
		$classes .= " ".$this->getElement(6);
		$value = "";
		$value = $this->getValue();
		
		if(!is_array($value))
			$value = array('new' => '', 'current' => $this->getValue());
		
		$wc = "";
		if (isset($this->params["warning"][$this->getId()]))
			$wc = " ".$this->params["warning"][$this->getId()];

		$this->params["form_output"][$this->getId()] = '
			<p class="formtext formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'-0">
				<label class="text' . $wc . '" for="' . $this->getFieldId() . '-0" >Neues Passwort:</label>
				<input type="text" class="text'.$classes.$wc.'" name="'.$this->getFieldName().'[new]" id="'.$this->getFieldId().'-0" value="" />	
			</p>';
		
		if($this->getElement(4))
		{
			$this->params["form_output"][$this->getId()] .= '
			<p class="formtext formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'-1">
				<label class="text" for="' . $this->getFieldId() . '-1" >'.$this->getElement(2).' ['.$this->getElement(3).']</label>
				<input type="text" class="text'.$classes.'" id="'.$this->getFieldId().'-1" name="'.$this->getFieldName().'[current]" value="'.htmlspecialchars(stripslashes($value['current'])).'" readonly="readonly" />
			</p>';
		}

		## Unverschlüsselt in eMail schreiben
		$this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($value['new']);
		
		## Verschlüsselt in Datenbank schreiben
		if ($this->getElement(5) != "no_db")
		{
			if($value['new'] != '')
				$this->params["value_pool"]["sql"][$this->getElement(1)] = hash($this->getElement(3),$value['new']);
		}
	}

	function getDescription()
	{
		return "hashedpassword -> Beispiel: hashedpassword|label|Bezeichnung|(md5/sha1/sha512/...)|hashwert anzeigen (1/0)|[no_db]";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'hashedpassword',
						'values' => array(
									array( 'type' => 'name',    'label' => 'Feld' ),
									array( 'type' => 'text',    'label' => 'Bezeichnung'),
									array( 'type' => 'text',    'label' => 'Algorithmus'),
									array( 'type' => 'boolean', 'label' => 'Hashwert anzeigen', 'default' => 1),
									array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 1),
									array( 'type' => 'text',    'label' => 'classes'),
								),
						'description' => 'Ein verschlüsseltes Passwort',
						'dbtype' => 'text',
						'famous' => FALSE
						);
	}
}

?>