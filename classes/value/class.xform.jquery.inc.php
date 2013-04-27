<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_jquery extends rex_xform_abstract
{

  function enterObject()
  {

    $out = $this->getElement(3);

    foreach ($this->obj as $o) {
      $out = str_replace('###' . $o->getName() . '###', $o->getFieldId(), $out);
    }

    $this->params['form_output'][$this->getId()] = '
    <script type="text/javascript">
    ' . $out . ';
    </script>';

    return;

  }

  function getDescription()
  {
    /*
    jquery - jq1 - hidelabels - label - labels,labels,labels
    jquery - jq2 - maxlength - label
    */
    return 'jquery -> Beispiel: text|name|label|defaultwert|[no_db]';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'jquery',
      'values' => array(
        array( 'type' => 'name',  'label' => 'Feld' ),
        array( 'type' => 'select',  'label' => 'Welche Funktion ?', 'default' => '', 'definition' => '-=0;hidelabel=1;maxlength=1' ),
        array( 'type' => 'textarea', 'label' => 'Javascriptcode'),
      ),
      'description' => 'JQuery Hilfsfunktionen',
      'dbtype' => 'text'
    );

  }
}
