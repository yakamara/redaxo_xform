<?php

class rex_xform_jquery extends rex_xform_abstract
{

  function enterObject()
  {

    $out = $this->getElement(3);

    // alle labels ersetzen..
    foreach($this->obj as $o)
    {
      // echo "  ".$o->getName()."  ";
      $out = str_replace("###".$o->getName()."###",'el_'.$o->getId(),$out);
    }

    $this->params["form_output"][$this->getId()] = '
    <script type="text/javascript">
    '.$out.';
    </script>';

    return;

  }

  function getDescription()
  {
    /*
    jquery - jq1 - hidelabels - label - labels,labels,labels
    jquery - jq2 - maxlength - label
    */
    return "jquery -> Beispiel: text|label|Bezeichnung|defaultwert|[no_db]";
  }

  function getDefinitions()
  {
    return array(
            'type' => 'value',
            'name' => 'jquery',
            'values' => array(
              array( 'type' => 'name',	'label' => 'Feld' ),
              array( 'type' => 'select',	'label' => 'Welche Funktion ?', 'default' => '', 'definition' => '-=0;hidelabel=1;maxlength=1' ),
              array( 'type' => 'textarea','label' => 'Javascriptcode'),
                ),
            'description' => 'JQuery Hilfsfunktionen',
            'dbtype' => 'text'
            );

  }
}

?>