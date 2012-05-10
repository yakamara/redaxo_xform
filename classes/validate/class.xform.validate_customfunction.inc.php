<?php

class rex_xform_validate_customfunction extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if($this->params["send"]=="1")
    {

      $label = $this->getElement(2);
      $func = $this->getElement(3);
      $parameter = $this->getElement(4);

      foreach($this->obj_array as $Object)
      {
        if(function_exists($func))
        {
          if($func($label,$Object->getValue(),$parameter))
          {
            $this->params["warning"][$Object->getId()] = $this->params["error_class"];
            $this->params["warning_messages"][$Object->getId()] = $this->getElement(5);
          }
        }else
        {
          $this->params["warning"][$Object->getId()] = $this->params["error_class"];
          $this->params["warning_messages"][$Object->getId()] = 'ERROR: customfunction "'.$func.'" not found';
        }
      }
    }
  }

  function getDescription()
  {
    return "customfunction -> prüft über customfunc, beispiel: validate|customfunction|label|functionname|weitere_parameter|warning_message";
  }

  function getDefinitions()
  {
    return array(
            'type' => 'validate',
            'name' => 'customfunction',
            'values' => array(
              array( 'type' => 'select_name', 'label' => 'Name'),
              array( 'type' => 'text',	'label' => 'Name der Funktion' ),
              array( 'type' => 'text', 	'label' => 'Weitere Parameter'),
              array( 'type' => 'text', 	'label' => 'Fehlermeldung'),
            ),
            'description' => 'Mit eigener Funktion vergleichen',
      );

  }

}

?>