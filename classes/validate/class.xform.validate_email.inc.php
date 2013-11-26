<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_email extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if ($this->params['send'] == '1')
      foreach ($this->obj_array as $Object) {
        if ($Object->getValue()) {
          if ( !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $Object->getValue()) ) {
            $this->params['warning'][$Object->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$Object->getId()] = $this->getElement(3);
          }
        }
      }
  }

  function getDescription()
  {
    return 'email -> prueft ob email korrekt ist. leere email ist auch korrekt, bitte zusaetzlich mit ifempty prfen, beispiel: validate|email|emaillabel|warning_message ';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'validate',
      'name' => 'email',
      'values' => array(
        array( 'type' => 'select_name',     'label' => 'Name' ),
        array( 'type' => 'text',      'label' => 'Fehlermeldung'),
      ),
      'description' => 'Hiermit wird ein Label überprüft ob es eine E-Mail ist',
    );

  }

}
