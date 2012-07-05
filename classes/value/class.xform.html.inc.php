<?php

class rex_xform_html extends rex_xform_abstract
{

  function enterObject() {
    $html = str_replace('###'.$this->getElement(1).'###',
                              $this->getValue(),
                              $this->getElement(2));
    $this->params["form_output"][$this->getId()] = $html;
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