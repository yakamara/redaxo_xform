<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_intfromto extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if ($this->params['send'] == '1') {
      $from = (int) $this->getElement(3);
      $to = (int) $this->getElement(4);

      foreach ($this->obj_array as $Object) {
        $value = $Object->getValue();
        $value_int = (int) $value;
        if ("$value" != "$value_int" || $value_int < $from || $value_int > $to) {
          $this->params['warning'][$Object->getId()] = $this->params['error_class'];
          $this->params['warning_messages'][$Object->getId()] = $this->getElement(5);
        }
      }
    }
  }

  function getDescription()
  {
    return 'intfromto -> prüft auf zahlengröße, größer from, kleiner to: validate|intfromto|label|from|to|warning_message';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'validate',
      'name' => 'intfromto',
      'values' => array(
        array( 'type' => 'getName',     'label' => 'Name' ),
        array( 'type' => 'text',        'label' => 'Von'),
        array( 'type' => 'text',        'label' => 'Bis'),
        array( 'type' => 'text',        'label' => 'Fehlermeldung'),
      ),
      'description' => 'Hiermit wird ein Name überprüft ob es zwischen zwei Zahlen ist',
    );
  }


}
