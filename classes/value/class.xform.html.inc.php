<?php

class rex_xform_html extends rex_xform_abstract
{

  function enterObject() {
    $this->params["form_output"][$this->getId()] = $this->getElement(2);
  }

  function getDescription() {
    return htmlspecialchars(stripslashes('html -> Beispiel: html|label|<div class="block">'));
  }

  function getDefinitions() {

    return array(
            'type' => 'value',
            'name' => 'html',
            'values' => array(
                  array( 'type' => 'name',   'label' => 'Feld' ),
                  array( 'type' => 'textarea',    'label' => 'HTML'),
                ),
            'description' => 'Nur für die Ausgabe gedacht',
            'dbtype' => 'text'
          );

  }

}

?>