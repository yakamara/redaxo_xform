<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_be_table extends rex_xform_abstract
{

  function preValidateAction()
  {
    $columns = (int) $this->getElement(3);
    if ($columns < 1) $columns = 1;
    $id = $this->getId();
    $values = array();
    if ($this->params['send']) {

      $i = 0;
      
      $search = array(",",";");
      $replace = array('‚','⁏'); // , -> alt-s 
      
      if (isset($_REQUEST['v'][$id])) {
          foreach ($_REQUEST['v'][$id] as $c) {
            for ($r = 0; $r < count($c); $r++) {
              if (!isset($values[$r])) $values[$r] = '';
              if ($i > 0) $values[$r] .= ',';
              if (isset($c[$r])) $values[$r] .= str_replace($search,$replace,trim($c[$r]));
            }
            $i++;
            // die nur den Trenner haben loeschen
            if (count($values) > 0) {
              foreach ($values as $key => $val) {
                if (trim($val) == ',')
                  unset($values[$key]);
              }
            }
          }
      }

      $this->setValue('');
      $i = 0;
      foreach ($values as $value) {
        if ($i > 0) $this->setValue($this->getValue() . ';');
        $v = explode(',', $value);
        $e = '';
        $j = 0;
        for ($r = 0; $r < $columns; $r++) {
          if ($j > 0) $e .= ',';
          $e .= $v[$r];
          $j++;
        }
        $this->setValue($this->getValue() . $e);
        $i++;
      }

    }
  
  }

  function enterObject()
  {

    global $I18N;

    $columns = (int) $this->getElement(3);
    if ($columns < 1) $columns = 1;

    $column_names = explode(',', $this->getElement(4));

    $id = $this->getId();

    // "1,1000,121;10,900,1212;100,800,1212;"

    $out_row_add = '';
    $out = '<script>

    function rex_xform_table_deleteRow' . $id . '(obj)
    {
      tr = obj.parent("td").parent("tr");
      tr.fadeOut("normal", function()
        {
          tr.remove();
        }
      );
    }

    function rex_xform_table_addRow' . $id . '(table)
    {

      jQuery(function($) { table.append(\'';

      $out .= '<tr>';
      for ($r = 0; $r < $columns; $r++) {
        $out .= '<td><input type="text" name="v[' . $id . '][' . $r . '][]" value="" /></td>';
      }
      $out .= '<td><a href="javascript:void(0)" onclick="rex_xform_table_deleteRow' . $id . '( jQuery(this) )">- '.$I18N->msg("delete").'</a></td>';
      $out .= '</tr>';

      $out .= '\');

          })

    }

    </script>';

    $values = explode(';', $this->getValue());

    /*
    if ($this->getValue() == '' && $this->params['send']) {
      $this->params['warning'][$this->getId()] = $this->params['error_class'];
    }
    */

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) $wc = $this->params['warning'][$this->getId()];

    $out_row_add .= '<a href="javascript:void(0);" onclick="rex_xform_table_addRow' . $id . '(jQuery(\'#xform_table' . $id . '\'))">+ '.$I18N->msg("add_row").'</a>';

    $out .= '<table class="rex-table rex-xform-be-table" id="xform_table' . $id . '"><tr>';
    for ($r = 0; $r < $columns; $r++) {
      $out .= '<th>';
      if (isset($column_names[$r]))
        $out .= $column_names[$r];
      $out .= '</th>';
    }
    $out .= '</tr>';


    foreach ($values as $value) {
      $v = explode(',', $value);

      $out .= '<tr>';
      for ($r = 0; $r < $columns; $r++) {
        $tmp = ''; if (isset($v[$r])) $tmp = $v[$r];
        $out .= '<td><input type="text" name="v[' . $id . '][' . $r . '][]" value="' . $tmp . '" /></td>';
      }
      $out .= '<td><a href="javascript:void(0)" onclick="rex_xform_table_deleteRow' . $id . '(jQuery(this))">- '.$I18N->msg("delete").'</a></td>';
      $out .= '</tr>';
    }
    $out .= '</table><br />';

    $this->params['form_output'][$this->getId()] = '
      <div class="xform-element ' . $this->getHTMLClass() . '" id="' . $this->getHTMLId() . '">
        <p class="formtable ' . $wc . '">
        <label class="table ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getElement(2) . '</label>
        ' . $out_row_add . '
        </p>
        ' . $out . '
      </div>';


    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(5) != 'no_db') {
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }
    return;

  }

  function getDescription()
  {
    return 'be_table -> Beispiel: be_table|name|label|Anzahl Spalten|Menge,Preis/Stück';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'be_table',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Name' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',    'label' => 'Anzahl Spalten'),
        array( 'type' => 'text',    'label' => 'Bezeichnung der Spalten (Menge,Preis,Irgendwas)'),
      ),
      'description' => 'Eine Tabelle mit Infos',
      'dbtype' => 'text'
    );
  }


}
