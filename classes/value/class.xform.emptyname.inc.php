<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_emptyname extends rex_xform_abstract
{

  function enterObject()
  {

  }

  function getDescription()
  {
    return 'emptyname -> Beispiel: emptyname|name|';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'emptyname',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Feld' ),
      ),
      'description' => 'Ein leeres Feld - unsichtbar im Formular',
      'dbtype' => 'text'
    );

  }
}
