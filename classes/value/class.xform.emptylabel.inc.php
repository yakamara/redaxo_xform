<?php

class rex_xform_emptylabel extends rex_xform_abstract
{

  function enterObject()
  {



  }

  function getDescription()
  {
    return "text -> Beispiel: emptylabel|label|";
  }

  function getDefinitions()
  {
    return array(
            'type' => 'value',
            'name' => 'emptylabel',
            'values' => array(
                  array( 'type' => 'name',   'label' => 'Feld' ),
                ),
            'description' => 'Ein leeres Feld - unsichtbar im Formular',
            'dbtype' => 'text'
            );

  }
}

?>