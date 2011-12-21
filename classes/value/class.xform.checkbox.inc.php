<?php

class rex_xform_checkbox extends rex_xform_abstract
{
	function enterObject()
  	{
	  	$v = 1;
	  	$checked = "";
	  	
	  	## set value attribute
	    if ($this->getElement(3) != '')
	    	$v = $this->getElement(3);
	    else
	   		$this->setElement(3,1);
	
	    ## is checkbox checked?
	    if($this->params["send"] != 1 && $this->getValue() === "" && $this->getElement(4) == $this->getElement(3))
	    	$checked = ' checked="checked"';
	    elseif($this->getValue() == $this->getElement(3))
	      	$checked = ' checked="checked"';
	    elseif($this->getValue() == 1)
	     	$checked = ' checked="checked"';
		else
	      	$this->setValue("0");
	
	    $wc = "";
	    if (isset($this->params["warning"][$this->getId()]))
	    	$wc = $this->params["warning"][$this->getId()];

	    $this->params["form_output"][$this->getId()] = '
			<p class="formcheckbox formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
				<input type="checkbox" class="checkbox '.$wc.'" name="'.$this->getFieldName().'" id="'.$this->getFieldId().'" value="'.$v.'" '.$checked.' />
				<label class="checkbox '.$wc.'" for="'.$this->getFieldId().'" >'.rex_translate($this->getElement(2)).'</label>
			</p>';

	    ## set values
	    $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
	    if ($this->getElement(5) != "no_db")
	    	$this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
  	}

	function getDescription()
  	{
		return "checkbox -> Beispiel: checkbox|label|Bezeichnung|Value|default (1/0)|[no_db]";
  	}

  	function getDefinitions()
  	{
    	return array(
            'type' => 'value',
            'name' => 'checkbox',
            'values' => array(
   		 		array( 'type' => 'name', 'label' => 'Name' ),
    			array( 'type' => 'text', 'label' => 'Bezeichnung'),
    			array( 'type' => 'text', 'label' => 'Wert wenn angeklickt', 'default' => 1),
    			array( 'type' => 'boolean', 'label' => 'Defaultstatus', 'default' => 1),
    			array( 'type' => 'no_db', 'label' => 'Datenbank', 'default' => 1),
    			),
            'description' => 'Eine Checkbox mit fester Definition.',
            'dbtype' => 'varchar(255)',
            'famous' => TRUE
    		);
  	}
}

?>