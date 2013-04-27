<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_compare extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if ($this->params['send'] == '1') {
      $field_1 = $this->getElement(2);
      $field_2 = $this->getElement(3);
      foreach ($this->obj as $o) {
        if ($o->getName() == $field_1) {
          $id_1    = !isset($id_1)    ? $o->getId()    : $id_1;
          $value_1 = !isset($value_1) ? $o->getValue() : $value_1;
        }
        if ($o->getName() == $field_2) {
          $id_2    = !isset($id_2)    ? $o->getId()    : $id_2;
          $value_2 = !isset($value_2) ? $o->getValue() : $value_2;
        }
      }
      if ($value_1 != $value_2) {
        $this->params['warning'][$id_1] = $this->params['error_class'];
        $this->params['warning'][$id_2] = $this->params['error_class'];
        $this->params['warning_messages'][$id_1] = $this->getElement(4);
      }
    }
  }

  function getDescription()
  {
    return 'compare -> prüft ob leer, beispiel: validate|compare|label1|label2|warning_message ';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'validate',
      'name' => 'compare',
      'values' => array(
        array( 'type' => 'select_name', 'label' => 'Name der 1. Felder' ),
        array( 'type' => 'select_name', 'label' => 'Name der 1. Felder'),
        array( 'type' => 'text',   'label' => 'Fehlermeldung'),
      ),
      'description' => '2 Felder werden miteinander verglichen',
    );

  }
}
